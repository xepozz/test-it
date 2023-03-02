<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Event\Listener;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Psr\Container\ContainerInterface;
use Xepozz\TestIt\Event\AfterGenerationEvent;
use Xepozz\TestIt\Helper\PathFinder;
use Yiisoft\VarDumper\ClosureExporter;

final class ContainerAwareTraitGenerationListener
{
    public function __invoke(AfterGenerationEvent $event): void
    {
        $context = $event->context;
        if (!$context->hasAttribute('ContainerAwareTrait') || $context->getAttribute('ContainerAwareTrait') !== true) {
            return;
        }
        $directory = $context->config->getTargetDirectory() . '/Support';
        $traitNamespace = PathFinder::getNamespaceByPath($directory);

        $traitName = 'ContainerAwareTrait';
        $trait = new ClassType($traitName);
        $trait->setType(ClassType::TYPE_TRAIT);
        $property = $trait->addProperty('container');
        $property->setPrivate()
            ->setType(ContainerInterface::class)
            ->setValue(null)
            ->setStatic()
        ->setNullable();

        $setUpMethod = $trait->addMethod('setUp');
        $setUpMethod->setProtected()
            ->setReturnType('void')
            ->addBody(
                <<<PHP
\$this->initializeContainer();
PHP
            );
        $stringableInitiator = (new ClosureExporter())->export($context->config->getContainerFactory());
        $initializeContainerMethod = $trait->addMethod('initializeContainer');
        $initializeContainerMethod->setPrivate()
            ->setReturnType('void')
            ->addBody(
                <<<PHP
self::\$container ??= ({$stringableInitiator})();
PHP
            );


        $namespace = new PhpNamespace($traitNamespace);
        $namespace->add($trait);
        $namespace->addUse(ContainerInterface::class);

        $file = new PhpFile();
        $file->addNamespace($namespace);
        $file->setStrictTypes();

        if (!is_dir(($directory))) {
            mkdir(($directory), 0777, true);
        }
        $path = $directory . '/' . $traitName . '.php';
        file_put_contents(
            $path,
            $this->tabsToSpaces((string)$file)
        );
    }

    private function tabsToSpaces(string $s): string
    {
        return str_replace("\t", '    ', $s);
    }
}
