<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderInteger\tests;

use Xepozz\TestIt\Tests\Integration\DataProviderInteger\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderCalculate
     */
    public function testCalculate(int $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->calculate();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderCalculate(): array
    {
        return [
        ];
    }
}
