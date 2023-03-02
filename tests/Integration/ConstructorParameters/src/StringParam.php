<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorParameters\src;

class StringParam
{
    public function __construct(private readonly string $host)
    {
    }

    public function equals(string $value): bool
    {
        return $value === $this->host;
    }
}