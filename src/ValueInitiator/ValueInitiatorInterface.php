<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node\Stmt\Class_;

interface ValueInitiatorInterface
{
    public function getString(Class_ $class): string;

    public function getObject(Class_ $class): object;

    public function supports(Class_ $class): bool;
}