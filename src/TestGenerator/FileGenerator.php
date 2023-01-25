<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\PhpFile;

class FileGenerator
{
    public function generate(array $namespaces): PhpFile
    {
        $file = new PhpFile();
        $file->setStrictTypes();

        foreach ($namespaces as $namespace) {
            $file->addNamespace($namespace);
        }
        return $file;
    }
}