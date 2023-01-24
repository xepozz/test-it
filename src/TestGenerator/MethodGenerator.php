<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use PhpParser\Node\Stmt;
use Xepozz\TestIt\Helper\TestMethodFactory;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;

class MethodGenerator
{
    private TypeNormalizer $typeNormalizer;
    private TestCaseGenerator $testCaseGenerator;
    /**
     * @var PositiveMethodGenerator[]
     */
    private array $methodGenerators;
    private TestMethodFactory $testMethodFactory;

    public function __construct(
        private readonly Context $context,
    ) {
        $this->typeNormalizer = new TypeNormalizer();
        $this->testCaseGenerator = new TestCaseGenerator();
        $this->testMethodFactory = new TestMethodFactory();
        $this->methodGenerators = [
            new PositiveMethodGenerator(),
            new NegativeMethodGenerator(),
        ];
    }

    public function generate(Stmt\ClassMethod $method): array
    {
        $class = $this->context->class;
        $methodName = $method->name->name;

        $variableName = '$' . lcfirst($class->name->name);

        // TODO: calc parameters types?
        $possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());

        if (count($possibleReturnTypes) === 0) {
            $methodBodyBuilder = MethodBodyBuilder::create();
            $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            $template = <<<PHP
\$this->expectNotToPerformAssertions();
{$variableName}->{$methodName}();
PHP;

            $methodBodyBuilder->addAssert($template);
            $testMethodName = 'test' . ucfirst($method->name->name);
            $testMethod = $this->testMethodFactory->create($testMethodName, $method);
            $testMethod = $testMethod->cloneWithName($testMethodName);
            $testMethod->addBody($methodBodyBuilder->build());

            return [$testMethod];
        }

        $cases = iterator_to_array($this->testCaseGenerator->generate($this->context), false);

        $methods = [];
        foreach ($this->methodGenerators as $generator) {
            $methods = [...$methods, ...$generator->generate($this->context, $cases)];
        }

        return $methods;
    }

}