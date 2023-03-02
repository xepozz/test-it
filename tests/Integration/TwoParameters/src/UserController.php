<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\TwoParameters\src;

class UserController
{
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }

    public function diff($a, $b): void
    {
    }
}
