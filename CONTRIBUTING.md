# Contributing to Laravel Routes

Thank you for considering contributing to Laravel Routes! We welcome contributions from everyone. This document provides guidelines and instructions for contributing.

## Code of Conduct

Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in this project you agree to abide by its terms.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the [issue list](https://github.com/Gecko-Digital-Solutions/laravel-routes/issues) as you might find out that you do not need to create one. When you are creating a bug report, please include as many details as possible:

- Use a clear and descriptive title
- Describe the exact steps which reproduce the problem
- Provide specific examples to demonstrate the steps
- Describe the behavior you observed after following the steps
- Explain which behavior you expected to see instead and why
- Include your PHP version, Laravel version, and package version
- Include relevant error messages and stack traces

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- A clear and descriptive title
- A detailed description of the suggested enhancement
- Possible implementation details
- Use cases and examples demonstrating the enhancement

### Pull Requests

- Fill in the required template
- Follow the PHP and coding style guidelines
- Document new code with appropriate comments
- End all files with a newline character
- Avoid platform-specific code
- Add tests for any new functionality

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Git

### Local Setup

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/your-username/laravel-routes.git
   cd laravel-routes
   ```

3. Add the upstream repository:
   ```bash
   git remote add upstream https://github.com/Gecko-Digital-Solutions/laravel-routes.git
   ```

4. Install dependencies:
   ```bash
   composer install
   ```

5. Create a branch for your changes:
   ```bash
   git checkout -b feature/your-feature-name
   ```

## Running Tests

Make sure all tests pass before submitting a pull request:

```bash
# Run all tests
composer test

# Run with verbosity
vendor/bin/phpunit --verbose

# Run specific test file
vendor/bin/phpunit tests/Unit/RouteControllerTest.php

# Run tests matching a pattern
vendor/bin/phpunit --filter "testDiscovers"

# Generate code coverage
vendor/bin/phpunit --coverage-html=coverage
```

## Coding Standards

This project follows PSR-12 coding standards. Key guidelines:

- Use 4 spaces for indentation (not tabs)
- Class names should be in PascalCase
- Method names should be in camelCase
- Constants should be UPPER_CASE
- Use type hints for all method parameters and return types
- Use meaningful variable names
- Keep lines to 120 characters or less

### Example

```php
<?php

namespace App\Http\Routes;

use Illuminate\Support\Facades\Route;
use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;

class UserRoutes extends AbstractRouteController
{
    public function handle(): void
    {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
        });
    }
}
```

## Commit Messages

Write clear, descriptive commit messages:

- Use the present tense (Add feature not Added feature)
- Use the imperative mood (Move cursor to ... not Moves cursor to ...)
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line
- Consider starting the commit message with an emoji:
  - :bug: for bug fixes
  - :sparkles: for new features
  - :memo: for documentation
  - :recycle: for refactoring
  - :white_check_mark: for tests

Example:

```
Add route discovery for nested directories

- Implements glob pattern support for recursive discovery
- Adds configuration option for custom paths
- Includes comprehensive tests for edge cases

Fixes #42
```

## Documentation

When adding new features, please update the relevant documentation:

- Update [README.md](README.md) with usage examples
- Update [TESTS.md](TESTS.md) if tests are added
- Add code comments for complex logic
- Update [TESTING.md](TESTING.md) if test execution changes

## File Structure

Keep the following file structure in mind:

```
laravel-routes/
├── src/
│   ├── Config/
│   │   └── routes.php
│   ├── Providers/
│   │   └── LaravelRoutesProvider.php
│   └── Routes/
│       ├── AbstractRouteController.php
│       └── RouteController.php
├── tests/
│   ├── Feature/
│   ├── Unit/
│   ├── Fixtures/
│   ├── TestCase.php
│   └── workbench/
├── README.md
├── TESTING.md
├── TESTS.md
├── composer.json
└── phpunit.xml
```

## Review Process

1. Submit your pull request with a clear description
2. Ensure all tests pass and there are no linting errors
3. Code review by maintainers
4. Address any requested changes
5. Once approved, your pull request will be merged

## Release Process

The maintainers handle releases. The process typically includes:

1. Updating version numbers
2. Updating the CHANGELOG
3. Creating a git tag
4. Publishing to Packagist

## Questions?

Feel free to open an issue with the question label or contact us through the [GitHub repository](https://github.com/Gecko-Digital-Solutions/laravel-routes).

## License

By contributing to Laravel Routes, you agree that your contributions will be licensed under its MIT license.

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Standards](https://www.php-fig.org/)
- [Git Documentation](https://git-scm.com/doc)
- [Composer Documentation](https://getcomposer.org/doc/)
