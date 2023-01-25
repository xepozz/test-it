<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Xepozz\TestIt\Helper\Finder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\Parser\ContextMethodVisitor;

final readonly class TestGenerator
{
    private Parser $parser;

    public function __construct(private Config $config)
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function process(): void
    {
        $sourceDirectory = $this->config->getSourceDirectory();
        $targetDirectory = $this->config->getTargetDirectory();

        $finder = Finder::fromConfig($this->config);

        $traverser = new NodeTraverser;
        $nameResolver = new NameResolver;
        $traverser->addVisitor($nameResolver);

        $context = new Context($this->config);

        $visitor = new ContextMethodVisitor($context);
        $traverser->addVisitor($visitor);

        foreach ($finder as $file) {
            $fileSourcePath = $file->getRealPath();
            $fileTargetPath = str_replace([$sourceDirectory, '.php'], [$targetDirectory, 'Test.php'], $fileSourcePath);
            $nodes = (array) $this->parser->parse(file_get_contents($fileSourcePath));

            $traverser->traverse($nodes);
//            $traverser->removeVisitor($visitor);

            $files = $visitor->dump();
            foreach ($files as $phpFile) {
                $this->saveFile($fileTargetPath, $this->tabsToSpaces((string) $phpFile));
            }
        }
    }

    private function tabsToSpaces(string $s): string
    {
        return str_replace("\t", '    ', $s);
    }

    private function saveFile(string $path, string $content): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, $content);
    }
}