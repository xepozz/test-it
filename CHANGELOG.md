# Changelog

## 1.2.1 under development

- Add event dispatcher listens class, method and file generators 
- Add initiators to be able to construct any objects by user side
- Add PHPUnit, composer-unused, composer-require-checker to CI
- Fix generating no asserting case

## 1.2.0

- Add and refactor tests
- Add expecting an exception instead of assertEquals
- Add values deserialization in negative method
- Add dependency injector container
- Make test generators stateless
- Add yiisoft/var-dumper, because it may dump closure
- Refactor codebase, decompose TestMethodBuilder onto few separated classes, add more extension points
- Add "exactly" test method generation. It generates when the result is a constant statement and the method does not have any parameters. 
- TestGenerator does not generate test classes for classes without methods 
- Use generators (`yield` statement) instead of returning an array 

## 1.1.0

- Add control of test case evaluation.
- Add filtering directories.
- Add config file.

## 1.0.0

- First release.
