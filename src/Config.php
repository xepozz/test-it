<?php

declare(strict_types=1);

namespace Xepozz\TestIt;

use Psr\Container\ContainerInterface;

use Xepozz\TestIt\NamingStrategy\MethodNameStrategyEnum;

final class Config
{
    private array $excludedDirectories = [];
    private array $excludedFiles = [];
    private bool $evaluateCases = true;
    /**
     * @var class-string[]
     */
    private array $excludedClasses = [];
    private string $sourceDirectory = 'src';
    private string $targetDirectory = 'tests';
    private array $includedDirectories = [];
    private MethodNameStrategyEnum $methodNamingStrategy = MethodNameStrategyEnum::CAMEL_CASE;
    private ?ContainerInterface $container = null;
    /**
     * @var callable|null
     */
    public $containerFactory = null;

    /**
     * Disabled test cases evaluation at runtime.
     * Enabled by default.
     */
    public function evaluateCases(bool $enabled): self
    {
        $this->evaluateCases = $enabled;
        return $this;
    }

    public function isCaseEvaluationEnabled(): bool
    {
        return $this->evaluateCases;
    }

    /**
     * Directories that will be excluded from generation.
     * @param string[] $directories
     */
    public function excludeDirectories(array $directories): self
    {
        $this->excludedDirectories = array_merge($this->excludedDirectories, $directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }

    /**
     * Additional directories that will be included to generation, e.g. subdirectories of ignored directories.
     * @param string[] $directories
     */
    public function includeDirectories(array $directories): self
    {
        $this->includedDirectories = array_merge($this->includedDirectories, $directories);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getIncludedDirectories(): array
    {
        return $this->includedDirectories;
    }

    /**
     * Files that will be excluded from generation.
     * @param string[] $files
     */
    public function excludeFiles(array $files): self
    {
        $this->excludedFiles = array_merge($this->excludedFiles, $files);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExcludedFiles(): array
    {
        return $this->excludedFiles;
    }

    /**
     * Classes that will be excluded from generation.
     * @param class-string[] $classes
     */
    public function excludeClasses(array $classes): self
    {
        $this->excludedClasses = array_merge($this->excludedClasses, $classes);
        return $this;
    }

    /**
     * @return class-string[]
     */
    public function getExcludedClasses(): array
    {
        return $this->excludedClasses;
    }

    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    public function setSourceDirectory(string $directory): self
    {
        $this->sourceDirectory = $directory;
        return $this;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function setTargetDirectory(string $directory): self
    {
        $this->targetDirectory = $directory;
        return $this;
    }

    public function getMethodNamingStrategy(): MethodNameStrategyEnum
    {
        return $this->methodNamingStrategy;
    }

    public function useCamelCaseInTestNaming(): self
    {
        $this->methodNamingStrategy = MethodNameStrategyEnum::CAMEL_CASE;
        return $this;
    }

    public function useSnakeCaseInTestNaming(): self
    {
        $this->methodNamingStrategy = MethodNameStrategyEnum::SNAKE_CASE;
        return $this;
    }

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    public function getContainerFactory(): ?callable
    {
        return $this->containerFactory;
    }

    public function setContainerFactory(callable $callback): self
    {
        $this->containerFactory = $callback;
        $this->container = $callback();
        return $this;
    }
}