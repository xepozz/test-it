<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

final class VoidValueGenerator implements ValueGeneratorInterface
{
    public function generate(): iterable
    {
        return [];
    }
}
