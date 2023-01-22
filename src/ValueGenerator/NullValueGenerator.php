<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

class NullValueGenerator implements ValueGeneratorInterface
{
    public function generate(): \Generator
    {
        yield null;
    }
}