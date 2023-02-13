<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration;

use Composer\Autoload\ClassLoader;
use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\Config;
use Xepozz\TestIt\TestGenerator;
use Xepozz\TestIt\Tests\Support\Finder;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

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
        $compareFiles = Finder::getFiles($targetDirectory);
        foreach ($compareFiles as $file) {
            unlink($file->getRealPath());
        }

        $config = $this->getConfig($sourceDirectory, $targetDirectory);

        $definitions = require dirname(__DIR__, 2) . '/container.php';
        $containerConfig = ContainerConfig::create()
            ->withDefinitions($definitions);
        $container = new Container($containerConfig);

        $testGenerator = $container->get(TestGenerator::class);
        $testGenerator->process($config);

        $compareFiles = Finder::getFiles($compareDirectory, '*Test.php');
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

    protected function getConfig(string $sourceDirectory, string $targetDirectory): Config
    {
        return (new Config())
            ->setSourceDirectory($sourceDirectory)
            ->setTargetDirectory($targetDirectory);
    }
}