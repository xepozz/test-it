<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\PhpEntitiesConverter;
use Xepozz\TestIt\TestGenerator\DataProviderGenerator;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\TypeSerializer;

final class PositiveMethodGenerator implements TestMethodGeneratorInterface
{
    private TypeSerializer $typeSerializer;
    private TypeNormalizer $typeNormalizer;
    private MethodEvaluator $methodEvaluator;
    private Dumper $dumper;
    private DataProviderGenerator $dataProviderGenerator;
    private TestMethodFactory $testMethodFactory;
    private PhpEntitiesConverter $phpEntitiesConverter;

    public function __construct()
    {
        $this->typeSerializer = new TypeSerializer();
        $this->typeNormalizer = new TypeNormalizer();
        $this->methodEvaluator = new MethodEvaluator();
        $this->dumper = new Dumper();
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

        $testMethodName = 'test' . ucfirst($method->name->name);
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

        $dataProviderName = 'dataProvider' . ucfirst($method->name->name);
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

    public function supports(Context $context, array $cases): bool
    {
        return true;
    }
}