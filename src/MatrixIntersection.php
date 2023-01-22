<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

class MatrixIntersection
{
    /**
     * @param \Iterator[] $generators
     * @return array
     */
    public function intersect(iterable ...$generators): iterable
    {
        $result = [];
        $currentGenerator = array_shift($generators);
        if (!$currentGenerator) {
            return [];
        }
        $generatorsValues = [];
        if ($generators) {
            foreach ($this->intersect(...$generators) as $innerValue) {
                $generatorsValues[] = [...iterator_to_array($innerValue)];
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