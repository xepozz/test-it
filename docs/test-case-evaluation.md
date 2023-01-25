# Test case evaluation

Test cases are trying to be evaluated with the test method during the tests generation process.

By default, the parameter `\Xepozz\TestIt\Config::$evaluateCases` is enabled, but may cost you some time during the
generation process, anyway it is better to keep it as `true`.

Enabled parameter generates negative test cases in addition to positive ones.
Negative test cases are cases that were caused by catching an exception during the evaluation.

Call `$config->evaluateCases(false)` in the config file if you need to disable this behaviour.

When the `$evaluateCases` is `true`:

1. Positive test method generates every time you run the generation.
2. Positive test cases are filtering by the evaluation
    1. During test case generation a method calls with all possible values and checks result
    2. If the evaluation does not throw any exceptions, the test case becomes as a positive
    3. If an exception was caught, the test case becomes as a negative
3. Positive test cases use all possible values combinations are existed in value generators: both parameters types and
   method return type.

When the `$evaluateCases` is `true`:

1. Positive test method generates every time you run the generation.
2. Positive test cases are not filtering at all
3. Positive test cases do not use values combinations from the test method return type, only parameters ones.

## How it works

As an examples we have the following method:

```php
class UserController
{
    public function sum(int $a, int $b): int
    {
        return $a + $b;
    }
}
```

##### The result when `$evaluateCases` is enabled

```php
final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderSum
     */
    public function testSum(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderSum(): iterable
    {
        yield [-9223372036854775807-1, PHP_INT_MIN, 0];
        yield [-9223372036854775807, PHP_INT_MIN, 1];
        yield [-1, PHP_INT_MIN, PHP_INT_MAX];
        yield [-2, -1, -1];
        yield [-1, -1, 0];
        yield [0, -1, 1];
        yield [9223372036854775806, -1, PHP_INT_MAX];
        yield [-9223372036854775807-1, 0, PHP_INT_MIN];
        yield [-1, 0, -1];
        yield [0, 0, 0];
        yield [1, 0, 1];
        yield [9223372036854775807, 0, PHP_INT_MAX];
        yield [-9223372036854775807, 1, PHP_INT_MIN];
        yield [0, 1, -1];
        yield [1, 1, 0];
        yield [2, 1, 1];
        yield [-1, PHP_INT_MAX, PHP_INT_MIN];
        yield [9223372036854775806, PHP_INT_MAX, -1];
        yield [9223372036854775807, PHP_INT_MAX, 0];
    }


    /**
     * @dataProvider invalidDataProviderSum
     */
    public function testInvalidSum(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function invalidDataProviderSum(): iterable
    {
        yield [PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MIN, -1];
        yield [-1, PHP_INT_MIN];
        yield [1, PHP_INT_MAX];
        yield [PHP_INT_MAX, 1];
        yield [PHP_INT_MAX, PHP_INT_MAX];
    }
}
```

##### The result when `$evaluateCases` is disabled

```php
final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderSum
     */
    public function testSum(int $expectedValue, int $aValue, int $bValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->sum($aValue, $bValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderSum(): iterable
    {
        yield [PHP_INT_MIN, PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MIN, PHP_INT_MIN, -1];
        yield [PHP_INT_MIN, PHP_INT_MIN, 0];
        yield [PHP_INT_MIN, PHP_INT_MIN, 1];
        yield [PHP_INT_MIN, PHP_INT_MIN, PHP_INT_MAX];
        yield [PHP_INT_MIN, -1, PHP_INT_MIN];
        yield [PHP_INT_MIN, -1, -1];
        yield [PHP_INT_MIN, -1, 0];
        yield [PHP_INT_MIN, -1, 1];
        yield [PHP_INT_MIN, -1, PHP_INT_MAX];
        yield [PHP_INT_MIN, 0, PHP_INT_MIN];
        yield [PHP_INT_MIN, 0, -1];
        yield [PHP_INT_MIN, 0, 0];
        yield [PHP_INT_MIN, 0, 1];
        yield [PHP_INT_MIN, 0, PHP_INT_MAX];
        yield [PHP_INT_MIN, 1, PHP_INT_MIN];
        yield [PHP_INT_MIN, 1, -1];
        yield [PHP_INT_MIN, 1, 0];
        yield [PHP_INT_MIN, 1, 1];
        yield [PHP_INT_MIN, 1, PHP_INT_MAX];
        yield [PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MIN];
        yield [PHP_INT_MIN, PHP_INT_MAX, -1];
        yield [PHP_INT_MIN, PHP_INT_MAX, 0];
        yield [PHP_INT_MIN, PHP_INT_MAX, 1];
        yield [PHP_INT_MIN, PHP_INT_MAX, PHP_INT_MAX];
        yield [-1, PHP_INT_MIN, PHP_INT_MIN];
        yield [-1, PHP_INT_MIN, -1];
        yield [-1, PHP_INT_MIN, 0];
        yield [-1, PHP_INT_MIN, 1];
        yield [-1, PHP_INT_MIN, PHP_INT_MAX];
        yield [-1, -1, PHP_INT_MIN];
        yield [-1, -1, -1];
        yield [-1, -1, 0];
        yield [-1, -1, 1];
        yield [-1, -1, PHP_INT_MAX];
        yield [-1, 0, PHP_INT_MIN];
        yield [-1, 0, -1];
        yield [-1, 0, 0];
        yield [-1, 0, 1];
        yield [-1, 0, PHP_INT_MAX];
        yield [-1, 1, PHP_INT_MIN];
        yield [-1, 1, -1];
        yield [-1, 1, 0];
        yield [-1, 1, 1];
        yield [-1, 1, PHP_INT_MAX];
        yield [-1, PHP_INT_MAX, PHP_INT_MIN];
        yield [-1, PHP_INT_MAX, -1];
        yield [-1, PHP_INT_MAX, 0];
        yield [-1, PHP_INT_MAX, 1];
        yield [-1, PHP_INT_MAX, PHP_INT_MAX];
        yield [0, PHP_INT_MIN, PHP_INT_MIN];
        yield [0, PHP_INT_MIN, -1];
        yield [0, PHP_INT_MIN, 0];
        yield [0, PHP_INT_MIN, 1];
        yield [0, PHP_INT_MIN, PHP_INT_MAX];
        yield [0, -1, PHP_INT_MIN];
        yield [0, -1, -1];
        yield [0, -1, 0];
        yield [0, -1, 1];
        yield [0, -1, PHP_INT_MAX];
        yield [0, 0, PHP_INT_MIN];
        yield [0, 0, -1];
        yield [0, 0, 0];
        yield [0, 0, 1];
        yield [0, 0, PHP_INT_MAX];
        yield [0, 1, PHP_INT_MIN];
        yield [0, 1, -1];
        yield [0, 1, 0];
        yield [0, 1, 1];
        yield [0, 1, PHP_INT_MAX];
        yield [0, PHP_INT_MAX, PHP_INT_MIN];
        yield [0, PHP_INT_MAX, -1];
        yield [0, PHP_INT_MAX, 0];
        yield [0, PHP_INT_MAX, 1];
        yield [0, PHP_INT_MAX, PHP_INT_MAX];
        yield [1, PHP_INT_MIN, PHP_INT_MIN];
        yield [1, PHP_INT_MIN, -1];
        yield [1, PHP_INT_MIN, 0];
        yield [1, PHP_INT_MIN, 1];
        yield [1, PHP_INT_MIN, PHP_INT_MAX];
        yield [1, -1, PHP_INT_MIN];
        yield [1, -1, -1];
        yield [1, -1, 0];
        yield [1, -1, 1];
        yield [1, -1, PHP_INT_MAX];
        yield [1, 0, PHP_INT_MIN];
        yield [1, 0, -1];
        yield [1, 0, 0];
        yield [1, 0, 1];
        yield [1, 0, PHP_INT_MAX];
        yield [1, 1, PHP_INT_MIN];
        yield [1, 1, -1];
        yield [1, 1, 0];
        yield [1, 1, 1];
        yield [1, 1, PHP_INT_MAX];
        yield [1, PHP_INT_MAX, PHP_INT_MIN];
        yield [1, PHP_INT_MAX, -1];
        yield [1, PHP_INT_MAX, 0];
        yield [1, PHP_INT_MAX, 1];
        yield [1, PHP_INT_MAX, PHP_INT_MAX];
        yield [PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MIN];
        yield [PHP_INT_MAX, PHP_INT_MIN, -1];
        yield [PHP_INT_MAX, PHP_INT_MIN, 0];
        yield [PHP_INT_MAX, PHP_INT_MIN, 1];
        yield [PHP_INT_MAX, PHP_INT_MIN, PHP_INT_MAX];
        yield [PHP_INT_MAX, -1, PHP_INT_MIN];
        yield [PHP_INT_MAX, -1, -1];
        yield [PHP_INT_MAX, -1, 0];
        yield [PHP_INT_MAX, -1, 1];
        yield [PHP_INT_MAX, -1, PHP_INT_MAX];
        yield [PHP_INT_MAX, 0, PHP_INT_MIN];
        yield [PHP_INT_MAX, 0, -1];
        yield [PHP_INT_MAX, 0, 0];
        yield [PHP_INT_MAX, 0, 1];
        yield [PHP_INT_MAX, 0, PHP_INT_MAX];
        yield [PHP_INT_MAX, 1, PHP_INT_MIN];
        yield [PHP_INT_MAX, 1, -1];
        yield [PHP_INT_MAX, 1, 0];
        yield [PHP_INT_MAX, 1, 1];
        yield [PHP_INT_MAX, 1, PHP_INT_MAX];
        yield [PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MIN];
        yield [PHP_INT_MAX, PHP_INT_MAX, -1];
        yield [PHP_INT_MAX, PHP_INT_MAX, 0];
        yield [PHP_INT_MAX, PHP_INT_MAX, 1];
        yield [PHP_INT_MAX, PHP_INT_MAX, PHP_INT_MAX];
    }
}
```

So these examples show us two different way of file generation.