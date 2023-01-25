<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use PHPUnit\Framework\TestCase;
use Xepozz\TestIt\Parser\Context;

class ClassGenerator
{
    /**
     * @param Context $context
     * @param Method[] $methods
     * @return ClassType|null
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