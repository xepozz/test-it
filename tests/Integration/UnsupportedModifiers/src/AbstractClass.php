<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src;

abstract class AbstractClass
{
    public function get(): int
    {
        return 100;
    }

    abstract public function get2(): int;
}
