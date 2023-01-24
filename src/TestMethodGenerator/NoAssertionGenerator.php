<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;

class NoAssertionGenerator implements TestMethodGeneratorInterface
{
    private TypeNormalizer $typeNormalizer;
    private TestMethodFactory $testMethodFactory;

    public function __construct()
    {
        $this->typeNormalizer = new TypeNormalizer();
        $this->testMethodFactory = new TestMethodFactory();
    }

    public function generate(Context $context, array $cases): iterable
    {
        $class = $context->class;
        $method = $context->method;

        $testMethodName = 'test' . ucfirst($method->name->name);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);
        $testMethod = $testMethod->cloneWithName($testMethodName);

        $variableName = '$' . lcfirst($class->name->name);
        $methodBodyBuilder = MethodBodyBuilder::create();
        $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        $methodBodyBuilder->addAssert("\$this->expectNotToPerformAssertions();");
        $methodBodyBuilder->addAssert("{$variableName}->{$method->name->name}();");

        $testMethod->addBody($methodBodyBuilder->build());

        return [$testMethod];
    }

    public function supports(Context $context, iterable $cases): bool
    {
        $method = $context->method;

        $possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());

        return $possibleReturnTypes === [];
    }
}