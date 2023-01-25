<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ReturnTypeFunction\tests;

use Xepozz\TestIt\Tests\Integration\ReturnTypeFunction\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testLong(): void
    {
        // arrange
        $expectedValue = static function () {
                };
        $userController = new UserController();

        // act
        $actualValue = $userController->long();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public function testShort(): void
    {
        // arrange
        $expectedValue = static fn () => null;
        $userController = new UserController();

        // act
        $actualValue = $userController->short();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }
}
