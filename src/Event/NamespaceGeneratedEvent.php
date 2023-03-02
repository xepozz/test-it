<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\PhpNamespace;
use Xepozz\TestIt\Parser\Context;

final class NamespaceGeneratedEvent
{
    public function __construct(
        public readonly Context $context,
        public readonly PhpNamespace $namespace,
    )
    {
    }
}