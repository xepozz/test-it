<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use PhpParser\Node\Stmt\Namespace_;
use Xepozz\TestIt\Helper\PathFinder;
use Xepozz\TestIt\Parser\Context;

class NamespaceGenerator
{
    /**
     * @param ClassType[] $classes
     * @return PhpNamespace
     */
    public function generate(
        Context $context,
        array $classes,
    ): PhpNamespace {
        $currentNamespace = $context->namespace;
        $currentClass = $context->class;

        $newNamespace = PathFinder::translateNamespace((string) $currentNamespace->name, $context->targetDirectory);
        $phpNamespace = (new PhpNamespace($newNamespace));

        foreach ($classes as $class) {
            $phpNamespace->add($class)
                ->addUse((string)$currentClass->namespacedName);
        }
        return $phpNamespace;
    }
}