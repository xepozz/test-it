<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\tests;

use Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum;

final class EnumTest extends \PHPUnit\Framework\TestCase
{
    public function testOneValue(): void
    {
        // arrange
        $expectedValue = 'one';

        // act
        $actualValue = Enum::oneValue();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public function testOneName(): void
    {
        // arrange
        $expectedValue = 'ONE';

        // act
        $actualValue = Enum::oneName();

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    /**
     * @dataProvider dataProviderEqualsTo
     */
    public function testEqualsTo(bool $expectedValue, Enum $leftValue, Enum $rightValue): void
    {
        // act
        $actualValue = Enum::equalsTo($leftValue, $rightValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderEqualsTo(): iterable
    {
        yield [true, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE];
        yield [true, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::ONE];
        yield [false, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::TWO];
        yield [true, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE, \Xepozz\TestIt\Tests\Integration\UnsupportedModifiers\src\Enum::THREE];
    }
}
