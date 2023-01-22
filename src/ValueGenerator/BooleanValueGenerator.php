<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

class BooleanValueGenerator implements ValueGeneratorInterface
{
    public function generate(): \Generator
    {
        yield true;
        yield false;
    }
}