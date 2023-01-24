<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\DataProviderInteger\tests\Controller;

use Xepozz\TestIt\Tests\Integration\DataProviderInteger\src\Controller\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testCalculate(): void
    {
        // arrange
        $expectedValue = 5;
        $userController = new UserController();

        // act
        $actualValue = $userController->calculate();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }
}
