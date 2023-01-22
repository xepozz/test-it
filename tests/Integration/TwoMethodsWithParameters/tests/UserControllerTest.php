<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\TwoMethodsWithParameters\tests;

use Xepozz\TestIt\Tests\Integration\TwoMethodsWithParameters\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderDiff
     */
    public function testDiff(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->diff($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderDiff(): array
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
     * @dataProvider invalidDataProviderDiff
     */
    public function testInvalidDiff(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->diff($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function invalidDataProviderDiff(): array
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


    /**
     * @dataProvider dataProviderAvg
     */
    public function testAvg(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->avg($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderAvg(): array
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
     * @dataProvider invalidDataProviderAvg
     */
    public function testInvalidAvg(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->avg($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function invalidDataProviderAvg(): array
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


    /**
     * @dataProvider dataProviderEqual
     */
    public function testEqual(bool $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->equal($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderEqual(): array
    {
        return [
            [true, PHP_INT_MIN, PHP_INT_MIN],
            [false, PHP_INT_MIN, -1],
            [false, PHP_INT_MIN, 0],
            [false, PHP_INT_MIN, 1],
            [false, PHP_INT_MIN, PHP_INT_MAX],
            [false, -1, PHP_INT_MIN],
            [true, -1, -1],
            [false, -1, 0],
            [false, -1, 1],
            [false, -1, PHP_INT_MAX],
            [false, 0, PHP_INT_MIN],
            [false, 0, -1],
            [true, 0, 0],
            [false, 0, 1],
            [false, 0, PHP_INT_MAX],
            [false, 1, PHP_INT_MIN],
            [false, 1, -1],
            [false, 1, 0],
            [true, 1, 1],
            [false, 1, PHP_INT_MAX],
            [false, PHP_INT_MAX, PHP_INT_MIN],
            [false, PHP_INT_MAX, -1],
            [false, PHP_INT_MAX, 0],
            [false, PHP_INT_MAX, 1],
            [true, PHP_INT_MAX, PHP_INT_MAX],
        ];
    }


    /**
     * @dataProvider dataProviderFirstGreater
     */
    public function testFirstGreater(bool $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->firstGreater($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderFirstGreater(): array
    {
        return [
            [false, PHP_INT_MIN, PHP_INT_MIN],
            [false, PHP_INT_MIN, -1],
            [false, PHP_INT_MIN, 0],
            [false, PHP_INT_MIN, 1],
            [false, PHP_INT_MIN, PHP_INT_MAX],
            [true, -1, PHP_INT_MIN],
            [false, -1, -1],
            [false, -1, 0],
            [false, -1, 1],
            [false, -1, PHP_INT_MAX],
            [true, 0, PHP_INT_MIN],
            [true, 0, -1],
            [false, 0, 0],
            [false, 0, 1],
            [false, 0, PHP_INT_MAX],
            [true, 1, PHP_INT_MIN],
            [true, 1, -1],
            [true, 1, 0],
            [false, 1, 1],
            [false, 1, PHP_INT_MAX],
            [true, PHP_INT_MAX, PHP_INT_MIN],
            [true, PHP_INT_MAX, -1],
            [true, PHP_INT_MAX, 0],
            [true, PHP_INT_MAX, 1],
            [false, PHP_INT_MAX, PHP_INT_MAX],
        ];
    }


    /**
     * @dataProvider dataProviderSecondGreater
     */
    public function testSecondGreater(bool $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->secondGreater($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderSecondGreater(): array
    {
        return [
            [false, PHP_INT_MIN, PHP_INT_MIN],
            [true, PHP_INT_MIN, -1],
            [true, PHP_INT_MIN, 0],
            [true, PHP_INT_MIN, 1],
            [true, PHP_INT_MIN, PHP_INT_MAX],
            [false, -1, PHP_INT_MIN],
            [false, -1, -1],
            [true, -1, 0],
            [true, -1, 1],
            [true, -1, PHP_INT_MAX],
            [false, 0, PHP_INT_MIN],
            [false, 0, -1],
            [false, 0, 0],
            [true, 0, 1],
            [true, 0, PHP_INT_MAX],
            [false, 1, PHP_INT_MIN],
            [false, 1, -1],
            [false, 1, 0],
            [false, 1, 1],
            [true, 1, PHP_INT_MAX],
            [false, PHP_INT_MAX, PHP_INT_MIN],
            [false, PHP_INT_MAX, -1],
            [false, PHP_INT_MAX, 0],
            [false, PHP_INT_MAX, 1],
            [false, PHP_INT_MAX, PHP_INT_MAX],
        ];
    }
}
