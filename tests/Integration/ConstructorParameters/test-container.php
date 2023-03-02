<?php

declare(strict_types=1);

use Xepozz\TestIt\Tests\Integration\ConstructorParameters\src\StringParam;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

$config = ContainerConfig::create()
    ->withDefinitions([
        StringParam::class => [
            '__construct()' => [
                'host' => 'http://localhost',
            ],
        ],
    ]);

return new Container($config);