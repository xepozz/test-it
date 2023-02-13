<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Xepozz\TestIt\Helper\Finder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\Parser\ContextMethodVisitor;
use Xepozz\TestIt\Parser\ContextProvider;

final class TestGenerator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private Parser $parser;

    public function __construct(
        LoggerInterface $logger,
        private readonly ContextMethodVisitor $contextMethodVisitor,
        private readonly ContextProvider $contextProvider,
    ) {
        $this->logger = $logger;
        $this->parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
    }

    public function process(Config $config): void
    {
        $sourceDirectory = $config->getSourceDirectory();
        $targetDirectory = $config->getTargetDirectory();

        $finder = Finder::fromConfig($config);

        $visitor = $this->contextMethodVisitor;
        $context = new Context($config);
        $this->contextProvider->setContext($context);

        $traverser = new NodeTraverser();
        $nameResolver = new NameResolver();
        $traverser->addVisitor($nameResolver);
        $traverser->addVisitor($visitor);

        foreach ($finder as $file) {
            $fileSourcePath = $file->getRealPath();
            $this->logger->debug(
                sprintf(
                    'Processing "%s" file',
                    $fileSourcePath,
                )
            );
            $fileTargetPath = str_replace([$sourceDirectory, '.php'], [$targetDirectory, 'Test.php'], $fileSourcePath);
            $nodes = (array) $this->parser->parse(file_get_contents($fileSourcePath));

            $traverser->traverse($nodes);

            $files = $visitor->dump();
            foreach ($files as $phpFile) {
                $this->saveFile($fileTargetPath, $this->tabsToSpaces((string) $phpFile));
            }

            $this->logger->debug(
                sprintf(
                    'Output test file "%s"',
                    $fileTargetPath,
                )
            );
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