<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

final class ArrayValueGenerator implements ValueGeneratorInterface
{
    public function generate(): iterable
    {
        yield [];
        yield [[], []];
        yield [['value']];
        yield [[0]];
    }
}