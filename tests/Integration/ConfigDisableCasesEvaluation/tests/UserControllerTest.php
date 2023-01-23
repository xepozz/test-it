<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigDisableCasesEvaluation\tests;

use Xepozz\TestIt\Tests\Integration\ConfigDisableCasesEvaluation\src\UserController;

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


    public static function dataProviderInverse(): array
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false],
        ];
    }


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
            [PHP_INT_MIN, PHP_INT_MIN, PHP_INT_MIN],
            [PHP_INT_MIN, PHP_INT_MIN, -1],
            [PHP_INT_MIN, PHP_INT_MIN, 0],
            [PHP_INT_MIN, PHP_INT_MIN, 1],
            [PHP_INT_MIN, PHP_INT_MIN, PHP_INT_MAX],
            [PHP_INT_MIN, -1, PHP_INT_MIN],
            [PHP_INT_MIN, -1, -1],
            [PHP_INT_MIN, -1, 0],
            [PHP_INT_MIN, -1, 1],
            [PHP_INT_MIN, -1, PHP_INT_MAX],
            [PHP_INT_MIN, 0, PHP_INT_MIN],
            [PHP_INT_MIN, 0, -1],
            [PHP_INT_MIN, 0, 0],
            [PHP_INT_MIN, 0, 1],
            [PHP_INT_MIN, 0, PHP_INT_MAX],
            [PHP_INT_MIN, 1, PHP_INT_MIN],
            [PHP_INT_MIN, 1, -1],
            [PHP_INT_MIN, 1, 0],
            [PHP_INT_MIN, 1, 1],
            [PHP_INT_MIN, 1, PHP_INT_MAX],
            [PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MIN],
            [PHP_INT_MIN, PHP_INT_MAX, -1],
            [PHP_INT_MIN, PHP_INT_MAX, 0],
            [PHP_INT_MIN, PHP_INT_MAX, 1],
            [PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MAX],
            [-1, PHP_INT_MIN, PHP_INT_MIN],
            [-1, PHP_INT_MIN, -1],
            [-1, PHP_INT_MIN, 0],
            [-1, PHP_INT_MIN, 1],
            [-1, PHP_INT_MIN, PHP_INT_MAX],
            [-1, -1, PHP_INT_MIN],
            [-1, -1, -1],
            [-1, -1, 0],
            [-1, -1, 1],
            [-1, -1, PHP_INT_MAX],
            [-1, 0, PHP_INT_MIN],
            [-1, 0, -1],
            [-1, 0, 0],
            [-1, 0, 1],
            [-1, 0, PHP_INT_MAX],
            [-1, 1, PHP_INT_MIN],
            [-1, 1, -1],
            [-1, 1, 0],
            [-1, 1, 1],
            [-1, 1, PHP_INT_MAX],
            [-1, PHP_INT_MAX, PHP_INT_MIN],
            [-1, PHP_INT_MAX, -1],
            [-1, PHP_INT_MAX, 0],
            [-1, PHP_INT_MAX, 1],
            [-1, PHP_INT_MAX, PHP_INT_MAX],
            [0, PHP_INT_MIN, PHP_INT_MIN],
            [0, PHP_INT_MIN, -1],
            [0, PHP_INT_MIN, 0],
            [0, PHP_INT_MIN, 1],
            [0, PHP_INT_MIN, PHP_INT_MAX],
            [0, -1, PHP_INT_MIN],
            [0, -1, -1],
            [0, -1, 0],
            [0, -1, 1],
            [0, -1, PHP_INT_MAX],
            [0, 0, PHP_INT_MIN],
            [0, 0, -1],
            [0, 0, 0],
            [0, 0, 1],
            [0, 0, PHP_INT_MAX],
            [0, 1, PHP_INT_MIN],
            [0, 1, -1],
            [0, 1, 0],
            [0, 1, 1],
            [0, 1, PHP_INT_MAX],
            [0, PHP_INT_MAX, PHP_INT_MIN],
            [0, PHP_INT_MAX, -1],
            [0, PHP_INT_MAX, 0],
            [0, PHP_INT_MAX, 1],
            [0, PHP_INT_MAX, PHP_INT_MAX],
            [1, PHP_INT_MIN, PHP_INT_MIN],
            [1, PHP_INT_MIN, -1],
            [1, PHP_INT_MIN, 0],
            [1, PHP_INT_MIN, 1],
            [1, PHP_INT_MIN, PHP_INT_MAX],
            [1, -1, PHP_INT_MIN],
            [1, -1, -1],
            [1, -1, 0],
            [1, -1, 1],
            [1, -1, PHP_INT_MAX],
            [1, 0, PHP_INT_MIN],
            [1, 0, -1],
            [1, 0, 0],
            [1, 0, 1],
            [1, 0, PHP_INT_MAX],
            [1, 1, PHP_INT_MIN],
            [1, 1, -1],
            [1, 1, 0],
            [1, 1, 1],
            [1, 1, PHP_INT_MAX],
            [1, PHP_INT_MAX, PHP_INT_MIN],
            [1, PHP_INT_MAX, -1],
            [1, PHP_INT_MAX, 0],
            [1, PHP_INT_MAX, 1],
            [1, PHP_INT_MAX, PHP_INT_MAX],
            [PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MIN],
            [PHP_INT_MAX, PHP_INT_MIN, -1],
            [PHP_INT_MAX, PHP_INT_MIN, 0],
            [PHP_INT_MAX, PHP_INT_MIN, 1],
            [PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MAX],
            [PHP_INT_MAX, -1, PHP_INT_MIN],
            [PHP_INT_MAX, -1, -1],
            [PHP_INT_MAX, -1, 0],
            [PHP_INT_MAX, -1, 1],
            [PHP_INT_MAX, -1, PHP_INT_MAX],
            [PHP_INT_MAX, 0, PHP_INT_MIN],
            [PHP_INT_MAX, 0, -1],
            [PHP_INT_MAX, 0, 0],
            [PHP_INT_MAX, 0, 1],
            [PHP_INT_MAX, 0, PHP_INT_MAX],
            [PHP_INT_MAX, 1, PHP_INT_MIN],
            [PHP_INT_MAX, 1, -1],
            [PHP_INT_MAX, 1, 0],
            [PHP_INT_MAX, 1, 1],
            [PHP_INT_MAX, 1, PHP_INT_MAX],
            [PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MIN],
            [PHP_INT_MAX, PHP_INT_MAX, -1],
            [PHP_INT_MAX, PHP_INT_MAX, 0],
            [PHP_INT_MAX, PHP_INT_MAX, 1],
            [PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MAX],
        ];
    }
}
