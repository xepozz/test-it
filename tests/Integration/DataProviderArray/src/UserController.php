<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderArray\src;

class UserController
{
    public function generate(array $value): array
    {
        return $value;
    }
    public function array(): array
    {
        return [
            'key' => 'value',
        ];
    }
}
