<?php

declare(strict_types=1);

namespace Xepozz\TestIt\ValueGenerator;

use Nette\PhpGenerator\Literal;
use PhpParser\Node;
use Xepozz\TestIt\Parser\ContextProvider;

final class ClassValueGenerator implements ValueGeneratorInterface
{
    public function __construct(
        private readonly ContextProvider $contextProvider,
    ) {
    }


    public function generate(): iterable
    {
        $context = $this->contextProvider->getContext();

        $class = $context->class;

        if ($class instanceof Node\Stmt\Enum_) {
            $reflectionClass = new \ReflectionEnum((string) $class->namespacedName);
            foreach ($reflectionClass->getCases() as $case) {
                yield new Literal(
                    sprintf(
                        "%s::%s",
                        '\\' . $case->class,
                        $case->name,
                    )
                );
            }
        }
        return [];
    }
}