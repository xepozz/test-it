<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src;

class PrivateMethod
{
    private function get(): void
    {
    }

    private static function stat(): int
    {
        return 100;
    }

    private function findDateRangeOverlap($a, $b, $c): void
    {
    }
}
