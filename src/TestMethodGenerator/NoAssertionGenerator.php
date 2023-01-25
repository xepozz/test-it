<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\NamingStrategy\MethodNameStrategy;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;

final readonly class NoAssertionGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private TypeNormalizer $typeNormalizer,
        private TestMethodFactory $testMethodFactory,
        private MethodNameStrategy $methodNameStrategy,
    ) {
    }

    public function generate(Context $context, array $cases): iterable
    {
        $class = $context->class;
        $method = $context->method;

        $testMethodNameParts = ['test', $method->name->name];
        $testMethodName = $this->methodNameStrategy->generate($context, $testMethodNameParts);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);

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
        if ($possibleReturnTypes !== []) {
            return false;
        }

        return $method->getParams() === [];
    }
}