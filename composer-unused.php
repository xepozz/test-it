<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;

return static function (Configuration $config): Configuration {
    return $config
        ->setAdditionalFilesFor('xepozz/test-it', ['test-it']);
};