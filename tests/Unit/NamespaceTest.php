<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\Helper\PathFinder;

class NamespaceTest extends TestCase
{
    /**
     * @dataProvider dataProviderNamespaces
     */
    public function testNamespaces(string $expectedPath, string $namespace)
    {
        $namespace = PathFinder::getPathByNamespace($namespace);

        $this->assertNotNull($namespace);
        $this->assertEquals($expectedPath, $namespace);
    }

    public static function dataProviderNamespaces(): array
    {
        $root = dirname(__DIR__, 2);

        return [
            [
                $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR,
                'Xepozz\\TestIt',
            ],
            [
                $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR,
                'Xepozz\\TestIt\\',
            ],
            [
                $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR,
                'Xepozz\\TestIt\\Helper',
            ],
            [
                dirname(__DIR__) . DIRECTORY_SEPARATOR,
                'Xepozz\\TestIt\\Tests\\',
            ],
            [
                __DIR__ . DIRECTORY_SEPARATOR,
                'Xepozz\\TestIt\\Tests\\Unit',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderPaths
     */
    public function testPaths(string $expectedNamespace, string $path)
    {
        $path = PathFinder::getNamespaceByPath($path);

        $this->assertNotNull($path);
        $this->assertEquals($expectedNamespace, $path);
    }

    public static function dataProviderPaths(): array
    {
        $root = dirname(__DIR__, 2);

        return [
            [
                'Xepozz\\TestIt',
                $root . DIRECTORY_SEPARATOR . 'src',
            ],
            [
                'Xepozz\\TestIt',
                $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR,
            ],
            [
                'Xepozz\\TestIt\\Helper',
                $root . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR,
            ],
            [
                'Xepozz\\TestIt\\Tests',
                dirname(__DIR__) . DIRECTORY_SEPARATOR,
            ],
            [
                'Xepozz\\TestIt\\Tests\\Unit',
                __DIR__ . DIRECTORY_SEPARATOR,
            ],
        ];
    }


    /**
     * @dataProvider dataProviderTranslateNamespace
     */
    public function testTranslateNamespace(string $expectedResult, string $fromNamespace, string $newPath): void
    {
        $actualResult = PathFinder::translateNamespace($fromNamespace, $newPath);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function dataProviderTranslateNamespace(): array
    {
        return [
            [
                'Xepozz\\TestIt\\Tests\\Helper',
                'Xepozz\\TestIt\\Helper',
                'tests',
            ],
            [
                'Xepozz\\TestIt\\Tests\\Helper',
                'Xepozz\\TestIt\\Helper\\',
                'tests',
            ],
            [
                'Xepozz\\TestIt\\Tests\\Helper',
                'Xepozz\\TestIt\\Tests\\Helper',
                'tests',
            ],
            [
                'Xepozz\TestIt\Tests\Integration\DataProviderInteger\tests',
                'Xepozz\TestIt\Tests\Integration\DataProviderInteger\src',
                dirname(__DIR__) . '/Integration/DataProviderInteger/tests/',
            ],
        ];
    }
}
