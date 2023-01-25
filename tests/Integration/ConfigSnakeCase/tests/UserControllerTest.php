<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigSnakeCase\tests;

use Xepozz\TestIt\Tests\Integration\ConfigSnakeCase\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function test_snake_case(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->snake_case();
    }


    public function test_camel_case(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->camelCase();
    }


    public function test_pascal_case(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->PascalCase();
    }


    public function test_lowered(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->__lowered__();
    }


    public function test_u_p_p_e_r_e_d(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->__UPPERED__();
    }


    public function test_c1_a2_3_4_a_абвёэй(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->C1__A2_3_4__a_абвёэй_();
    }


    /**
     * @dataProvider data_provider_sum
     */
    public function test_sum(int $expectedValue, mixed $aValue, mixed $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function data_provider_sum(): iterable
    {
        yield [0, 0, 0];
        yield [0, 0, null];
        yield [0, null, 0];
        yield [0, null, null];
    }


    /**
     * @dataProvider invalid_data_provider_sum
     */
    public function test_invalid_sum(string $expectedExceptionClass, mixed $aValue, mixed $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $this->expectException($expectedExceptionClass);
        $userController->sum($aValue, $bValue);
    }


    public static function invalid_data_provider_sum(): iterable
    {
        yield [\RuntimeException::class, 0, 'value'];
        yield [\RuntimeException::class, 'value', 0];
        yield [\RuntimeException::class, 'value', 'value'];
        yield [\RuntimeException::class, 'value', null];
        yield [\RuntimeException::class, null, 'value'];
    }
}
