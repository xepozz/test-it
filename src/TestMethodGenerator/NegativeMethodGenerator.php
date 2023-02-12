<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
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

        $testMethodName = 'testInvalid' . ucfirst($method->name->name);
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
            $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            $methodBodyBuilder->addAct("\$this->expectException(\$expectedExceptionClass);");
            $methodBodyBuilder->addAct("{$variableName}->{$method->name->name}($arguments);");
        }

        $dataProviderName = 'invalidDataProvider' . ucfirst($method->name->name);
        $invalidDataProvider = $this->dataProviderGenerator->generate($dataProviderName, $testMethod);

        $hasInvalidCases = false;

        foreach ($cases as $case) {
            $valuesToPrint = $this->phpEntitiesConverter->convert($case);
            try {
                $this->methodEvaluator->evaluate($context, $valuesToPrint);
            } catch (\Throwable $e) {
                $exceptionClass = new Literal('\\' . $e::class . '::class');
                $valuesToPrint = array_map($this->dumper->dump(...), [$exceptionClass, ...$case]);
                $case = implode(', ', $valuesToPrint);
                $invalidDataProvider->addBody("yield [{$case}];");
                $hasInvalidCases = true;
            }
        }
        $testMethod->addBody($methodBodyBuilder->build());

        if (!$hasInvalidCases) {
            return [];
        }
        return [$testMethod, $invalidDataProvider];
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