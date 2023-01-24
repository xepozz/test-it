<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneParameter\tests;

use Xepozz\TestIt\Tests\Integration\OneParameter\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderInverse
     */
    public function testInverse(bool $expectedValue, bool $valueValue): void
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
        yield [false, true];
        yield [true, false];
    }
}
