<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\ContextProvider;

final class TypeSerializer
{
    public function __construct(
        private readonly ContextProvider $contextProvider
    ) {
    }

    /**
     * @param string[] $types
     */
    public function serialize(array $types): string
    {
        if (count($types) === 2 && $types[0] === 'null') {
            unset($types[0]);
            return '?' . $this->serialize($types);
        }
        if (in_array('self', $types, true)) {
            $context = $this->contextProvider->getContext();
            $selfPosition = array_search('self', $types);
            $types[$selfPosition] = $context->class->name;
            return $this->serialize($types);
        }

        return implode('|', array_unique($types));
    }
}
