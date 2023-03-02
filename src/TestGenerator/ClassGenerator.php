<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\Parser\Context;

final class ClassGenerator
{
    /**
     * @param Method[] $methods
     */
    public function generate(Context $context, array $methods): ?ClassType
    {
        if ($methods === []) {
            return null;
        }
        return (new ClassType($context->class->name->name . 'Test'))
            ->setFinal()
            ->setExtends(TestCase::class)
            ->setMethods($methods);
    }
}