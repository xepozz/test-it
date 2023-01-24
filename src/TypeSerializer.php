<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

final class TypeSerializer
{
    /**
     * @param string[] $types
     * @return string
     */
    public function serialize(array $types): string
    {
        return implode('|', array_unique($types));
    }
}