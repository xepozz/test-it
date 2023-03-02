<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\MatrixIntersection;

class MatrixIntersectionTest extends TestCase
{
    private static function generator(array $array): \Generator
    {
        foreach ($array as $value) {
            yield $value;
        }
    }

    /**
     * @dataProvider dataProviderCalculate
     * @dataProvider dataProviderCalculateGenerator
     */
    public function testCalculate(array $expectedValue, iterable $arrays): void
    {
        $g = new MatrixIntersection();

        $actualValue = $g->intersect(...$arrays);

        $this->assertEquals($expectedValue, ($actualValue));
    }

    public static function dataProviderCalculateGenerator(): iterable
    {
        return [
            [
                [
                    [1],
                    [2],
                ],
                [
                    self::generator([1, 2]),
                ],
            ],
            [
                [
                    [1, 1],
                    [1, 2],
                    [2, 1],
                    [2, 2],
                ],
                [
                    self::generator([1, 2]),
                    self::generator([1, 2]),
                ],
            ],
            [
                [
                    [1, 2, 3],
                    [1, 2, 4],
                    [1, 3, 3],
                    [1, 3, 4],
                    [2, 2, 3],
                    [2, 2, 4],
                    [2, 3, 3],
                    [2, 3, 4],
                ],
                [
                    self::generator([1, 2]),
                    self::generator([2, 3]),
                    self::generator([3, 4]),
                ],
            ],
        ];
    }

    public static function dataProviderCalculate(): array
    {
        return [
            [
                [
                    [1],
                    [2],
                ],
                [
                    [1, 2],
                ],
            ],
            [
                [
                    [1, 1],
                    [1, 2],
                    [2, 1],
                    [2, 2],
                ],
                [
                    [1, 2],
                    [1, 2],
                ],
            ],
            [
                [
                    [1, 2, 3],
                    [1, 2, 4],
                    [1, 3, 3],
                    [1, 3, 4],
                    [2, 2, 3],
                    [2, 2, 4],
                    [2, 3, 3],
                    [2, 3, 4],
                ],
                [
                    [1, 2],
                    [2, 3],
                    [3, 4],
                ],
            ],
        ];
    }
}
