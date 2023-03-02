<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\Method;

final class DataProviderGenerator
{
    public function generate(
        string $name,
        Method $testMethod,
    ): Method {
        $testMethod->addComment("@dataProvider {$name}");

        $dataProvider = new Method($name);
        $dataProvider->setPublic();
        $dataProvider->setReturnType('iterable');
        $dataProvider->setStatic();

        return $dataProvider;
    }
}
