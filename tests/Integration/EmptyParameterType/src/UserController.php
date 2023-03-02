<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\EmptyParameterType\src;

class UserController
{
    public function inverse($value): bool
    {
        return !$value;
    }
}
