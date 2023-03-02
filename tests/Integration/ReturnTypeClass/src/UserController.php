<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeClass\src;

class UserController
{
    public function std(): object
    {
        return new \stdClass();
    }
    public function self(): object
    {
        return new self();
    }
}
