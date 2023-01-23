<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigExcludeClasses\src;

class UserController
{
    public function __construct(private readonly string $value)
    {
    }

    public function get(): string
    {
        return $this->value;
    }
}