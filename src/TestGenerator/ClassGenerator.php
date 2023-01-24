<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpNamespace;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\TestCase;

class ClassGenerator
{
    private ClassType $class;

    public function __construct(Class_ $class)
    {
        $this->class = (new ClassType($class->name->name . 'Test'))
            ->setFinal()
            ->setExtends(TestCase::class);
    }

    /**
     * @param Method[] $methods
     * @return ClassType|null
     */
    public function generate(array $methods): ?ClassType
    {
        if ($methods === []) {
            return null;
        }
        $this->class->setMethods($methods);
       return $this->class;
    }
}