<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigSnakeCase\src;

class UserController
{
    public function snake_case(): void
    {
    }

    public function camelCase(): void
    {
    }

    public function PascalCase(): void
    {
    }

    public function __lowered__(): void
    {
    }

    public function __UPPERED__(): void
    {
    }

    public function C1__A2_3_4__a_абвёэй_(): void
    {
    }

    public function sum($a, $b): int
    {
        return $a + $b;
    }
}