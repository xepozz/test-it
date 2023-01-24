<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeFunction\src;

class UserController
{
    public function array(): callable
    {
        return [$this, 'long'];
    }

    public function bind(): callable
    {
        return $this->long(...);
    }

    public function long(): callable
    {
        return static function () {
        };
    }

    public function short(): callable
    {
        return static fn () => null;
    }

    public function closure(): callable
    {
        $value = 1;
        return static fn () => $value;
    }

    public function closureObject(): \Closure
    {
        return \Closure::fromCallable(fn () => null);
    }
}