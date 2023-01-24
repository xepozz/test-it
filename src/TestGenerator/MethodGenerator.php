<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TestMethodGenerator\NegativeMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\NoAssertionGenerator;
use Xepozz\TestIt\TestMethodGenerator\PositiveMethodGenerator;
use Xepozz\TestIt\TestMethodGenerator\TestMethodGeneratorInterface;

class MethodGenerator
{
    private TestCaseGenerator $testCaseGenerator;
    /**
     * @var TestMethodGeneratorInterface[]
     */
    private array $methodGenerators;

    public function __construct()
    {
        $this->testCaseGenerator = new TestCaseGenerator();
        $this->methodGenerators = [
            new NoAssertionGenerator(),
            new PositiveMethodGenerator(),
            new NegativeMethodGenerator(),
        ];
    }

    /**
     * @param Context $context
     * @return Method[]
     */
    public function generate(Context $context): array
    {
        $cases = iterator_to_array($this->testCaseGenerator->generate($context), false);

        $methods = [];
        foreach ($this->methodGenerators as $generator) {
            if ($generator->supports($context, $cases)) {
                $methods = [...$methods, ...$generator->generate($context, $cases)];
            }
        }

        return $methods;
    }

}