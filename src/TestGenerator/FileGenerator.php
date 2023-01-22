<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\PhpFile;

class FileGenerator
{
    private PhpFile $file;

    public function __construct()
    {
        $this->file = new PhpFile();
        $this->file->setStrictTypes();
    }

    public function generate(array $namespaces): PhpFile
    {
        foreach ($namespaces as $namespace) {
            $this->file->addNamespace($namespace);
        }
        return $this->file;
    }

}