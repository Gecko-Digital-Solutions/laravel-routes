# Test Execution Guide - Laravel Routes Package

## 📦 Installation and Preparation

### Prerequisites
- PHP 8.2+
- Composer
- Laravel 10.0+

### Steps

```bash
# 1. Install dependencies
composer install

# 2. Test files are ready to use
```

## 🧪 Running Tests

### All tests
```bash
# Use the Composer script
composer test

# Or directly with PHPUnit
vendor/bin/phpunit

# With verbose output
vendor/bin/phpunit --verbose
```

### Specific tests

```bash
# Unit tests only
vendor/bin/phpunit tests/Unit/

# Integration tests only
vendor/bin/phpunit tests/Feature/

# A specific test file
vendor/bin/phpunit tests/Unit/RouteControllerTest.php

# A specific test
vendor/bin/phpunit --filter testDiscoversValidRouteClasses

# Multiple tests by pattern
vendor/bin/phpunit --filter "testCalls"
```

## 📊 Code Coverage

### Generate HTML report
```bash
vendor/bin/phpunit --coverage-html=coverage
# Open coverage/index.html in browser
```

### Generate Clover XML report (for Codecov, etc.)
```bash
vendor/bin/phpunit --coverage-clover=coverage.xml
```

### Generate text report
```bash
vendor/bin/phpunit --coverage-text
```

## 🔄 CI/CD Integration

### GitHub Actions

```yaml
name: Run Tests & Coverage

on:
  push:
    branches:
      - main
  pull_request:
    branches:
      - main

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      fail-fast: false
      matrix:
        php: ['8.2', '8.3', '8.4']
        laravel: ['^10.0', '^11.0', '^12.0']
        exclude:
          - php: '8.2'
            laravel: '^12.0'
          - php: '8.4'
            laravel: '^10.0'
          - php: '8.4'
            laravel: '^11.0'

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: dom, xml
          coverage: xdebug

      - name: Install Laravel Dependencies
        run: composer require "laravel/framework:${{ matrix.laravel }}" --no-interaction --no-update

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Tests
        run: vendor/bin/phpunit --coverage-clover build/logs/clover.xml

      - name: Upload coverage to Coveralls
        uses: coverallsapp/github-action@v2
        with:
          github-token: ${{ secrets.GITHUB_TOKEN }}
          path-to-lcov: build/logs/clover.xml
          parallel: true
          build-number: ${{ github.run_id }}

  coveralls:
    name: Finalize Coveralls
    needs: test
    if: always()
    runs-on: ubuntu-latest
    steps:
      - name: Send Coveralls Finished
        uses: coverallsapp/github-action@v2
        with:
          github-token: ${{ secrets.GITHUB_TOKEN }}
          parallel-finished: true
          build-number: ${{ github.run_id }}
```

## ✅ Checklist

- [ ] Install dependencies with `composer install`
- [ ] Run `composer test` to verify all tests pass
- [ ] Read the documentation in `TESTS.md`
- [ ] Add tests to your CI/CD
- [ ] Configure code coverage if desired

## 📝 Useful PHPUnit Options

```bash
# Stop on first failure
vendor/bin/phpunit --stop-on-failure

# Verbose output
vendor/bin/phpunit -v

# Very verbose (shows each test)
vendor/bin/phpunit -vv

# Exclude tests
vendor/bin/phpunit --exclude-group slow

# Test groups
vendor/bin/phpunit --group integration

# Stop after X failures
vendor/bin/phpunit --stop-on-failure --stop-on-error

# Show warnings
vendor/bin/phpunit -d error_reporting=E_ALL
```

## 🐛 Troubleshooting

### Tests do not run
```bash
# Verify that phpunit.xml file exists
ls -la phpunit.xml

# Check autoload
php -r "require 'vendor/autoload.php'; echo 'OK';"
```

### Cache errors
```bash
# Clear workbench cache
rm -rf tests/workbench/bootstrap/cache/*

# Regenerate autoload
composer dump-autoload
```

### Permissions
```bash
# Ensure cache is accessible
chmod -R 777 tests/workbench/bootstrap/cache
```

## 📈 Success Metrics

When tests pass, you should see:
```
PHPUnit 12.4.3 by Sebastian Bergmann and contributors.

...............................                                   31 / 31 (100%)

Time: 00:00.439, Memory: 34.00 MB

OK (31 tests, 47 assertions)
```

### Code Coverage
```
RouteController.php: 13 of 16 statements covered (81.25%)
```

## 📚 Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Orchestra Testbench](https://github.com/orchestral/testbench)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
