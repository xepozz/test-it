<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;

final readonly class MethodEvaluator
{
    public function __construct(
        private ValueInitiatorInterface $valueInitiator
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
        $object = $this->valueInitiator->getObject($class);

        try {
            return $object->{$method->name->name}(...$arguments);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'An error occurred while trying to evaluate method',
                previous: $e,
            );
        }
    }
}