<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

use Nette\PhpGenerator\Literal;

final class MixedValueGenerator implements ValueGeneratorInterface
{
    public function generate(): iterable
    {
        yield 0;
        yield 'value';
        yield new Literal('null');
    }
}