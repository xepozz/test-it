<?php

declare(strict_types=1);

namespace Xepozz\TestIt\NamingStrategy;

use Xepozz\TestIt\Parser\Context;
use Yiisoft\Strings\Inflector;

final class MethodNameStrategy
{
    public function __construct(
        private readonly Inflector $inflector,
    ) {
    }

    /**
     * @param Context $context
     * @param string[] $parts
     * @return string
     */
    public function generate(Context $context, array $parts): string
    {
        $methodName = implode('_', $parts);

        return match ($context->config->getMethodNamingStrategy()) {
            MethodNameStrategyEnum::CAMEL_CASE => $this->inflector->toCamelCase($methodName),
            MethodNameStrategyEnum::SNAKE_CASE => $this->inflector->toSnakeCase($methodName),
        };
    }
}