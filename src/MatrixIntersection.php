<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

final class MatrixIntersection
{
    /**
     * @param \Iterator[] $generators
     * @return array[]
     */
    public function intersect(iterable ...$generators): array
    {
        $result = [];
        $currentGenerator = array_shift($generators);
        if (!$currentGenerator) {
            return [];
        }
        $generatorsValues = [];
        if ($generators) {
            foreach ($this->intersect(...$generators) as $innerValue) {
                $array = $innerValue instanceof \Traversable ? iterator_to_array($innerValue) : $innerValue;
                $generatorsValues[] = [...$array];
            }
        }
        foreach ($currentGenerator as $value) {
            if ($generatorsValues) {
                foreach ($generatorsValues as $generatorsValue) {
                    $result[] = [$value, ...$generatorsValue];
                }
            } else {
                $result[] = [$value];
            }
        }
        return $result;
    }
}