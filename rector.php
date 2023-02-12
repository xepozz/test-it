<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
    ]);
    $rectorConfig->skip([
        AddLiteralSeparatorToNumberRector::class,
        \Rector\Php81\Rector\Array_\FirstClassCallableRector::class=>[
            __DIR__.'/tests\Integration\ReturnTypeFunction\src\UserController.php'
        ]
    ]);
};
