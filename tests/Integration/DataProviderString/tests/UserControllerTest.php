<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderString\tests;

use Xepozz\TestIt\Tests\Integration\DataProviderString\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderGenerate
     */
    public function testGenerate(string $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->generate();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderGenerate(): array
    {
        return [
        ];
    }
}
