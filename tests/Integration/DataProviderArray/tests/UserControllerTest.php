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


    public static function dataProviderGenerate(): iterable
    {
        yield [[], []];
        yield [[[], []], [[], []]];
        yield [[['value']], [['value']]];
        yield [[[0]], [[0]]];
    }
}
