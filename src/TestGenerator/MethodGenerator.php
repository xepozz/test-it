<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\UnionType;
use Xepozz\TestIt\MatrixIntersection;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\ValueGenerator\ArrayValueGenerator;
use Xepozz\TestIt\ValueGenerator\BooleanValueGenerator;
use Xepozz\TestIt\ValueGenerator\IntegerValueGenerator;
use Xepozz\TestIt\ValueGenerator\MixedValueGenerator;
use Xepozz\TestIt\ValueGenerator\NullValueGenerator;
use Xepozz\TestIt\ValueGenerator\StringValueGenerator;
use Xepozz\TestIt\ValueGenerator\ValueGeneratorInterface;

class MethodGenerator
{
    private Method $method;
    private int $possibleTestsNumbers = 1;
    private array $possibleTypes = [];

    private MethodBodyBuilder $methodBodyBuilder;
    private Dumper $dumper;
    private MatrixIntersection $intersection;

    public function __construct(Stmt\ClassMethod $method)
    {
        $testMethod = new Method('test' . ucfirst($method->name->name));
        $testMethod->setReturnType('void');

        if ($method->isFinal()) {
            $testMethod->setFinal();
        }
        if ($method->isAbstract()) {
            $testMethod->setAbstract();
        }
        if ($method->isStatic()) {
            $testMethod->setStatic();
        }
        if ($method->isProtected()) {
            $testMethod->setProtected();
        } elseif ($method->isPrivate()) {
            $testMethod->setPrivate();
        } else {
            $testMethod->setPublic();
        }

        $this->method = $testMethod;
        $this->dumper = new Dumper();
        $this->intersection = new MatrixIntersection();
        $this->methodBodyBuilder = MethodBodyBuilder::create();
    }

    public function generate(Context $context): array
    {
        $class = $context->class;
        $method = $context->method;
        $methodName = $method->name->name;

        $methods = [];
        $variableName = '$' . lcfirst($class->name->name);

        $this->calculatePossibleTestsNumber($method);

        if ($this->possibleTestsNumbers === 1) {
            $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            $template = <<<PHP
\$this->expectNotToPerformAssertions();
{$variableName}->{$methodName}();
PHP;

            $this->methodBodyBuilder->addAssert($template);
            $testMethod = $this->method->cloneWithName('test' . ucfirst($method->name->name));
            $testMethod->addBody($this->methodBodyBuilder->build());
            return [$testMethod];
        } elseif ($this->possibleTestsNumbers > 1) {
            $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            array_push($methods, ...$this->addPositiveDataProvider($class, $method));
            $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            array_push($methods, ...$this->addNegativeDataProvider($class, $method));
        }

        return $methods;
    }

    private function calculatePossibleTestsNumber(Stmt\ClassMethod $method): void
    {
//        $this->processParameters($method->getParams());
        $this->processType($method->getReturnType());
    }

    private function processType(mixed $returnType): void
    {
        if ($returnType === null) {
            return;
        }
        if ($returnType instanceof Identifier) {
            if ($returnType->name === 'true' || $returnType->name === 'false') {
                $this->possibleTypes[] = $returnType->name;
            } elseif ($returnType->name === 'bool') {
                $this->possibleTestsNumbers *= 2;
            } elseif ($returnType->name === 'string' || $returnType->name === 'int') {
                $this->possibleTestsNumbers *= 10;
            } elseif ($returnType->name === 'array') {
                $this->possibleTestsNumbers *= 10;
            }
            $this->possibleTypes[] = $returnType->name;
            return;
        }
        if ($returnType instanceof UnionType || $returnType instanceof IntersectionType) {
            foreach ($returnType->types as $type) {
                $this->processType($type);
            }
        }
        if ($returnType instanceof NullableType) {
            $this->possibleTestsNumbers++;
            $this->possibleTypes[] = 'null';
            $this->processType($returnType->type);
        }
        if ($returnType instanceof FullyQualified) {
            $this->possibleTestsNumbers *= 10;
            $this->possibleTypes[] = $returnType->toString();
        }
    }

    /**
     * @param Param[] $parameters
     * @return void
     */
    private function processParameters(array $parameters): void
    {
        foreach ($parameters as $parameter) {
            $this->processType($parameter->type);
        }
    }

    /**
     * @param Stmt\Class_ $class
     * @param Stmt\ClassMethod $method
     * @return Method[]
     * @throws \ReflectionException
     */
    private function addPositiveDataProvider(
        Stmt\Class_ $class,
        Stmt\ClassMethod $method
    ): array {
        $variableName = '$' . lcfirst($class->name->name);

        $testMethod = $this->method->cloneWithName('test' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType(implode('|', array_unique($this->possibleTypes)));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->getParameterType($parameter));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $positiveDataProvider = $this->createPositiveDataProvider($method, $testMethod);

        if (count($this->possibleTypes) === 0) {
            $positiveDataProvider->addBody('return [];');
            return [];
        }
        $positiveDataProvider->addBody('return [');

        foreach ($this->possibleTypes as $possibleType) {
            $valueGenerator = $this->valueGenerator($possibleType);

            if ($valueGenerator === null) {
                continue;
            }

            $parameterValueGenerators = [];
            foreach ($method->getParams() as $parameter) {
                $parameterValueGenerator = $this->valueGenerator($this->getParameterType($parameter));

                if ($parameterValueGenerator === null) {
                    continue;
                }
                $parameterValueGenerators[] = $parameterValueGenerator->generate();
            }
            $cases = $this->intersection->intersect(...$parameterValueGenerators);

            if ($cases === []) {
                continue;
            }
            $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
            $object = $reflectionClass->newInstanceWithoutConstructor();
            foreach ($cases as $case) {
                $valuesToPrint = array_map(
                    fn ($code) => eval(sprintf('return %s;', $code)),
                    array_map($this->dumper->dump(...), $case)
                );
                try {
                    $result = $object->{$method->name->name}(...$valuesToPrint);
                } catch (\Throwable) {
                    continue;
                }
                $valuesToPrint = array_map($this->dumper->dump(...), [$result, ...$case]);
                $case = implode(', ', $valuesToPrint);
                $positiveDataProvider->addBody("\t[{$case}],");
            }
        }
        $positiveDataProvider->addBody('];');
        $testMethod->addBody($this->methodBodyBuilder->build());

        return [$testMethod, $positiveDataProvider];
    }

    /**
     * @param Stmt\Class_ $class
     * @param Stmt\ClassMethod $method
     * @return Method[]
     * @throws \ReflectionException
     */
    private function addNegativeDataProvider(
        Stmt\Class_ $class,
        Stmt\ClassMethod $method
    ): array {
        $variableName = '$' . lcfirst($class->name->name);

        $testMethod = $this->method->cloneWithName('testInvalid' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType(implode('|', array_unique($this->possibleTypes)));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->getParameterType($parameter));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $invalidDataProvider = $this->createNegativeDataProvider($method, $testMethod);

        if (count($this->possibleTypes) === 0) {
            return [];
        }
        $invalidDataProvider->addBody('return [');

        $hasInvalidCases = false;
        foreach ($this->possibleTypes as $possibleType) {
            $valueGenerator = $this->valueGenerator($possibleType);

            if ($valueGenerator === null) {
                continue;
            }

            $parameterValueGenerators = [];
            foreach ($method->getParams() as $parameter) {
                $parameterValueGenerator = $this->valueGenerator($this->getParameterType($parameter));

                if ($parameterValueGenerator === null) {
                    continue;
                }
                $parameterValueGenerators[] = $parameterValueGenerator->generate();
            }
            $cases = $this->intersection->intersect(...$parameterValueGenerators);

            if ($cases === []) {
                continue;
            }
            $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
            $object = $reflectionClass->newInstanceWithoutConstructor();
            foreach ($cases as $case) {
                $valuesToPrint = array_map(
                    fn ($code) => eval(sprintf('return %s;', $code)),
                    array_map($this->dumper->dump(...), $case)
                );
                try {
                    $object->{$method->name->name}(...$valuesToPrint);
                } catch (\Throwable $e) {
                    $case = implode(', ', $valuesToPrint);
                    $invalidDataProvider->addBody("\t[{$case}],");
                    $hasInvalidCases = true;
                }
            }
        }
        $invalidDataProvider->addBody('];');
        $testMethod->addBody($this->methodBodyBuilder->build());

        if ($hasInvalidCases) {
            return [$testMethod, $invalidDataProvider];
        } else {
            return [];
        }
    }

    private function valueGenerator(mixed $possibleType): ?ValueGeneratorInterface
    {
        return match ($possibleType) {
            'array' => new ArrayValueGenerator(),
            'bool' => new BooleanValueGenerator(),
            'null' => new NullValueGenerator(),
            'string' => new StringValueGenerator(),
            'int' => new IntegerValueGenerator(),
            'mixed' => new MixedValueGenerator(),
            default => null,
        };
    }

    private function createPositiveDataProvider(
        Stmt\ClassMethod $method,
        Method $testMethod,
    ): Method {
        return $this->createDataProviderInner('dataProvider', $method, $testMethod);
    }

    private function createNegativeDataProvider(
        Stmt\ClassMethod $method,
        Method $testMethod,
    ): Method {
        return $this->createDataProviderInner('invalidDataProvider', $method, $testMethod);
    }

    private function createDataProviderInner(
        string $prefix,
        Stmt\ClassMethod $method,
        Method $testMethod,
    ): Method {
        $dataProviderMethodName = $prefix . ucfirst($method->name->name);
        $testMethod->addComment("@dataProvider {$dataProviderMethodName}");

        $dataProvider = new Method($dataProviderMethodName);
        $dataProvider->setPublic();
        $dataProvider->setReturnType('array');
        $dataProvider->setStatic();

        return $dataProvider;
    }

    private function getParameterType(mixed $parameter): string
    {
        return $parameter->type?->toString() ?? 'mixed';
    }
}