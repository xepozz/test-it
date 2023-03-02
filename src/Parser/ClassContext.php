<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

class ClassContext
{
    /**
     * @var class-string[]
     */
    public array $traits = [];

    /**
     * @param class-string $class
     * @return void
     */
    public function addTrait(string $class): void
    {
        $this->traits[$class] = $class;
    }
}