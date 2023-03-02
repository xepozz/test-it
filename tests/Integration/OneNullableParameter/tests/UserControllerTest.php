<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneNullableParameter\tests;

use Xepozz\TestIt\Tests\Integration\OneNullableParameter\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderInverse
     */
    public function testInverse(?bool $expectedValue, bool $valueValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->inverse($valueValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderInverse(): iterable
    {
        yield [null, true];
        yield [null, false];
        yield [true, true];
        yield [true, false];
        yield [false, true];
        yield [false, false];
    }
}
