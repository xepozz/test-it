<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeDateTime\src;

class UserController
{
    public function dateTime(): \DateTime
    {
        return new \DateTime('2020-01-01');
    }

    public function dateTimeImmutable(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2020-01-01');
    }
}