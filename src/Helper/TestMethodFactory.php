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
        $testMethod->setPublic();

        return $testMethod;
    }
}
