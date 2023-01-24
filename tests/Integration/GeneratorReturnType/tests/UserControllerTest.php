<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\GeneratorReturnType\tests;

use Xepozz\TestIt\Tests\Integration\GeneratorReturnType\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderGenerator
     */
    public function testGenerator(\Generator $expectedValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->generator();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderGenerator(): iterable
    {
    }
}
