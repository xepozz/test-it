<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConfigExcludeClasses;

use Xepozz\TestIt\Config;
use Xepozz\TestIt\Tests\Integration\AbstractTestCase;
use Xepozz\TestIt\Tests\Integration\ConfigExcludeClasses\src\UserController;

class IndexTest extends AbstractTestCase
{
    public function getTestDirectory(): string
    {
        return __DIR__;
    }

    protected function getSrcNamespace(): string
    {
        return __NAMESPACE__ . '\\src\\';
    }

    protected function getTestsNamespace(): string
    {
        return __NAMESPACE__ . '\\tests\\';
    }

    protected function getConfig(string $sourceDirectory, string $targetDirectory): Config
    {
        return (new Config())
            ->setSourceDirectory($sourceDirectory)
            ->setTargetDirectory($targetDirectory)
            ->excludeClasses([UserController::class]);
    }
}