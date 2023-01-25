<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

use PhpParser\Node;
use Xepozz\TestIt\Config;

final class Context
{
    public ?Node\Stmt\Namespace_ $namespace = null;
    public ?Node\Stmt\Class_ $class = null;
    public ?Node\Stmt\ClassMethod $method = null;

    public function __construct(
        public readonly Config $config,
    ) {
    }

    public function setClass(Node\Stmt\Class_ $node): void
    {
        $this->class = $node;
    }

    public function setNamespace(Node\Stmt\Namespace_ $node): void
    {
        $this->namespace = $node;
    }

    public function setClassMethod(Node\Stmt\ClassMethod $node): void
    {
        $this->method = $node;
    }
}