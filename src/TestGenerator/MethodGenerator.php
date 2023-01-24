<?php

declare(strict_types=1);

namespace Xepozz\TestIt\TestGenerator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Method;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Xepozz\TestIt\MethodBodyBuilder;
use Xepozz\TestIt\Parser\Context;
use Xepozz\TestIt\TypeNormalizer;
use Xepozz\TestIt\TypeSerializer;

class MethodGenerator
{
    private array $possibleReturnTypes = [];

    private MethodBodyBuilder $methodBodyBuilder;
    private Dumper $dumper;
    private TypeNormalizer $typeNormalizer;
    private TypeSerializer $typeSerializer;
    private TestCaseGenerator $testCaseGenerator;

    public function __construct(
        private readonly Context $context,
    ) {
        $this->typeNormalizer = new TypeNormalizer();
        $this->typeSerializer = new TypeSerializer();
        $this->dumper = new Dumper();
        $this->testCaseGenerator = new TestCaseGenerator();

        $this->methodBodyBuilder = MethodBodyBuilder::create();
    }

    public function generate(Stmt\ClassMethod $method): array
    {
        $testMethod = $this->createTestMethod($method);
        $class = $this->context->class;
        $methodName = $method->name->name;

        $methods = [];
        $variableName = '$' . lcfirst($class->name->name);

        // TODO: calc parameters types?
        $this->possibleReturnTypes = $this->typeNormalizer->denormalize($method->getReturnType());

        if (count($this->possibleReturnTypes) === 0) {
            $methodBodyBuilder = MethodBodyBuilder::create();
            $methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
            $template = <<<PHP
\$this->expectNotToPerformAssertions();
{$variableName}->{$methodName}();
PHP;

            $methodBodyBuilder->addAssert($template);
            $testMethod = $testMethod->cloneWithName('test' . ucfirst($method->name->name));
            $testMethod->addBody($methodBodyBuilder->build());

            return [$testMethod];
        }

        $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        array_push($methods, ...$this->addPositiveTest($class, $method, $testMethod));
        $this->methodBodyBuilder->addArrange("{$variableName} = new {$class->name->name}();");
        array_push($methods, ...$this->addNegativeTest($class, $method, $testMethod));

        return $methods;
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
        $testMethod = $testMethod->cloneWithName('test' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType($this->typeSerializer->serialize($this->possibleReturnTypes));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->typeSerializer->serialize($this->typeNormalizer->denormalize($parameter->type)));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);
        $variableName = '$' . lcfirst($class->name->name);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $positiveDataProvider = $this->createPositiveDataProvider($method, $testMethod);

        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        $cases = $this->testCaseGenerator->generate($this->context);

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
        $testMethod->addBody($this->methodBodyBuilder->build());

        return [$testMethod, $positiveDataProvider];
    }

    /**
     * @param Class_ $class
     * @param ClassMethod $method
     * @param Method $testMethod
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

        $testMethod = $testMethod->cloneWithName('testInvalid' . ucfirst($method->name->name));
        $testMethod
            ->addParameter('expectedValue')
            ->setType($this->typeSerializer->serialize($this->possibleReturnTypes));

        $arguments = [];
        foreach ($method->getParams() as $parameter) {
            $parameterName = $parameter->var->name . 'Value';
            $arguments[] = '$' . $parameterName;
            $testMethod
                ->addParameter($parameterName)
                ->setType($this->typeSerializer->serialize($this->typeNormalizer->denormalize($parameter->type)));
        }
        $arguments = $arguments === [] ? null : implode(', ', $arguments);
        $variableName = '$' . lcfirst($class->name->name);

        $this->methodBodyBuilder->addAct("\$actualValue = {$variableName}->{$method->name->name}($arguments);");
        $this->methodBodyBuilder->addAssert("\$this->assertEquals(\$expectedValue, \$actualValue);");

        $invalidDataProvider = $this->createNegativeDataProvider($method, $testMethod);

        $reflectionClass = new \ReflectionClass((string) $class->namespacedName);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        $hasInvalidCases = false;
        $cases = $this->testCaseGenerator->generate($this->context);

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
        $testMethod->addBody($this->methodBodyBuilder->build());

        if (!$hasInvalidCases) {
            return [];
        }
        return [$testMethod, $invalidDataProvider];
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