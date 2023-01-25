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

final class ContextMethodVisitor extends NodeVisitorAbstract
{
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
    private FileGenerator $fileGenerator;
    private NamespaceGenerator $namespaceGenerator;
    private ClassGenerator $classGenerator;
    private MethodGenerator $methodGenerator;

    public function __construct(
        private readonly Context $context,
    ) {
        $this->fileGenerator = new FileGenerator();
        $this->namespaceGenerator = new NamespaceGenerator();
        $this->classGenerator = new ClassGenerator();
        $this->methodGenerator = new MethodGenerator();
    }

    public function beforeTraverse(array $nodes)
    {
        $this->generatedMethods = [];
        $this->generatedClasses = [];
        $this->generatedNamespaces = [];
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
            $generated = $this->namespaceGenerator->generate($this->context, $this->generatedClasses);
            if ($generated !== null) {
                $this->generatedNamespaces[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\Class_) {
            if ($this->isClassExcluded($node)) {
                return null;
            }
            $generated = $this->classGenerator->generate($this->context, $this->generatedMethods);
            if ($generated !== null) {
                $this->generatedClasses[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->isMethodIgnored($node)) {
                return null;
            }
            $generated = $this->methodGenerator->generate($this->context);
            foreach ($generated as $testMethod) {
                $name = $testMethod->getName();
                if (isset($this->generatedMethods[$name])) {
                    throw new \Exception(
                        sprintf(
                            'Generated method with name "%s" was already generated.',
                            $name,
                        )
                    );
                }
                $this->generatedMethods[$name] = $testMethod;
            }
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
            $files[] = $this->fileGenerator->generate([$namespace]);
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