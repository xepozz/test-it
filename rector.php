<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Set\ValueObject\DowngradeLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // define sets of rules
    $rectorConfig->sets([
        DowngradeLevelSetList::DOWN_TO_PHP_81,
    ]);
    $rectorConfig->skip([
        AddLiteralSeparatorToNumberRector::class,
        FirstClassCallableRector::class=>[
            __DIR__.'/tests\Integration\ReturnTypeFunction\src\UserController.php'
        ]
    ]);
};
