<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

final class TypeSerializer
{
    /**
     * @param string[] $types
     */
    public function serialize(array $types): string
    {
        if (count($types) === 2 && $types[0] === 'null') {
            unset($types[0]);
            return '?' . $types[1];
        }
        return implode('|', array_unique($types));
    }
}