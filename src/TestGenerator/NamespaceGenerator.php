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
     * @param Context $context
     * @param ClassType[] $classes
     * @return PhpNamespace|null
     */
    public function generate(
        Context $context,
        array $classes,
    ): ?PhpNamespace {
        if ($classes === []) {
            return null;
        }
        $newNamespace = PathFinder::translateNamespace(
            (string) $context->namespace->name,
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