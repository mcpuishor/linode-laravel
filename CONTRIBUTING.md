# Contributing

Thank you for considering contributing to the Linode Laravel package! This document contains guidelines that will help you get started with contributing to this project.

## Development Environment

1. Fork the repository
2. Clone your fork locally
3. Install dependencies with Composer:

```bash
composer install
```

## Testing

This package uses Pest for testing. Pest is a testing framework built on top of PHPUnit that provides a better developer experience and simpler API.

### Running Tests

To run the test suite:

```bash
composer test
```

To run the tests with coverage reporting:

```bash
composer test:coverage
```

### Writing Tests

All tests should be written in Pest format. Here's a basic example of how to write a test in Pest:

```php
test('it does something', function () {
    // Arrange
    $object = new SomeClass();

    // Act
    $result = $object->doSomething();

    // Assert
    expect($result)->toBe('expected result');
});
```

Use the provided helper functions in `Pest.php` like `config_for_testing()` to set up common test requirements.

## Coding Standards

This project follows the PSR-12 coding standard. Make sure your code adheres to these standards before submitting a pull request.

## Pull Request Process

1. Update the README.md or documentation with details of changes if appropriate
2. Update the CHANGELOG.md file with details of changes
3. The PR should work with the latest PHP and Laravel versions
4. Ensure all tests pass
5. Make sure code coverage stays at an acceptable level

## License

By contributing, you agree that your contributions will be licensed under the project's MIT License.
