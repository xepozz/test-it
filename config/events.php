<?php

declare(strict_types=1);

use Xepozz\TestIt\Event\ClassGeneratedEvent;
use Xepozz\TestIt\Event\Listener\ContainerAwareTraitGenerationListener;
use Xepozz\TestIt\Event\Listener\ContainerValueInitiatorListener;

return [
    ClassGeneratedEvent::class => [
        ContainerValueInitiatorListener::class,
    ],
    \Xepozz\TestIt\Event\NamespaceGeneratedEvent::class => [
        ContainerValueInitiatorListener::class,
    ],
    \Xepozz\TestIt\Event\AfterGenerationEvent::class => [
        ContainerAwareTraitGenerationListener::class,
    ],
];