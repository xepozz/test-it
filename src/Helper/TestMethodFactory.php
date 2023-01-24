<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Helper;

use Nette\PhpGenerator\Method;
use PhpParser\Node;

final class TestMethodFactory
{
    public function create(string $name, Node\Stmt\ClassMethod $method): Method
    {
        $testMethod = new Method($name);
        $testMethod->setReturnType('void');

        if ($method->isFinal()) {
            $testMethod->setFinal();
        }
        if ($method->isAbstract()) {
            $testMethod->setAbstract();
        }
        if ($method->isStatic()) {
            $testMethod->setStatic();
        }
        if ($method->isProtected()) {
            $testMethod->setProtected();
        } elseif ($method->isPrivate()) {
            $testMethod->setPrivate();
        } else {
            $testMethod->setPublic();
        }
        return $testMethod;
    }
}