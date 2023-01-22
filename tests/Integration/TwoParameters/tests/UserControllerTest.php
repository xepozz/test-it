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


    public static function dataProviderSum(): array
    {
        return [
            [-9223372036854775807-1, PHP_INT_MIN, 0],
            [-9223372036854775807, PHP_INT_MIN, 1],
            [-1, PHP_INT_MIN, PHP_INT_MAX],
            [-2, -1, -1],
            [-1, -1, 0],
            [0, -1, 1],
            [9223372036854775806, -1, PHP_INT_MAX],
            [-9223372036854775807-1, 0, PHP_INT_MIN],
            [-1, 0, -1],
            [0, 0, 0],
            [1, 0, 1],
            [9223372036854775807, 0, PHP_INT_MAX],
            [-9223372036854775807, 1, PHP_INT_MIN],
            [0, 1, -1],
            [1, 1, 0],
            [2, 1, 1],
            [-1, PHP_INT_MAX, PHP_INT_MIN],
            [9223372036854775806, PHP_INT_MAX, -1],
            [9223372036854775807, PHP_INT_MAX, 0],
        ];
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


    public static function invalidDataProviderSum(): array
    {
        return [
            [-9223372036854775808, -9223372036854775808],
            [-9223372036854775808, -1],
            [-1, -9223372036854775808],
            [1, 9223372036854775807],
            [9223372036854775807, 1],
            [9223372036854775807, 9223372036854775807],
        ];
    }
}
