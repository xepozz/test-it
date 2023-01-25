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

final readonly class NegativeMethodGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private TypeSerializer $typeSerializer,
        private TypeNormalizer $typeNormalizer,
        private MethodEvaluator $methodEvaluator,
        private DataProviderGenerator $dataProviderGenerator,
        private TestMethodFactory $testMethodFactory,
        private PhpEntitiesConverter $phpEntitiesConverter,
        private Dumper $dumper,
        private MethodNameStrategy $methodNameStrategy,
    ) {
    }

    /**
     * @param Context $context
     * @param array $cases
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
        $variableName = '$' . lcfirst($class->name->name);

        $methodBodyBuilder = MethodBodyBuilder::create();
        $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        $methodBodyBuilder->addAct("\$this->expectException(\$expectedExceptionClass);");
        $methodBodyBuilder->addAct("{$variableName}->{$method->name->name}($arguments);");

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
                $valuesToPrint = array_map($this->dumper->dump(...), [$exceptionClass, ...$case]);
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
            } catch (\Throwable $e) {
                return true;
            }
        }

        return false;
    }
}