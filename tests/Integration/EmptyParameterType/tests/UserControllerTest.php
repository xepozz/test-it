<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\EmptyParameterType\tests;

use Xepozz\TestIt\Tests\Integration\EmptyParameterType\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderInverse
     */
    public function testInverse(bool $expectedValue, mixed $valueValue): void
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
            [true, 0],
            [false, 'value'],
            [true, null],
        ];
    }
}
