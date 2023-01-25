<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

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