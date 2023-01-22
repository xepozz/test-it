<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

class StringValueGenerator implements ValueGeneratorInterface
{
    public function generate(): \Generator
    {
        yield '';
        yield 'null';
        yield "\r";
        yield "\r\n";
        yield "\t";
        yield "\x00";
        yield "123";
        yield 'eval("exit(1);")';
    }
}