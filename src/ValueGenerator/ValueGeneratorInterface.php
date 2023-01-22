<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

interface ValueGeneratorInterface
{
    public function generate(): \Generator;
}