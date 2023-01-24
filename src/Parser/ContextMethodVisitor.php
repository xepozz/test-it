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
use Xepozz\TestIt\TestGenerator\ClassGenerator;
use Xepozz\TestIt\TestGenerator\FileGenerator;
use Xepozz\TestIt\TestGenerator\MethodGenerator;
use Xepozz\TestIt\TestGenerator\NamespaceGenerator;

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
    public array $generatedClasses = [];
    /**
     * @var PhpNamespace[]
     */
    public array $generatedNamespaces = [];

    public function __construct(
        private readonly Context $context,
    ) {
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->context->setNamespace($node);
            return null;
        }
        if ($node instanceof Node\Stmt\Class_) {
            if ($this->isClassExcluded($node)) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            $this->context->setClass($node);
            return null;
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->isMethodIgnored($node)) {
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
            $generator = new NamespaceGenerator();
            $generated = $generator->generate($this->context, $this->generatedClasses);
            if ($generated !== null) {
                $this->generatedNamespaces[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\Class_) {
            if ($this->isClassExcluded($node)) {
                return null;
            }
            $generator = new ClassGenerator($node);
            $generated = $generator->generate($this->generatedMethods);
            if ($generated !== null) {
                $this->generatedClasses[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->isMethodIgnored($node)) {
                return null;
            }
            $generator = new MethodGenerator($this->context);
            array_push($this->generatedMethods, ...$generator->generate($node));
            return null;
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
            $fileGenerator = new FileGenerator();
            $files[] = $fileGenerator->generate([$namespace]);
        }
        return $files;
    }

    private function isClassExcluded(Node\Stmt\Class_ $node): bool
    {
        return in_array($node->namespacedName->toString(), $this->context->config->getExcludedClasses());
    }

    private function isMethodIgnored(Node\Stmt\ClassMethod $node): bool
    {
        return $node->isMagic();
    }
}