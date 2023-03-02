<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use Xepozz\TestIt\Helper\PathFinder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\Parser\ContextProvider;

final class ContainerValueInitiator implements ValueInitiatorInterface
{
    private const TRAIT_CLASS = 'ContainerAwareTrait';
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
    }

    public function getString(Class_|Enum_ $class): string
    {
        $context = $this->getContext();
        $traitNamespace = PathFinder::getNamespaceByPath(
            $context->config->getTargetDirectory() . '/Support',
        );
        $context->classContext->addTrait($traitNamespace . '\\' . self::TRAIT_CLASS);
        return "self::\$container->get({$class->name}::class)";
    }

    public function getObject(Class_|Enum_ $class): object
    {
        return $this->getContext()->config->getContainer()->get((string) $class->namespacedName);
    }

    public function generateArtifacts(Class_|Enum_ $class): void
    {
    }

    public function supports(Class_|Enum_ $class): bool
    {
        $container = $this->getContext()->config->getContainer();
        return $container !== null && $container->has((string) $class->namespacedName);
    }

    private function getContext(): Context
    {
        return $this->contextProvider->getContext();
    }
}
