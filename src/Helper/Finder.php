<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Helper;

use SplFileInfo;
use Xepozz\TestIt\Config;

final class Finder
{
    /**
     * @param Config $config
     * @return SplFileInfo[]
     */
    public static function fromConfig(Config $config): iterable
    {
        $realpath = realpath($config->getSourceDirectory());

        $excludedDirs = str_replace($realpath . '/', '', $config->getExcludedDirectories());
        $excludedFiles = str_replace($realpath . '/', '', $config->getExcludedFiles());

        yield from (new \Symfony\Component\Finder\Finder())
            ->ignoreUnreadableDirs()
            ->in([$realpath, ...array_map(realpath(...), $config->getIncludedDirectories())])
            ->name('*.php')
            ->exclude($excludedDirs)
            ->notPath($excludedFiles)
            ->files()
            ->getIterator();
    }
}