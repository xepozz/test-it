<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event;

use Xepozz\TestIt\Parser\Context;

final class AfterGenerationEvent
{
    public function __construct(
        public readonly Context $context,
    ) {
    }
}
