<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

use PhpParser\Node;
use Xepozz\TestIt\Config;

final class Context
{
    public ?Node\Stmt\Namespace_ $namespace = null;
    public null|Node\Stmt\Class_|Node\Stmt\Enum_ $class = null;
    /**
     * @var Node\Stmt\Class_[]|Node\Stmt\Enum_[]|null
     */
    public array $classes = [];
    public ?Node\Stmt\ClassMethod $method = null;

    public function __construct(
        public readonly Config $config,
    ) {
    }

    public function setClass(Node\Stmt\Class_|Node\Stmt\Enum_ $node): void
    {
        $this->class = $node;
        $this->classes[] = $node;
    }

    public function setNamespace(Node\Stmt\Namespace_ $node): void
    {
        $this->namespace = $node;
    }

    public function setClassMethod(Node\Stmt\ClassMethod $node): void
    {
        $this->method = $node;
    }

    public function reset(): void
    {
        $this->namespace = null;
        $this->class = null;
        $this->classes = [];
        $this->method = null;
    }
}