<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Nette\PhpGenerator\ClassType;
use Xepozz\TestIt\Parser\Context;

final readonly class ClassGeneratedEvent
{
    public function __construct(
        public Context $context,
        public ClassType $class,
    )
    {
    }
}