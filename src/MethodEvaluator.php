<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\Context;

class MethodEvaluator
{
    public function evaluate(Context $context, array $arguments)
    {
        $class = $context->class;
        $method = $context->method;

        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        return $object->{$method->name->name}(...$arguments);
    }
}