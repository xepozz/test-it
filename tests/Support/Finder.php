<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Support;

class Finder
{
    /**
     * @return \SplFileInfo[]
     */
    public static function getFiles(string $dir): array
    {
        return array_values(iterator_to_array(
            \Symfony\Component\Finder\Finder::create()
                ->in($dir)
                ->ignoreDotFiles(true)
                ->ignoreVCS(true)
                ->files()
                ->name('*.php')
                ->getIterator()
        ));
    }
}