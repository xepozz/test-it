<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestMethodGenerator;

use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Parser\Context;

interface TestMethodGeneratorInterface
{
    /**
     * @param Context $context
     * @param array $cases
     * @return Method[]
     */
    public function generate(Context $context, array $cases): iterable;

    public function supports(Context $context, array $cases): bool;
}