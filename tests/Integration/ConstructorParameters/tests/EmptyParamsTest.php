<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\ConstructorParameters\tests;

use Xepozz\TestIt\Tests\Integration\ConstructorParameters\src\EmptyParams;

final class EmptyParamsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderInverse
     */
    public function testInverse(bool $expectedValue, bool $valueValue): void
    {
        // arrange
        $emptyParams = new EmptyParams();

        // act
        $actualValue = $emptyParams->inverse($valueValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderInverse(): iterable
    {
        yield [false, true];
        yield [true, false];
    }
}
