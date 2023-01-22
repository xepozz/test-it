<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

use PhpParser\Node;

class Context
{
    private int $currentNamespace = 0;
    private int $currentClass = 0;
    private int $currentMethod = 0;
    /**
     * @var Node\Stmt\Namespace_[]
     */
    private array $namespaces = [];
    public Node\Stmt\Namespace_ $namespace;
    public Node\Stmt\Class_ $class;
    public Node\Stmt\ClassMethod $method;

    public function __construct(
        public readonly string $sourceDirectory,
        public readonly string $targetDirectory,
    ) {
    }

    public function setClass(\PhpParser\Node\Stmt\Class_ $node): void
    {
        $this->namespaces[++$this->currentNamespace]
        ['classes'][++$this->currentClass]['class'] = $node;
        $this->class = $node;
    }

    public function setNamespace(\PhpParser\Node\Stmt\Namespace_ $node): void
    {
        $this->namespace = $node;
        $this->namespaces[++$this->currentNamespace]['namespace'] = $node;
        $this->namespace = $node;
    }

    public function setClassMethod(\PhpParser\Node\Stmt\ClassMethod $node): void
    {
        $this->namespaces[++$this->currentNamespace]
        ['classes'][++$this->currentClass]['class']
        ['methods'][++$this->currentMethod]['method'] = $node;
        $this->method = $node;
    }
}