<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Xepozz\TestIt\Parser\Context;

final readonly class AfterGenerationEvent
{
    public function __construct(
        public Context $context,
    )
    {
    }
}