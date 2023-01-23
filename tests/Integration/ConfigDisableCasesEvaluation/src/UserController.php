<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigDisableCasesEvaluation\src;

class UserController
{
    public function inverse(bool $value): bool
    {
        return !$value;
    }

    public function diff(int $a, int $b): int
    {
        return $a + $b;
    }
}