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
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Xepozz\TestIt\Event\AfterGenerationEvent;
use Xepozz\TestIt\Event\ClassGeneratedEvent;
use Xepozz\TestIt\Event\FileGeneratedEvent;
use Xepozz\TestIt\Event\NamespaceGeneratedEvent;
use Xepozz\TestIt\TestGenerator\ClassGenerator;
use Xepozz\TestIt\TestGenerator\FileGenerator;
use Xepozz\TestIt\TestGenerator\MethodGenerator;
use Xepozz\TestIt\TestGenerator\NamespaceGenerator;

final class ContextMethodVisitor extends NodeVisitorAbstract implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
        LoggerInterface $logger,
        private readonly ContextProvider $contextProvider,
        private readonly FileGenerator $fileGenerator,
        private readonly NamespaceGenerator $namespaceGenerator,
        private readonly ClassGenerator $classGenerator,
        private readonly MethodGenerator $methodGenerator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
        $this->logger = $logger;
    }

    public function beforeTraverse(array $nodes)
    {
        $context = $this->contextProvider->getContext();
        $context->reset();
        ;

        $this->generatedMethods = [];
        $this->generatedClasses = [];
        $this->generatedNamespaces = [];
    }

    public function enterNode(Node $node): ?int
    {
        if ($node instanceof Node\Stmt\Trait_) {
            return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
        }

        $context = $this->contextProvider->getContext();

        if ($node instanceof Node\Stmt\Namespace_) {
            $this->generatedClasses = [];
            $context->setNamespace($node);
            return null;
        }
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Enum_) {
            $this->generatedMethods = [];
            if ($this->isClassExcluded($node)) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            $context->setClass($node);
            return null;
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->isMethodIgnored($node)) {
                return NodeTraverser::DONT_TRAVERSE_CURRENT_AND_CHILDREN;
            }
            $context->setClassMethod($node);
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }
        return null;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Trait_) {
            return null;
        }
        $context = $this->contextProvider->getContext();

        if ($node instanceof Node\Stmt\Namespace_) {
            $generated = $this->namespaceGenerator->generate($context, $this->generatedClasses);
            if ($generated !== null) {
                $this->eventDispatcher->dispatch(new NamespaceGeneratedEvent($context, $generated));
                $this->generatedNamespaces[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Enum_) {
            if ($this->isClassExcluded($node)) {
                return null;
            }
            $generated = $this->classGenerator->generate($context, $this->generatedMethods);
            if ($generated !== null) {
                $this->eventDispatcher->dispatch(new ClassGeneratedEvent($context, $generated));
                $this->generatedClasses[] = $generated;
            }
            return null;
        }
        if ($node instanceof Node\Stmt\ClassMethod) {
            if ($this->isMethodIgnored($node)) {
                return null;
            }
            $generated = $this->methodGenerator->generate($context);
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
            $generated = $this->fileGenerator->generate([$namespace]);
            $this->eventDispatcher->dispatch(new FileGeneratedEvent($this->contextProvider->getContext(), $generated));
            $files[] = $generated;
        }
        return $files;
    }

    public function afterTraverse(array $nodes)
    {
        $this->eventDispatcher->dispatch(new AfterGenerationEvent($this->contextProvider->getContext()));
        return parent::afterTraverse($nodes);
    }

    private function isClassExcluded(Node\Stmt\Class_|Node\Stmt\Enum_ $node): bool
    {
        if ($node instanceof Node\Stmt\Class_ && $node->isAbstract()) {
            return false;
        }
        $context = $this->contextProvider->getContext();

        return in_array($node->namespacedName->toString(), $context->config->getExcludedClasses());
    }

    private function isMethodIgnored(Node\Stmt\ClassMethod $node): bool
    {
        return $node->isMagic() || $node->isProtected() || $node->isPrivate();
    }
}
