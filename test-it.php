<?php

declare(strict_types=1);

use Xepozz\TestIt\Config;

return function (Config $config) {
    $config
        ->excludeFiles([
            'src/Config.php',
        ])
        ->excludeDirectories([
            'src/Helper',
            'src/Parser',
        ]);
};