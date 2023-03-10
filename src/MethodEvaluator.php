<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;

final class MethodEvaluator
{
    public function __construct(
        private readonly ValueInitiatorInterface $valueInitiator
    ) {
    }

    public function evaluate(Context $context, array $arguments)
    {
        $class = $context->class;
        $method = $context->method;

        /**
         * A file may contain multiple both namespaces and classes.
         * It's impossible to find them with Reflection
         * TODO: make a workaround
         */

        try {
            if ($method->isStatic()) {
                return [$class->namespacedName->toString(), $method->name->toString()](...$arguments);
            }
            $object = $this->valueInitiator->getObject($class);
            return $object->{$method->name->name}(...$arguments);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'An error occurred while trying to evaluate method',
                previous: $e,
            );
        }
    }
}
