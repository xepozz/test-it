<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

use Xepozz\TestIt\Parser\ContextProvider;

final class ValueGeneratorRepository
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
    }
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
            'self' => new ClassValueGenerator($this->contextProvider),
            default => null,
        };
    }
}
