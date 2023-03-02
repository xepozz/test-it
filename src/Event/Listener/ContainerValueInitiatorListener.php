<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event\Listener;

use Xepozz\TestIt\Event\ClassGeneratedEvent;
use Xepozz\TestIt\Event\NamespaceGeneratedEvent;

final class ContainerValueInitiatorListener
{
    public function __invoke(NamespaceGeneratedEvent|ClassGeneratedEvent $event): void
    {
        if ($event instanceof ClassGeneratedEvent) {
            $class = $event->class;

            $traits = $event->context->classContext->traits;
            if ($traits !== []) {
                $event->context->setAttribute('ContainerAwareTrait', true);
                foreach ($traits as $trait) {
                    $class->addTrait($trait);
                }
            }
        } elseif ($event instanceof NamespaceGeneratedEvent) {
            $namespace = $event->namespace;

            $traits = $event->context->classContext->traits;
            if ($traits !== []) {
                $event->context->setAttribute('ContainerAwareTrait', true);
                foreach ($traits as $trait) {
                    $namespace->addUse($trait);
                }
            }
        }
    }
}
