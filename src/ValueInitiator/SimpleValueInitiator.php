<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;

final class SimpleValueInitiator implements ValueInitiatorInterface
{
    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    public function getString(Class_ $class): string
    {
        return "new {$class->name->name}()";
    }

    public function getObject(Class_ $class): object
    {
        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);

        return $reflectionClass->newInstanceWithoutConstructor();
    }

    public function generateArtifacts(Class_ $class): void
    {
    }

    public function supports(Class_ $class): bool
    {
        try {
            new \ReflectionClass((string) $class->namespacedName);
        } catch (\ReflectionException) {
            return false;
        }

        /**
         * @var ClassMethod|null $constructor
         */
        $constructor = $this->nodeFinder->findFirst($class->stmts, function (Node $node) {
            return $node instanceof Node\Stmt\ClassMethod && $node->name->name === '__construct';
        });
        return $constructor === null || $constructor->params === [];
    }
}