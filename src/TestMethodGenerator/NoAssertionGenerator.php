<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\NamingStrategy\MethodNameStrategy;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;

final class NoAssertionGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private readonly TypeNormalizer $typeNormalizer,
        private readonly TestMethodFactory $testMethodFactory,
        private readonly ValueInitiatorInterface $valueInitiator,
        private readonly MethodNameStrategy $methodNameStrategy,
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
        if ($this->valueInitiator->supports($class)) {
            $classInitiation = $this->valueInitiator->getString($class);
            $methodBodyBuilder->addArrange("{$variableName} = {$classInitiation};");
        } else {
            $methodBodyBuilder->addArrange("// TODO construct the object");
            $methodBodyBuilder->addArrange("{$variableName} = new {$class->name}();");
        }
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
