<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneMethodAndDataProvider\tests;

use Xepozz\TestIt\Tests\Integration\OneMethodAndDataProvider\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderHas
     */
    public function testHas(bool $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->has();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderHas(): array
    {
        return [
        ];
    }
}
