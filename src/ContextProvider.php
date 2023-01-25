<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Xepozz\TestIt\Parser\Context;

final class ContextProvider
{
    private ?Context $context = null;

    public function getContext(): Context
    {
        if ($this->context === null) {
            throw new \Exception('Context is not set.');
        }
        return $this->context;
    }

    public function setContext(Context $context): void
    {
        $this->context = $context;
    }
}