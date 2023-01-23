<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
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
    ): ?PhpNamespace {
        $currentNamespace = $context->namespace;

        if ($classes === []) {
            return null;
        }
        $newNamespace = PathFinder::translateNamespace(
            (string) $currentNamespace->name,
            $context->config->getTargetDirectory()
        );
        $phpNamespace = (new PhpNamespace($newNamespace));

        foreach ($classes as $class) {
            $currentClass = $context->class;
            $phpNamespace->add($class)
                ->addUse((string) $currentClass->namespacedName);
        }
        return $phpNamespace;
    }
}