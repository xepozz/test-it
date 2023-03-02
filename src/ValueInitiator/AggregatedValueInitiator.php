<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueInitiator;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use RuntimeException;

final class AggregatedValueInitiator implements ValueInitiatorInterface
{
    public function __construct(
        /**
         * @var ValueInitiatorInterface[] $valueInitiators
         */
        private readonly array $valueInitiators,
    ) {
    }

    public function getString(Class_|Enum_ $class): string
    {
        foreach ($this->valueInitiators as $valueInitiator) {
            if ($valueInitiator->supports($class)) {
                return $valueInitiator->getString($class);
            }
        }
        throw new RuntimeException(
            sprintf(
                'Could not find any value initiators for "%s" class.',
                $class->namespacedName,
            )
        );
    }

    public function getObject(Class_|Enum_ $class): object
    {
        foreach ($this->valueInitiators as $valueInitiator) {
            if ($valueInitiator->supports($class)) {
                return $valueInitiator->getObject($class);
            }
        }
        throw new RuntimeException(
            sprintf(
                'Could not find any value initiators for "%s" class.',
                $class->namespacedName,
            )
        );
    }

    public function generateArtifacts(Class_|Enum_ $class): void
    {
        foreach ($this->valueInitiators as $valueInitiator) {
            $valueInitiator->generateArtifacts($class);
        }
    }

    public function supports(Class_|Enum_ $class): bool
    {
        foreach ($this->valueInitiators as $valueInitiator) {
            if ($valueInitiator->supports($class)) {
                return true;
            }
        }
        return false;
    }
}
