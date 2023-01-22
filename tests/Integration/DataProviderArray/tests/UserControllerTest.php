<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderArray\tests;

use Xepozz\TestIt\Tests\Integration\DataProviderArray\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderGenerate
     */
    public function testGenerate(array $expectedValue, array $valueValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->generate($valueValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderGenerate(): array
    {
        return [
            [[], []],
            [[[], []], [[], []]],
            [[['value']], [['value']]],
            [[[0]], [[0]]],
        ];
    }


    /**
     * @dataProvider dataProviderArray
     */
    public function testArray(array $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->array();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderArray(): array
    {
        return [
        ];
    }
}
