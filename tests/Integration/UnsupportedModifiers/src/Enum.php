<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src;

enum Enum
{
    public function get(): int
    {
        return 100;
    }
}
