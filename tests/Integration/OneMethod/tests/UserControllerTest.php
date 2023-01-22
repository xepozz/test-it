<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneMethod\tests;

use Xepozz\TestIt\Tests\Integration\OneMethod\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    public function testVoid(): void
    {
        // arrange
        $userController = new UserController();


        // assert
        $this->expectNotToPerformAssertions();
        $userController->void();
    }
}
