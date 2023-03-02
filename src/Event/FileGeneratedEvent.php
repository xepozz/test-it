<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\PhpFile;
use Xepozz\TestIt\Parser\Context;

final class FileGeneratedEvent
{
    public function __construct(
        public readonly Context $context,
        public readonly PhpFile $file,
    ) {
    }
}
