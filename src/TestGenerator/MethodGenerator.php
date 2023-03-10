<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\Method;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TestMethodGenerator\TestMethodGeneratorInterface;

final class MethodGenerator
{
    public function __construct(
        private readonly TestCaseGenerator $testCaseGenerator,
        /**
         * @var TestMethodGeneratorInterface[]
         */
        private readonly array $testMethodGenerators,
    ) {
    }

    /**
     * @return Method[]
     */
    public function generate(Context $context): array
    {
        $cases = iterator_to_array($this->testCaseGenerator->generate($context), false);

        $methods = [];
        foreach ($this->testMethodGenerators as $generator) {
            if ($generator->supports($context, $cases)) {
                $methods = [...$methods, ...$generator->generate($context, $cases)];
            }
        }

        return $methods;
    }
}
