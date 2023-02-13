<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

use PhpParser\Node;
use Xepozz\TestIt\Config;

final class Context
{
    public ?Node\Stmt\Namespace_ $namespace = null;
    public ?Node\Stmt\Class_ $class = null;
    /**
     * @var Node\Stmt\Class_[]
     */
    public array $classes = [];
    public ?Node\Stmt\ClassMethod $method = null;
    public ?ClassContext $classContext = null;
    public array $attributes = [];

    public function __construct(
        public readonly Config $config,
    ) {
    }

    public function setClass(Node\Stmt\Class_ $node): void
    {
        $this->class = $node;
        $this->classContext = new ClassContext();
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

    public function setAttribute(string $attribute, $value): void
    {
        $this->attributes[$attribute] = $value;
    }

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function getAttribute(string $attribute): mixed
    {
        return $this->attributes[$attribute];
    }

    public function reset(): void
    {
        $this->namespace = null;
        $this->class = null;
        $this->classes = [];
        $this->method = null;
    }
}