<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

class ArrayValueGenerator implements ValueGeneratorInterface
{
    public function generate(): \Generator
    {
        yield [];
        yield [[], []];
        yield [['value']];
        yield [[0]];
    }
}