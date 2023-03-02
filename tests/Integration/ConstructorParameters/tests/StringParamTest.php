<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorParameters\tests;

use Xepozz\TestIt\Tests\Integration\ConstructorParameters\src\StringParam;
use Xepozz\TestIt\Tests\Integration\ConstructorParameters\tests\Support\ContainerAwareTrait;

final class StringParamTest extends \PHPUnit\Framework\TestCase
{
    use ContainerAwareTrait;

    /**
     * @dataProvider dataProviderEquals
     */
    public function testEquals(bool $expectedValue, string $valueValue): void
    {
        // arrange
        $stringParam = self::$container->get(StringParam::class);

        // act
        $actualValue = $stringParam->equals($valueValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderEquals(): iterable
    {
        yield [false, ''];
        yield [false, 'null'];
        yield [false, "\r"];
        yield [false, "\r\n"];
        yield [false, "\t"];
        yield [false, "\x00"];
        yield [false, '123'];
        yield [false, 'eval("exit(1);")'];
    }
}
