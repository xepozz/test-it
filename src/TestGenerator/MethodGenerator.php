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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\UnionType;
use Xepozz\TestIt\MatrixIntersection;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeSerializer;
use Xepozz\TestIt\ValueGeneratorRepository;

class MethodGenerator
{
    private Method $method;
    private int $possibleTestsNumbers = 1;
    private array $possibleReturnTypes = [];

    private MethodBodyBuilder $methodBodyBuilder;
    private Dumper $dumper;
    private MatrixIntersection $intersection;
    private ValueGeneratorRepository $valueGeneratorRepository;
    private TypeSerializer $typeSerializer;

    public function __construct(
        private readonly Context $context,
    ) {
        $this->typeSerializer = new TypeSerializer();
        $this->valueGeneratorRepository = new ValueGeneratorRepository();
        $this->dumper = new Dumper();
        $this->intersection = new MatrixIntersection();
        $this->methodBodyBuilder = MethodBodyBuilder::create();
    }

    public function generate(Stmt\ClassMethod $method): array
    {
        $testMethod = $this->createTestMethod($method);
        $class = $this->context->class;
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
            $testMethod = $testMethod->cloneWithName('test' . ucfirst($method->name->name));
            $testMethod->addBody($this->methodBodyBuilder->build());
            return [$testMethod];
        } elseif ($this->possibleTestsNumbers > 1) {
            $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            array_push($methods, ...$this->addPositiveTest($class, $method, $testMethod));
            $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            array_push($methods, ...$this->addNegativeTest($class, $method, $testMethod));
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
                $this->possibleReturnTypes[] = $returnType->toString();
            } elseif ($returnType->name === 'bool') {
                $this->possibleTestsNumbers *= 2;
            } elseif ($returnType->name === 'string' || $returnType->name === 'int') {
                $this->possibleTestsNumbers *= 10;
            } elseif ($returnType->name === 'array') {
                $this->possibleTestsNumbers *= 10;
            }
            $this->possibleReturnTypes[] = $returnType->toString();
            return;
        }
        if ($returnType instanceof NullableType) {
            $this->possibleTestsNumbers++;
            $this->possibleReturnTypes[] = 'null';
            $this->processType($returnType->type);
            return;
        }
        if ($returnType instanceof FullyQualified) {
            $this->possibleTestsNumbers *= 10;
            $this->possibleReturnTypes[] = $returnType->toString();
            return;
        }
        if ($returnType instanceof UnionType || $returnType instanceof IntersectionType) {
            foreach ($returnType->types as $type) {
                $this->processType($type);
            }
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
     * @param Class_ $class
     * @param ClassMethod $method
     * @param Method $testMethod
     * @return Method[]
     * @throws \ReflectionException
     */
    private function addPositiveTest(
        Stmt\Class_ $class,
        Stmt\ClassMethod $method,
        Method $testMethod,
    ): array {
        $variableName = '$' . lcfirst($class->name->name);

        $testMethod = $testMethod->cloneWithName('test' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType(implode('|', array_unique($this->possibleReturnTypes)));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->typeSerializer->serialize($parameter->type));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $positiveDataProvider = $this->createPositiveDataProvider($method, $testMethod);

        if (count($this->possibleReturnTypes) === 0) {
            $positiveDataProvider->addBody('return [];');
            return [];
        }

        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($this->possibleReturnTypes as $possibleType) {
            $valueGenerator = $this->valueGeneratorRepository->getByType($possibleType);

            if ($valueGenerator === null) {
                continue;
            }

            $parameterValueGenerators = [];
            foreach ($method->getParams() as $parameter) {
                $parameterValueGenerator = $this->valueGeneratorRepository->getByType(
                    $this->typeSerializer->serialize($parameter->type)
                );

                if ($parameterValueGenerator === null) {
                    continue;
                }
                $parameterValueGenerators[] = $parameterValueGenerator->generate();
            }
            $cases = $this->context->config->isCaseEvaluationEnabled()
                ? $this->intersection->intersect(...$parameterValueGenerators)
                : $this->intersection->intersect(
                    $valueGenerator->generate(),
                    ...$parameterValueGenerators,
                );

            if ($cases === []) {
                continue;
            }
            foreach ($cases as $case) {
                $valuesToPrint = $this->convertToCodeEntities($case);
                try {
                    if ($this->context->config->isCaseEvaluationEnabled()) {
                        $result = $this->evaluateMethod($object, $method, $valuesToPrint);
                        $case = [$result, ...$case];
                    }
                } catch (\Throwable) {
                    continue;
                }
                $valuesToPrint = array_map($this->dumper->dump(...), $case);
                $case = implode(', ', $valuesToPrint);
                $positiveDataProvider->addBody("yield [{$case}];");
            }
        }
        $testMethod->addBody($this->methodBodyBuilder->build());

        return [$testMethod, $positiveDataProvider];
    }

    /**
     * @param Stmt\Class_ $class
     * @param Stmt\ClassMethod $method
     * @return Method[]
     * @throws \ReflectionException
     */
    private function addNegativeTest(
        Stmt\Class_ $class,
        Stmt\ClassMethod $method,
        Method $testMethod,
    ): array {
        if (!$this->context->config->isCaseEvaluationEnabled()) {
            /**
             * No need to generate negative test because we can check if the result is not successful
             */
            return [];
        }
        $variableName = '$' . lcfirst($class->name->name);

        $testMethod = $testMethod->cloneWithName('testInvalid' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType(implode('|', array_unique($this->possibleReturnTypes)));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->typeSerializer->serialize($parameter->type));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $invalidDataProvider = $this->createNegativeDataProvider($method, $testMethod);

        if (count($this->possibleReturnTypes) === 0) {
            return [];
        }
        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        $hasInvalidCases = false;
        foreach ($this->possibleReturnTypes as $possibleType) {
            $parameterValueGenerators = [];
            foreach ($method->getParams() as $parameter) {
                $parameterValueGenerator = $this->valueGeneratorRepository->getByType(
                    $this->typeSerializer->serialize($parameter->type)
                );

                if ($parameterValueGenerator === null) {
                    continue;
                }
                $parameterValueGenerators[] = $parameterValueGenerator->generate();
            }
            $cases = $this->intersection->intersect(...$parameterValueGenerators);

            if ($cases === []) {
                continue;
            }
            foreach ($cases as $case) {
                $valuesToPrint = $this->convertToCodeEntities($case);
                try {
                    $this->evaluateMethod($object, $method, $valuesToPrint);
                } catch (\Throwable $e) {
                    $case = implode(', ', $valuesToPrint);
                    $invalidDataProvider->addBody("yield [{$case}];");
                    $hasInvalidCases = true;
                }
            }
        }
        $testMethod->addBody($this->methodBodyBuilder->build());

        if ($hasInvalidCases) {
            return [$testMethod, $invalidDataProvider];
        } else {
            return [];
        }
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
        $dataProvider->setReturnType('iterable');
        $dataProvider->setStatic();

        return $dataProvider;
    }

    private function getParameterType(mixed $parameter): string
    {
        return $parameter->type?->toString() ?? 'mixed';
    }

    private function convertToCodeEntities(mixed $case): array
    {
        return array_map(
            fn ($code) => eval(sprintf('return %s;', $code)),
            array_map($this->dumper->dump(...), $case)
        );
    }

    private function createTestMethod(Stmt\ClassMethod $method): Method
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
        return $testMethod;
    }

    private function evaluateMethod(object $object, Stmt\ClassMethod $method, array $values): mixed
    {
        return $object->{$method->name->name}(...$values);
    }
}