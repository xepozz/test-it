<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\Context;

final class MethodEvaluator
{
    public function evaluate(Context $context, array $arguments)
    {
        $class = $context->class;
        $method = $context->method;

        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        try {
            return $object->{$method->name->name}(...$arguments);
        } catch (\Throwable) {
            throw new \RuntimeException('An error occurred while trying to evaluate method');
        }
    }
}