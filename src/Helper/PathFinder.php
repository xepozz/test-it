<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Helper;

use Composer\Autoload\ClassLoader;

final class PathFinder
{
    private const NAMESPACE_SEPARATOR = '\\';

    public static function translateNamespace(string $fromNamespace, string $newPath): string
    {
        $newPath = self::normalizePath($newPath);

        $newNamespace = self::getNamespaceByPath($newPath);
        $baseNamespace = self::getBaseNamespace($fromNamespace);
        $additionalNamespace = str_replace($baseNamespace, '', $fromNamespace);

        return self::normalizeNamespace($newNamespace . $additionalNamespace);
    }

    public static function getNamespaceByPath(string $path): ?string
    {
        $path = self::normalizePath($path);
        $classLoaders = ClassLoader::getRegisteredLoaders();
        foreach ($classLoaders as $classLoader) {
            $dirs = $classLoader->getPrefixesPsr4();

            $dirs = array_map(fn ($dirs) => array_map(fn ($path) => realpath($path), $dirs), $dirs);
            [$foundNamespace, $foundPath] = self::getClosestWithKeyAndValue($path, $dirs);
            if ($foundNamespace !== null) {
                $foundPath = self::normalizePath($foundPath);
                if ($path === $foundPath) {
                    return self::normalizeNamespace($foundNamespace);
                }
                $relativePath = str_replace($foundPath, '', $path);
                $subNamespace = self::pathToNamespace($relativePath);
                return self::normalizeNamespace($foundNamespace . $subNamespace);
            }
        }

        return null;
    }

    public static function getPathByNamespace(string $namespace): ?string
    {
        $namespace = self::normalizeNamespace($namespace) . self::NAMESPACE_SEPARATOR;
        $classLoaders = ClassLoader::getRegisteredLoaders();
        foreach ($classLoaders as $classLoader) {
            $dirs = $classLoader->getPrefixesPsr4();

            $match = self::getClosest($namespace, array_keys($dirs));
            if ($match !== null) {
                $paths = array_map(fn (string $path) => realpath($path), $dirs[$match]);
                $current = self::normalizePath(current($paths));
                if ($namespace === $match) {
                    return $current;
                }
                $subDirectory = str_replace([$match, self::NAMESPACE_SEPARATOR], ['', DIRECTORY_SEPARATOR], $namespace);
                return self::normalizePath($current . $subDirectory);
            }
        }

        return null;
    }

    public static function getBaseNamespace(string $namespace): ?string
    {
        $namespace = self::normalizeNamespace($namespace) . self::NAMESPACE_SEPARATOR;
        $classLoaders = ClassLoader::getRegisteredLoaders();
        foreach ($classLoaders as $classLoader) {
            $dirs = $classLoader->getPrefixesPsr4();

            $match = self::getClosest($namespace, array_keys($dirs));
            if ($match !== null) {
                return self::normalizeNamespace($match);
            }
        }

        return null;
    }

    /**
     * @param string $search
     * @param string[] $strings
     * @return string|null
     */
    private static function getClosest(string $search, array $strings): ?string
    {
        if (in_array($search, $strings)) {
            return $search;
        }
        $shortest = -1;
        $closest = null;
        foreach ($strings as $string) {
            $len = strlen(str_replace($string, '', $search));

            if (str_starts_with($search, $string) && ($len <= $shortest || $shortest < 0)) {
                $closest = $string;
                $shortest = $len;
            }
        }

        return $closest;
    }

    /**
     * @param string $search
     * @param string[][] $array
     * @return array
     */
    private static function getClosestWithKeyAndValue(string $search, array $array): array
    {
        $shortest = -1;
        $closest = [null, null];
        foreach ($array as $key => $arrayOfStrings) {
            if (in_array($search, $arrayOfStrings)) {
                return [$key, $search];
            }
            foreach ($arrayOfStrings as $string) {
                $len = strlen(str_replace($string, '', $search));

                if (str_starts_with($search, $string) && ($len <= $shortest || $shortest < 0)) {
                    $closest = [$key, $string];
                    $shortest = $len;
                }
            }
        }

        return $closest;
    }

    private static function normalizeNamespace(string $namespace): string
    {
        $namespace = strtr($namespace, ['-' => '_']);
        return $namespace[-1] === self::NAMESPACE_SEPARATOR ? substr($namespace, 0, -1) : $namespace;
    }

    private static function normalizePath(string $path): string
    {
        $path = realpath($path);
        return $path[-1] === DIRECTORY_SEPARATOR ? $path : $path . DIRECTORY_SEPARATOR;
    }

    private static function pathToNamespace(string $path): string
    {
        return strtr($path, [DIRECTORY_SEPARATOR => self::NAMESPACE_SEPARATOR]);
    }
}