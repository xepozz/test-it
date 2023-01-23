# Test it!

A tool for generating files with tests cases based on class methods signatures.

### Installation

```shell
composer require xepozz/test-it --dev
```

### Usage

Run the script from the console and pass `source` directory and `target` directory by your needs.
Default values are `src` and `tests` respectively.

```shell
./vendor/bin/test-it src tests
```

### Description

The package reads all `.php` files from the `source` directory, analyses it and 
creates files mirrored by the relative path to `source` directory in `target` directory.

The tool respects parameters types and methods return value and generates all possible test cases.

### Example

Input: 
```php
<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneParameter\src;

class UserController
{
    public function inverse(bool $value): bool
    {
        return !$value;
    }
}
```

Output:
```php
<?php

declare(strict_types=1);

namespace Xepozz\TestIt\Tests\Integration\OneParameter\tests;

use Xepozz\TestIt\Tests\Integration\OneParameter\src\UserController;

final class UserControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider dataProviderInverse
     */
    public function testInverse(bool $expectedValue, bool $valueValue): void
    {
        // arrange
        $userController = new UserController();

        // act
        $actualValue = $userController->inverse($valueValue);

        // assert
        $this->assertEquals($expectedValue, $actualValue);
    }


    public static function dataProviderInverse(): array
    {
        return [
            [false, true],
            [true, false],
        ];
    }
}
```

As we can see it generates a `dataProvider` related to a function, evaluates return values and saves it to the data provider function.

### Config file

Create a file with name `test-it.php` in project root if you need to configure generation process and configure the config as you wish.

Here is an example of possible options to configure:

```php
<?php

declare(strict_types=1);

use Xepozz\TestIt\Config;

return function (Config $config) {
    $config
        // disabled results substitution
        ->evaluateCases(false)
        // sets a directory to scan
        ->setSourceDirectory('src')
        // excludes particular files from scanning
        ->excludeFiles([
            __DIR__ . '/src/Kernel.php',
        ])
        // excludes particular directories and all child directories from scanning
        ->excludeDirectories([
            __DIR__ . '/src/Asset',
            __DIR__ . '/src/Controller',
            __DIR__ . '/src/View',
        ])
        // includes subdirectories when parent directories were ignored
        ->includeDirectories([
            __DIR__ . '/src/Controller/DTO',
        ]);
};
```

> Passing command arguments does not make any changes the config

### Help

Call the script with the flag `--help` to see all the possible options.

```shell
./vendor/bin/test-it --help
```
```
Usage:
./test-it [<source> [<target>]]

Arguments:
source                The directory that will be processed [default: "src"]
target                The output directory where tests will be placed [default: "tests"]

Options:
-h, --help            Display help for the given command. When no command is given display help for the ./test-it command
-q, --quiet           Do not output any message
-V, --version         Display this application version
--ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
-n, --no-interaction  Do not ask any interactive question
-v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

### Additional documentation

- [Test case evaluation](/docs/test-case-evaluation.md)

### Roadmap

- [ ] Mock classes creation
- [ ] Support multiple test methods names strategies (`test_function_name` and `testFunctionName`)
- [ ] Test constant expression when a method always returns the same result
- [ ] Add benchmarks
- [ ] Add static analyzer
- [ ] Add exclusion list
  - [X] Paths (directories, files)
  - [X] Classes
  - [ ] Inheritance tree (interfaces, parent classes)
- [ ] Override config with command arguments
- [ ] Add Codeception support

### Restrictions

It doesn't work with not namespaced classes.

It has only one test method name generation strategy. See the roadmap.

