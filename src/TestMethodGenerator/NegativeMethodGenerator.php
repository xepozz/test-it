<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Literal;
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

final class NegativeMethodGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private readonly TypeSerializer $typeSerializer,
        private readonly TypeNormalizer $typeNormalizer,
        private readonly MethodEvaluator $methodEvaluator,
        private readonly DataProviderGenerator $dataProviderGenerator,
        private readonly TestMethodFactory $testMethodFactory,
        private readonly PhpEntitiesConverter $phpEntitiesConverter,
        private readonly Dumper $dumper,
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

        if (!$context->config->isCaseEvaluationEnabled()) {
            /**
             * No need to generate negative test because we can check if the result is not successful
             */
            return [];
        }

        $testMethodNameParts = ['test', 'invalid', $method->name->name];
        $testMethodName = $this->methodNameStrategy->generate($context, $testMethodNameParts);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);
        $testMethod
            ->addParameter('expectedExceptionClass')
            ->setType('string');

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
            $methodBodyBuilder->addAct("\$this->expectException(\$expectedExceptionClass);");
            $methodBodyBuilder->addAct("{$class->name->name}::{$method->name->name}($arguments);");
        } else {
            $variableName = '$' . lcfirst($class->name->name);
            if ($this->valueInitiator->supports($class)) {
                $classInitiation = $this->valueInitiator->getString($class);
                $methodBodyBuilder->addArrange("{$variableName} = {$classInitiation};");
            } else {
                $methodBodyBuilder->addArrange("// TODO construct the object");
                $methodBodyBuilder->addArrange("{$variableName} = new {$class->name}();");
            }
            $methodBodyBuilder->addAct("\$this->expectException(\$expectedExceptionClass);");
            $methodBodyBuilder->addAct("{$variableName}->{$method->name->name}($arguments);");
        }

        $dataProviderNameParts = ['invalid', 'data', 'provider', $method->name->name];
        $dataProviderName = $this->methodNameStrategy->generate($context, $dataProviderNameParts);
        $dataProvider = $this->dataProviderGenerator->generate($dataProviderName, $testMethod);

        $hasInvalidCases = false;

        foreach ($cases as $case) {
            $valuesToPrint = $this->phpEntitiesConverter->convert($case);
            try {
                $this->methodEvaluator->evaluate($context, $valuesToPrint);
            } catch (\Throwable $e) {
                $exceptionClass = new Literal('\\' . $e::class . '::class');
                $valuesToPrint = array_map([$this->dumper, 'dump'], [$exceptionClass, ...$case]);
                $case = implode(', ', $valuesToPrint);
                $dataProvider->addBody("yield [{$case}];");
                $hasInvalidCases = true;
            }
        }
        $testMethod->addBody($methodBodyBuilder->build());

        if (!$hasInvalidCases) {
            return [];
        }
        return [$testMethod, $dataProvider];
    }

    public function supports(Context $context, iterable $cases): bool
    {
        if (!$context->config->isCaseEvaluationEnabled()) {
            return false;
        }
        foreach ($cases as $case) {
            $valuesToPrint = $this->phpEntitiesConverter->convert($case);
            try {
                $this->methodEvaluator->evaluate($context, $valuesToPrint);
            } catch (\Throwable) {
                return true;
            }
        }

        return false;
    }
}
