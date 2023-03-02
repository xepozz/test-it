<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorParameters\tests\Support;

use Psr\Container\ContainerInterface;

trait ContainerAwareTrait
{
    private static ?ContainerInterface $container = null;


    protected function setUp(): void
    {
        $this->initializeContainer();
    }


    private function initializeContainer(): void
    {
        self::$container ??= (fn() => require_once __DIR__ . '/test-container.php')();
    }
}
