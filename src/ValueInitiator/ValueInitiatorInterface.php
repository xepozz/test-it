<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;

interface ValueInitiatorInterface
{
    public function getString(Class_|Enum_ $class): string;

    public function getObject(Class_|Enum_ $class): object;

    public function generateArtifacts(Class_|Enum_ $class): void;

    public function supports(Class_|Enum_ $class): bool;
}