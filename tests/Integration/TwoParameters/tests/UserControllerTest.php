<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\TwoParameters\tests;

use Xepozz\TestIt\Tests\Integration\TwoParameters\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderSum
     */
    public function testSum(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderSum(): iterable
    {
        yield [-9223372036854775807-1, PHP_INT_MIN, 0];
        yield [-9223372036854775807, PHP_INT_MIN, 1];
        yield [-1, PHP_INT_MIN, PHP_INT_MAX];
        yield [-2, -1, -1];
        yield [-1, -1, 0];
        yield [0, -1, 1];
        yield [9223372036854775806, -1, PHP_INT_MAX];
        yield [-9223372036854775807-1, 0, PHP_INT_MIN];
        yield [-1, 0, -1];
        yield [0, 0, 0];
        yield [1, 0, 1];
        yield [9223372036854775807, 0, PHP_INT_MAX];
        yield [-9223372036854775807, 1, PHP_INT_MIN];
        yield [0, 1, -1];
        yield [1, 1, 0];
        yield [2, 1, 1];
        yield [-1, PHP_INT_MAX, PHP_INT_MIN];
        yield [9223372036854775806, PHP_INT_MAX, -1];
        yield [9223372036854775807, PHP_INT_MAX, 0];
    }


    /**
     * @dataProvider invalidDataProviderSum
     */
    public function testInvalidSum(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function invalidDataProviderSum(): iterable
    {
        yield [-9223372036854775808, -9223372036854775808];
        yield [-9223372036854775808, -1];
        yield [-1, -9223372036854775808];
        yield [1, 9223372036854775807];
        yield [9223372036854775807, 1];
        yield [9223372036854775807, 9223372036854775807];
    }
}
