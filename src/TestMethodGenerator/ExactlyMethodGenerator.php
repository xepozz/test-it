<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\MethodEvaluator;
use Xepozz\TestIt\NamingStrategy\MethodNameStrategy;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;
use Yiisoft\VarDumper\ClosureExporter;

final class ExactlyMethodGenerator implements TestMethodGeneratorInterface
{
    public function __construct(
        private readonly TypeNormalizer $typeNormalizer,
        private readonly MethodEvaluator $methodEvaluator,
        private readonly Dumper $dumper,
        private readonly TestMethodFactory $testMethodFactory,
        private readonly ClosureExporter $closureExporter,
        private readonly ValueInitiatorInterface $valueInitiator,
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

        $testMethodNameParts = ['test', $method->name->name];
        $testMethodName = $this->methodNameStrategy->generate($context, $testMethodNameParts);
        $testMethod = $this->testMethodFactory->create($testMethodName, $method);

        $variableName = '$' . lcfirst($class->name->name);

        $result = $this->methodEvaluator->evaluate($context, []);

        try {
            if ($this->isClosureAndSerializable($result)) {
                $value = $this->closureExporter->export($result);
            } else {
                $value = $this->dumper->dump($result);
            }
        } catch (\Throwable) {
            /**
             * Additional check
             */
            return [];
        }

        $methodBodyBuilder = MethodBodyBuilder::create();
        $methodBodyBuilder->addArrange("\$expectedValue = {$value};");
        if ($method->isStatic()) {
            $methodBodyBuilder->addAct("\$actualValue = {$class->name->name}::{$method->name->name}();");
        } else {
            $classInitiation = $this->valueInitiator->getString($class);
            $methodBodyBuilder->addArrange("{$variableName} = {$classInitiation};");
            $methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}();");
        }
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

        if (!$this->isSerializationSupported($possibleReturnTypes)) {
            return false;
        }

        try {
            $this->methodEvaluator->evaluate($context, []);
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    private function isSerializationSupported(array $possibleReturnTypes): bool
    {
        $impossibleTypes = [
            \Generator::class,
            'object',
        ];
        if (array_intersect($impossibleTypes, $possibleReturnTypes) !== []) {
            return false;
        }
        foreach ($possibleReturnTypes as $type) {
            if (class_exists($type)) {
                $reflection = new \ReflectionClass($type);
                if ($reflection->isUserDefined()) {
                    return false;
                }
                if ($reflection->isAbstract()) {
                    return false;
                }
                if ($reflection->isInternal() && !in_array($type, [\DateTime::class, \DateTimeImmutable::class,])) {
                    return false;
                }
            }
        }

        return true;
    }

    private function isClosureAndSerializable(mixed $result): bool
    {
        if (!is_callable($result)) {
            return false;
        }
        if (is_array($result)) {
            throw new \Exception('Array callable serialization is not unsupported.');
        }
        if (!$result instanceof \Closure) {
            return false;
        }
        $reflection = new \ReflectionFunction($result);
        if (!$reflection->isStatic()) {
            throw new \Exception('Non-static closure serialization is not unsupported.');
        }
        if ($reflection->getClosureUsedVariables() !== []) {
            throw new \Exception('Serialization of closure with bound variable is not supported.');
        }
        return true;
    }
}