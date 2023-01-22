<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\TwoMethodsWithParameters\src;

class UserController
{
    public function diff(int $a, int $b): int
    {
        return $a + $b;
    }

    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

    public function avg(int $a, int $b): int
    {
        return $a + $b;
    }

    public function equal(int $a, int $b): bool
    {
        return $a === $b;
    }

    public function firstGreater(int $a, int $b): bool
    {
        return $a > $b;
    }

    public function secondGreater(int $a, int $b): bool
    {
        return $a < $b;
    }
}