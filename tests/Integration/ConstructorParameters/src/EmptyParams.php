<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorParameters\src;

class EmptyParams
{
    public function __construct()
    {
    }

    public function inverse(bool $value): bool
    {
        return !$value;
    }
}
