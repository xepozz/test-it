<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\Config;
use Xepozz\TestIt\Helper\Finder;

class FinderTest extends TestCase
{
    /**
     * @dataProvider dataProviderFiles
     */
    public function testFromConfig(array $expectedFiles, Config $config)
    {
        $actualFiles = Finder::fromConfig($config);

        $this->assertIsIterable($actualFiles);
        $arrayOfFiles = iterator_to_array($actualFiles);
        $filePaths = array_keys($arrayOfFiles);

        sort($expectedFiles);
        sort($filePaths);
        $this->assertEquals($expectedFiles, $filePaths);
    }

    public function dataProviderFiles()
    {
        yield [
            [
                __DIR__ . '/Fixtures/Directory1/file1.php',
                __DIR__ . '/Fixtures/Directory1/file2.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file1.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file2.php',
                __DIR__ . '/Fixtures/directory2/file1.php',
                __DIR__ . '/Fixtures/directory2/file2.php',
                __DIR__ . '/Fixtures/index.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures'),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/Directory1/file1.php',
                __DIR__ . '/Fixtures/Directory1/file2.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file1.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file2.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures/Directory1'),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/directory2/file1.php',
                __DIR__ . '/Fixtures/directory2/file2.php',
                __DIR__ . '/Fixtures/index.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures')
                ->excludeDirectories([
                    __DIR__ . '/Fixtures/Directory1',
                ]),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/directory2/file1.php',
                __DIR__ . '/Fixtures/directory2/file2.php',
                __DIR__ . '/Fixtures/index.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures')
                ->excludeDirectories([
                    __DIR__ . '/Fixtures/Directory1/',
                ]),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/Directory1/file1.php',
                __DIR__ . '/Fixtures/Directory1/file2.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file1.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file2.php',
                __DIR__ . '/Fixtures/directory2/file1.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures')
                ->excludeFiles([
                    __DIR__ . '/Fixtures/index.php',
                    __DIR__ . '/Fixtures/directory2/file2.php',
                ]),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/directory2/file1.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__ . '/Fixtures')
                ->excludeDirectories([
                    __DIR__ . '/Fixtures/Directory1',
                ])
                ->excludeFiles([
                    __DIR__ . '/Fixtures/index.php',
                    __DIR__ . '/Fixtures/directory2/file2.php',
                ]),
        ];
        yield [
            [
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file1.php',
                __DIR__ . '/Fixtures/Directory1/Subdirectory/file2.php',
            ],
            (new Config())
                ->setSourceDirectory(__DIR__.'/Fixtures')
                ->excludeDirectories([
                    __DIR__ . '/Fixtures/Directory1',
                    __DIR__ . '/Fixtures/directory2',
                ])
                ->includeDirectories([
                    __DIR__ . '/Fixtures/Directory1/Subdirectory',
                ])
                ->excludeFiles([
                    __DIR__ . '/Fixtures/index.php',
                ]),
        ];
    }
}