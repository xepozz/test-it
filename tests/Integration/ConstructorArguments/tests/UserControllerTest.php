<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorArguments\tests;

use Xepozz\TestIt\Tests\Integration\ConstructorArguments\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderGet
     */
    public function testGet(string $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->get();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderGet(): iterable
    {
    }
}
