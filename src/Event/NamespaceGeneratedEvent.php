<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\PhpNamespace;
use Xepozz\TestIt\Parser\Context;

final readonly class NamespaceGeneratedEvent
{
    public function __construct(
        public Context $context,
        public PhpNamespace $namespace,
    )
    {
    }
}