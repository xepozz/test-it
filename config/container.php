<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Xepozz\TestIt\TestGenerator\MethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\ExactlyMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\NegativeMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\NoAssertionGenerator;
use Xepozz\TestIt\TestMethodGenerator\PositiveMethodGenerator;
use Xepozz\TestIt\ValueInitiator\AggregatedValueInitiator;
use Xepozz\TestIt\ValueInitiator\ContainerValueInitiator;
use Xepozz\TestIt\ValueInitiator\SimpleValueInitiator;
use Xepozz\TestIt\ValueInitiator\ValueInitiatorInterface;
use Yiisoft\Definitions\DynamicReferencesArray;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ListenerCollection;
use Yiisoft\EventDispatcher\Provider\Provider;

return [
    LoggerInterface::class => new NullLogger(),
    ValueInitiatorInterface::class => AggregatedValueInitiator::class,
    AggregatedValueInitiator::class => [
        '__construct()' => [
            'valueInitiators' => DynamicReferencesArray::from([
                SimpleValueInitiator::class,
                ContainerValueInitiator::class,
            ]),
        ],
    ],
    MethodGenerator::class => [
        '__construct()' => [
            'testMethodGenerators' => DynamicReferencesArray::from([
                NoAssertionGenerator::class,
                ExactlyMethodGenerator::class,
                PositiveMethodGenerator::class,
                NegativeMethodGenerator::class,
            ]),
        ],
    ],
    EventDispatcherInterface::class => Dispatcher::class,
    ListenerProviderInterface::class => Provider::class,
    ListenerCollection::class => function (ContainerInterface $container) {
        $collection = new ListenerCollection();
        $events = require __DIR__ . '/events.php';
        foreach ($events as $name => $listeners) {
            foreach ($listeners as $listener) {
                $collection = $collection->add($container->get($listener), $name);
            }
        }
        return $collection;
    },
];
