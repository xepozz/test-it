<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\EmptyFile;

use Xepozz\TestIt\Tests\Integration\AbstractTestCase;

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
}