<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node\Stmt\Class_;
use Xepozz\TestIt\Parser\ContextProvider;

final readonly class ContainerValueInitiator implements ValueInitiatorInterface
{
    public function __construct(
        private ContextProvider $contextProvider,
    ) {
    }

    public function getString(Class_ $class): string
    {
        return "self::\$container->get({$class->name}::class)";
    }

    public function getObject(Class_ $class): object
    {
        return $this->contextProvider->getContext()->config->getContainer()->get((string) $class->namespacedName);
    }

    public function supports(Class_ $class): bool
    {
        return $this->contextProvider->getContext()->config->getContainer() !== null;
    }
}