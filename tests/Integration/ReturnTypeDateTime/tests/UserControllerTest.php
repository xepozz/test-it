<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeDateTime\tests;

use Xepozz\TestIt\Tests\Integration\ReturnTypeDateTime\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testDateTime(): void
    {
        // arrange
        $expectedValue = new \DateTime('2020-01-01 00:00:00.000000', new \DateTimeZone('UTC'));
        $userController = new UserController();

        // act
        $actualValue = $userController->dateTime();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public function testDateTimeImmutable(): void
    {
        // arrange
        $expectedValue = new \DateTimeImmutable('2020-01-01 00:00:00.000000', new \DateTimeZone('UTC'));
        $userController = new UserController();

        // act
        $actualValue = $userController->dateTimeImmutable();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }
}
