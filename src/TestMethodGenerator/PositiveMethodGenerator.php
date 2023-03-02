<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
use Xepozz\TestIt\NamingStrategy\MethodNameStrategy;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\PhpEntitiesConverter;
use Xepozz\TestIt\TestGenerator\DataProviderGenerator;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\TypeSerializer;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;

final class PositiveMethodGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private readonly TypeSerializer $typeSerializer,
        private readonly TypeNormalizer $typeNormalizer,
        private readonly MethodEvaluator $methodEvaluator,
        private readonly Dumper $dumper,
        private readonly DataProviderGenerator $dataProviderGenerator,
        private readonly TestMethodFactory $testMethodFactory,
        private readonly PhpEntitiesConverter $phpEntitiesConverter,
        private readonly ValueInitiatorInterface $valueInitiator,
        private readonly MethodNameStrategy $methodNameStrategy,
    ) {
    }

    /**
     * @return Method[]
     */
    public function generate(Context $context, array $cases): array
    {
        $class = $context->class;
        $method = $context->method;
        $possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());

        $testMethodNameParts = ['test', $method->name->name];
        $testMethodName = $this->methodNameStrategy->generate($context, $testMethodNameParts);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);
        $testMethod
            ->addParameter('expectedValue')
            ->setType($this->typeSerializer->serialize($possibleReturnTypes));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->typeSerializer->serialize($this->typeNormalizer->denormalize($parameter->type)));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);

        $methodBodyBuilder = MethodBodyBuilder::create();
        if ($method->isStatic()) {
            $methodBodyBuilder->addAct("\$actualValue = {$class->name->name}::{$method->name->name}($arguments);");
        } else {
            $variableName = '$' . lcfirst($class->name->name);
            if ($this->valueInitiator->supports($class)) {
                $classInitiation = $this->valueInitiator->getString($class);
                $methodBodyBuilder->addArrange("{$variableName} = {$classInitiation};");
            } else {
                $methodBodyBuilder->addArrange("// TODO construct the object");
                $methodBodyBuilder->addArrange("{$variableName} = new {$class->name}();");
            }
            $methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        }
        $methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $dataProviderNameParts = ['data', 'provider', $method->name->name];
        $dataProviderName = $this->methodNameStrategy->generate($context, $dataProviderNameParts);
        $positiveDataProvider = $this->dataProviderGenerator->generate($dataProviderName, $testMethod);

        $hasCase = false;
        foreach ($cases as $case) {
            $valuesToPrint = $this->phpEntitiesConverter->convert($case);
            try {
                if ($context->config->isCaseEvaluationEnabled()) {
                    $result = $this->methodEvaluator->evaluate($context, $valuesToPrint);
                    $case = [$result, ...$case];
                }
            } catch (\Throwable) {
                continue;
            }
            $hasCase = true;
            $valuesToPrint = array_map($this->dumper->dump(...), $case);
            $case = implode(', ', $valuesToPrint);
            $positiveDataProvider->addBody("yield [{$case}];");
        }
        $testMethod->addBody($methodBodyBuilder->build());

        if (!$hasCase) {
            return [];
        }
        return [$testMethod, $positiveDataProvider];
    }

    public function supports(Context $context, iterable $cases): bool
    {
        return true;
    }
}
