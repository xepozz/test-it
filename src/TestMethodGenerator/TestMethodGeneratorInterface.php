<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Parser\Context;

interface TestMethodGeneratorInterface
{
    /**
     * @return Method[]
     */
    public function generate(Context $context, array $cases): iterable;

    public function supports(Context $context, iterable $cases): bool;
}
