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


    public static function dataProviderDiff(): iterable
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


    public static function invalidDataProviderDiff(): iterable
    {
        yield [PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MIN, -1];
        yield [-1, PHP_INT_MIN];
        yield [1, PHP_INT_MAX];
        yield [PHP_INT_MAX, 1];
        yield [PHP_INT_MAX, PHP_INT_MAX];
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
        yield [PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MIN, -1];
        yield [-1, PHP_INT_MIN];
        yield [1, PHP_INT_MAX];
        yield [PHP_INT_MAX, 1];
        yield [PHP_INT_MAX, PHP_INT_MAX];
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


    public static function dataProviderAvg(): iterable
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


    public static function invalidDataProviderAvg(): iterable
    {
        yield [PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MIN, -1];
        yield [-1, PHP_INT_MIN];
        yield [1, PHP_INT_MAX];
        yield [PHP_INT_MAX, 1];
        yield [PHP_INT_MAX, PHP_INT_MAX];
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


    public static function dataProviderEqual(): iterable
    {
        yield [true, PHP_INT_MIN, PHP_INT_MIN];
        yield [false, PHP_INT_MIN, -1];
        yield [false, PHP_INT_MIN, 0];
        yield [false, PHP_INT_MIN, 1];
        yield [false, PHP_INT_MIN, PHP_INT_MAX];
        yield [false, -1, PHP_INT_MIN];
        yield [true, -1, -1];
        yield [false, -1, 0];
        yield [false, -1, 1];
        yield [false, -1, PHP_INT_MAX];
        yield [false, 0, PHP_INT_MIN];
        yield [false, 0, -1];
        yield [true, 0, 0];
        yield [false, 0, 1];
        yield [false, 0, PHP_INT_MAX];
        yield [false, 1, PHP_INT_MIN];
        yield [false, 1, -1];
        yield [false, 1, 0];
        yield [true, 1, 1];
        yield [false, 1, PHP_INT_MAX];
        yield [false, PHP_INT_MAX, PHP_INT_MIN];
        yield [false, PHP_INT_MAX, -1];
        yield [false, PHP_INT_MAX, 0];
        yield [false, PHP_INT_MAX, 1];
        yield [true, PHP_INT_MAX, PHP_INT_MAX];
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


    public static function dataProviderFirstGreater(): iterable
    {
        yield [false, PHP_INT_MIN, PHP_INT_MIN];
        yield [false, PHP_INT_MIN, -1];
        yield [false, PHP_INT_MIN, 0];
        yield [false, PHP_INT_MIN, 1];
        yield [false, PHP_INT_MIN, PHP_INT_MAX];
        yield [true, -1, PHP_INT_MIN];
        yield [false, -1, -1];
        yield [false, -1, 0];
        yield [false, -1, 1];
        yield [false, -1, PHP_INT_MAX];
        yield [true, 0, PHP_INT_MIN];
        yield [true, 0, -1];
        yield [false, 0, 0];
        yield [false, 0, 1];
        yield [false, 0, PHP_INT_MAX];
        yield [true, 1, PHP_INT_MIN];
        yield [true, 1, -1];
        yield [true, 1, 0];
        yield [false, 1, 1];
        yield [false, 1, PHP_INT_MAX];
        yield [true, PHP_INT_MAX, PHP_INT_MIN];
        yield [true, PHP_INT_MAX, -1];
        yield [true, PHP_INT_MAX, 0];
        yield [true, PHP_INT_MAX, 1];
        yield [false, PHP_INT_MAX, PHP_INT_MAX];
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


    public static function dataProviderSecondGreater(): iterable
    {
        yield [false, PHP_INT_MIN, PHP_INT_MIN];
        yield [true, PHP_INT_MIN, -1];
        yield [true, PHP_INT_MIN, 0];
        yield [true, PHP_INT_MIN, 1];
        yield [true, PHP_INT_MIN, PHP_INT_MAX];
        yield [false, -1, PHP_INT_MIN];
        yield [false, -1, -1];
        yield [true, -1, 0];
        yield [true, -1, 1];
        yield [true, -1, PHP_INT_MAX];
        yield [false, 0, PHP_INT_MIN];
        yield [false, 0, -1];
        yield [false, 0, 0];
        yield [true, 0, 1];
        yield [true, 0, PHP_INT_MAX];
        yield [false, 1, PHP_INT_MIN];
        yield [false, 1, -1];
        yield [false, 1, 0];
        yield [false, 1, 1];
        yield [true, 1, PHP_INT_MAX];
        yield [false, PHP_INT_MAX, PHP_INT_MIN];
        yield [false, PHP_INT_MAX, -1];
        yield [false, PHP_INT_MAX, 0];
        yield [false, PHP_INT_MAX, 1];
        yield [false, PHP_INT_MAX, PHP_INT_MAX];
    }
}
