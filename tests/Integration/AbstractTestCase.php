<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\TestGenerator;
use Xepozz\TestIt\Tests\Support\Finder;

abstract class AbstractTestCase extends TestCase
{
    public function testIntegration()
    {
        $classLoaders = ClassLoader::getRegisteredLoaders();
        $loader = current($classLoaders);
        $sourceDirectory = $this->getTestDirectory() . DIRECTORY_SEPARATOR . 'src';
        $targetDirectory = $this->getTestDirectory() . DIRECTORY_SEPARATOR . 'tests-ignore';
        $compareDirectory = $this->getTestDirectory() . DIRECTORY_SEPARATOR . 'tests';
        $loader->addPsr4($this->getSrcNamespace(), $sourceDirectory);
        $loader->addPsr4($this->getTestsNamespace(), $targetDirectory);

        $compareFiles = Finder::getFiles($compareDirectory);
        $nodeVisitor = new TestGenerator($sourceDirectory, $targetDirectory);

        $nodeVisitor->process();
        $resultFiles = Finder::getFiles($targetDirectory);

        $this->assertEquals(
            count($compareFiles),
            count($resultFiles),
        );
        foreach ($compareFiles as $index => $compareFile) {
            $resultFile = $resultFiles[$index];

            $this->assertEquals(
                file_get_contents($compareFile->getRealPath()),
                file_get_contents($resultFile->getRealPath()),
            );
        }
    }

    abstract protected function getTestDirectory(): string;

    abstract protected function getSrcNamespace(): string;

    abstract protected function getTestsNamespace(): string;
}