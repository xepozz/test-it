<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\TwoParameters;

use Nette\PhpGenerator\Helpers;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Xepozz\TestIt\TestGenerator;

use Xepozz\TestIt\Tests\Integration\AbstractTestCase;
use Xepozz\TestIt\Tests\Support\Finder;

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