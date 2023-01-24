<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

final class BooleanValueGenerator implements ValueGeneratorInterface
{
    public function generate(): iterable
    {
        yield true;
        yield false;
    }
}