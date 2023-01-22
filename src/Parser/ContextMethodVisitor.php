<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Parser;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Xepozz\TestIt\TestGenerator;

class ContextMethodVisitor extends NodeVisitorAbstract
{
    /**
     * @var PhpFile[]
     */
    public array $generated = [];
    /**
     * @var Method[]
     */
    public array $generatedMethods = [];
    /**
     * @var ClassType[]
     */
    public array $generatedClasses=[];
    /**
     * @var PhpNamespace[]
     */
    public array $generatedNamespaces=[];

    public function __construct(
        private readonly Context $context = new Context(),
    ) {
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->context->setNamespace($node);
        }
        if ($node instanceof Node\Stmt\Class_) {
            $this->context->setClass($node);
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($node->isMagic()) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            $this->context->setClassMethod($node);
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $generator = new TestGenerator\NamespaceGenerator();
            $this->generatedNamespaces[] = $generator->generate($this->context, $this->generatedClasses);
        }
        if ($node instanceof Node\Stmt\Class_) {
            $generator = new TestGenerator\ClassGenerator($node);
            $this->generatedClasses[] = $generator->generate($this->generatedMethods);
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($node->isMagic()) {
                return null;
            }
            $generator = new TestGenerator\MethodGenerator($node);
            array_push($this->generatedMethods, ...$generator->generate($this->context));
        }
        return null;
    }

    /**
     * @return PhpFile[]
     */
    public function dump(): array
    {
        $files = [];
        foreach ($this->generatedNamespaces as $namespace) {
            $fileGenerator = new TestGenerator\FileGenerator();
            $files[] = $fileGenerator->generate([$namespace]);
        }
        return $files;
    }
}