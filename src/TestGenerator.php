<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\Parser\ContextMethodVisitor;

final readonly class TestGenerator
{
    private Parser $parser;

    public function __construct(
        private string $sourceDirectory,
        private string $testDirectory,
    ) {
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function process(): void
    {
        $sourceDirectory = $this->sourceDirectory;
        $testDirectory = $this->testDirectory;

        $finder = (new Finder())->in($sourceDirectory)->name('*.php')->files();

        $traverser = new NodeTraverser;
        $nameResolver = new NameResolver;
        $traverser->addVisitor($nameResolver);

        $context = new Context($sourceDirectory, $testDirectory);

        foreach ($finder as $file) {
            $fileSourcePath = $file->getRealPath();
            $fileTargetPath = str_replace([$sourceDirectory, '.php'], [$testDirectory, 'Test.php'], $fileSourcePath);
            $nodes = (array) $this->parser->parse(file_get_contents($fileSourcePath));

            $visitor = new ContextMethodVisitor($context);
            $traverser->addVisitor($visitor);
            $traverser->traverse($nodes);
            $traverser->removeVisitor($visitor);

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