<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

final class TypeSerializer
{
    public function serialize(string|null|Identifier|Name|ComplexType $type): string
    {
        if (is_string($type)) {
            return $type;
        }
        if ($type === null) {
            return 'mixed';
        }
        return $type->toString();
    }
}