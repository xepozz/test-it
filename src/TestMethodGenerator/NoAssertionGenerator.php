<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;

final readonly class NoAssertionGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private TypeNormalizer $typeNormalizer,
        private TestMethodFactory $testMethodFactory,
        private ValueInitiatorInterface $valueInitiator,
    ) {
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
        $classInitiation = $this->valueInitiator->getString($class);
        $methodBodyBuilder->addArrange("{$variableName} = {$classInitiation};");
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