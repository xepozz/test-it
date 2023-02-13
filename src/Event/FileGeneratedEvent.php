<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\PhpFile;
use Xepozz\TestIt\Parser\Context;

final readonly class FileGeneratedEvent
{
    public function __construct(
        public Context $context,
        public PhpFile $file,
    )
    {
    }
}