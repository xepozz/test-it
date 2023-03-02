<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

final class NullValueGenerator implements ValueGeneratorInterface
{
    public function generate(): iterable
    {
        yield null;
    }
}
