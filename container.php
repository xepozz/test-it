<?php

declare(strict_types=1);

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
];