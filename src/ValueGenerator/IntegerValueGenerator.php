<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

use Nette\PhpGenerator\Literal;

class IntegerValueGenerator implements ValueGeneratorInterface
{
    public function generate(): \Generator
    {
        yield new Literal('PHP_INT_MIN');
        yield -1;
        yield 0;
        yield 1;
        yield new Literal('PHP_INT_MAX');
    }
}