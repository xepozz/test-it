<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\ClassType;
use Xepozz\TestIt\Parser\Context;

final class ClassGeneratedEvent
{
    public function __construct(
        public readonly Context $context,
        public readonly ClassType $class,
    ) {
    }
}
