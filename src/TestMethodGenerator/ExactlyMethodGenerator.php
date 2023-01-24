<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;

final class ExactlyMethodGenerator implements TestMethodGeneratorInterface
{
    private TypeNormalizer $typeNormalizer;
    private MethodEvaluator $methodEvaluator;
    private Dumper $dumper;
    private TestMethodFactory $testMethodFactory;

    public function __construct()
    {
        $this->typeNormalizer = new TypeNormalizer();
        $this->methodEvaluator = new MethodEvaluator();
        $this->dumper = new Dumper();
        $this->testMethodFactory = new TestMethodFactory();
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

        $testMethodName = 'test' . ucfirst($method->name->name);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);

        $variableName = '$' . lcfirst($class->name->name);

        $result = $this->methodEvaluator->evaluate($context, []);

        $value = $this->dumper->dump($result);

        $methodBodyBuilder = MethodBodyBuilder::create();
        $methodBodyBuilder->addArrange("\$expectedValue = {$value};");
        $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        $methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}();");
        $methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $testMethod->addBody($methodBodyBuilder->build());

        return [$testMethod];
    }

    public function supports(Context $context, iterable $cases): bool
    {
        if (!$context->config->isCaseEvaluationEnabled()) {
            return false;
        }
        $method = $context->method;
        if ($method->getParams() !== []) {
            return false;
        }

        $possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());
        if ($possibleReturnTypes === []) {
            return false;
        }

        $impossibleTypes = [\Generator::class, 'callable', \Closure::class];
        if (array_intersect($impossibleTypes, $possibleReturnTypes) !== []) {
            return false;
        }

        try {
            $this->methodEvaluator->evaluate($context, []);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}