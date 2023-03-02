<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Nette\PhpGenerator\Dumper;

final class PhpEntitiesConverter
{
    private readonly Dumper $dumper;

    public function __construct()
    {
        $this->dumper = new Dumper();
    }

    public function convert(array $case): array
    {
        return array_map(
            fn ($code) => eval(sprintf('return %s;', $code)),
            array_map([$this->dumper, 'dump'], $case)
        );
    }
}
