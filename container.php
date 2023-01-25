<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Xepozz\TestIt\TestGenerator\MethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\ExactlyMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\NegativeMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\NoAssertionGenerator;
use Xepozz\TestIt\TestMethodGenerator\PositiveMethodGenerator;
use Yiisoft\Definitions\DynamicReferencesArray;

return [
    LoggerInterface::class => new NullLogger(),
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