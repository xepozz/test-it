<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\ValueGenerator\ArrayValueGenerator;
use Xepozz\TestIt\ValueGenerator\BooleanValueGenerator;
use Xepozz\TestIt\ValueGenerator\IntegerValueGenerator;
use Xepozz\TestIt\ValueGenerator\MixedValueGenerator;
use Xepozz\TestIt\ValueGenerator\NullValueGenerator;
use Xepozz\TestIt\ValueGenerator\StringValueGenerator;
use Xepozz\TestIt\ValueGenerator\ValueGeneratorInterface;
use Xepozz\TestIt\ValueGenerator\VoidValueGenerator;

final class ValueGeneratorRepository
{
    public function getByType(mixed $possibleType): ?ValueGeneratorInterface
    {
        return match ($possibleType) {
            'array' => new ArrayValueGenerator(),
            'bool' => new BooleanValueGenerator(),
            'null' => new NullValueGenerator(),
            'string' => new StringValueGenerator(),
            'int' => new IntegerValueGenerator(),
            'mixed' => new MixedValueGenerator(),
            'void' => new VoidValueGenerator(),
            default => null,
        };
    }
}