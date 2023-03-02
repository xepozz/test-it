<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\NodeFinder;
use ReflectionClass;
use ReflectionException;

final class SimpleValueInitiator implements ValueInitiatorInterface
{
    public function __construct(
        private readonly NodeFinder $nodeFinder
    ) {
    }

    public function getString(Class_|Enum_ $class): string
    {
        return "new {$class->name->name}()";
    }

    public function getObject(Class_|Enum_ $class): object
    {
        $reflectionClass = new ReflectionClass((string) $class->namespacedName);

        return $reflectionClass->newInstanceWithoutConstructor();
    }

    public function generateArtifacts(Class_|Enum_ $class): void
    {
    }

    public function supports(Class_|Enum_ $class): bool
    {
        try {
            new ReflectionClass((string) $class->namespacedName);
        } catch (ReflectionException) {
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
