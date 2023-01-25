<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src;

class ProtectedMethod
{
    protected function get(): int
    {
        return 100;
    }

    protected static function stat(): int
    {
        return 100;
    }
}
