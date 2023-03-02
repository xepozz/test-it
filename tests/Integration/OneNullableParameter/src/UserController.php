<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneNullableParameter\src;

class UserController
{
    public function inverse(bool $value): ?bool
    {
        return !$value;
    }
}
