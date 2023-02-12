<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src;

enum Enum: string
{
    case ONE = 'one';
    case TWO = 'two';
    case THREE = 'three';

    public function get(): int
    {
        return 100;
    }

    public static function oneValue(): string
    {
        return self::ONE->value;
    }

    public static function oneName(): string
    {
        return self::ONE->name;
    }

    public static function equalsTo(self $left, self $right): bool
    {
        return $left === $right;
    }
}
