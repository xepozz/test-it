<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneMethodAndDataProvider\tests;

use Xepozz\TestIt\Tests\Integration\OneMethodAndDataProvider\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testHas(): void
    {
        // arrange
        $expectedValue = true;
        $userController = new UserController();

        // act
        $actualValue = $userController->has();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }
}
