<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

class MethodBodyBuilder
{
    private array $arranges = [];
    private array $acts = [];
    private array $asserts = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function addArrange(string $statement): void
    {
        $this->arranges[] = $statement;
    }

    public function addAct(string $statement): void
    {
        $this->acts[] = $statement;
    }

    public function addAssert(string $statement): void
    {
        $this->asserts[] = $statement;
    }

    public function build(): string
    {
        try {
            return implode("\n", [
                ...$this->arranges === [] ? [] : [
                    ...[
                        "// arrange",
                    ],
                    ...$this->arranges,
                ],
                '',
                ...$this->acts === [] ? [] : [
                    ...[
                        "// act",
                    ],
                    ...$this->acts,
                ],
                '',
                ...$this->asserts === [] ? [] : [
                    ...[
                        "// assert",
                    ],
                    ...$this->asserts,
                ],
            ]);
        } finally {
            $this->arranges = [];
            $this->acts = [];
            $this->asserts = [];
        }
    }
}