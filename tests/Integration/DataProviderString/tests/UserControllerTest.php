<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderString\tests;

use Xepozz\TestIt\Tests\Integration\DataProviderString\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testGenerate(): void
    {
        // arrange
        $expectedValue = '___';
        $userController = new UserController();

        // act
        $actualValue = $userController->generate();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }
}
