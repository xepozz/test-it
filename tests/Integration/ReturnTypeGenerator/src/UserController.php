<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeGenerator\src;

class UserController
{
    public function generator(): \Generator
    {
        yield 1;
        yield 'str';
    }
}
