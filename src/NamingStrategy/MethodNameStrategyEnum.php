<?php

declare(strict_types=1);

namespace Xepozz\TestIt\NamingStrategy;

enum MethodNameStrategyEnum
{
    /**
     * Example: testGetRepository
     */
    case CAMEL_CASE;
    /**
     * Example: test_get_repository
     */
    case SNAKE_CASE;
}
