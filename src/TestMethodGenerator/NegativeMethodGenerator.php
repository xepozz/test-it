<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\PhpEntitiesConverter;
use Xepozz\TestIt\TestGenerator\DataProviderGenerator;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\TypeSerializer;

final class NegativeMethodGenerator implements TestMethodGeneratorInterface
{
    private TypeSerializer $typeSerializer;
    private TypeNormalizer $typeNormalizer;
    private MethodEvaluator $methodEvaluator;
    private DataProviderGenerator $dataProviderGenerator;
    private TestMethodFactory $testMethodFactory;
    private PhpEntitiesConverter $phpEntitiesConverter;

    public function __construct()
    {
        $this->typeSerializer = new TypeSerializer();
        $this->typeNormalizer = new TypeNormalizer();
        $this->methodEvaluator = new MethodEvaluator();
        $this->dataProviderGenerator = new DataProviderGenerator();
        $this->testMethodFactory = new TestMethodFactory();
        $this->phpEntitiesConverter = new PhpEntitiesConverter();
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
        $possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());

        if (!$context->config->isCaseEvaluationEnabled()) {
            /**
             * No need to generate negative test because we can check if the result is not successful
             */
            return [];
        }

        $testMethodName = 'testInvalid' . ucfirst($method->name->name);
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
        $variableName = '$' . lcfirst($class->name->name);

        $methodBodyBuilder = MethodBodyBuilder::create();
        $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        $methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $dataProviderName = 'invalidDataProvider' . ucfirst($method->name->name);
        $invalidDataProvider = $this->dataProviderGenerator->generate($dataProviderName, $testMethod);

        $hasInvalidCases = false;

        foreach ($cases as $case) {
            $valuesToPrint = $this->phpEntitiesConverter->convert($case);
            try {
                $this->methodEvaluator->evaluate($context, $valuesToPrint);
            } catch (\Throwable $e) {
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

    public function supports(Context $context, array $cases): bool
    {
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