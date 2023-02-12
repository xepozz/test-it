<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\UnionType;

final class TypeNormalizer
{
    public function denormalize(string|null|Identifier|Name|ComplexType $type): array
    {
        if (is_string($type)) {
            return [$type];
        }

        if ($type === null) {
            return ['mixed'];
        }
        $result = [];

        if ($type instanceof NullableType) {
            return ['null', ...$this->denormalize($type->type)];
        } elseif ($type instanceof Identifier || $type instanceof FullyQualified) {
            $stringableType = $type->toString();
            if ($stringableType !== 'void') {
                $result[] = $stringableType;
            }
        } elseif ($type instanceof UnionType || $type instanceof IntersectionType) {
            foreach ($type->types as $type) {
                $result = [...$result, ...$this->denormalize($type)];
            }
        } elseif ($type instanceof Name) {
            return [$type->toString()];
        }
        return $result;
    }
}