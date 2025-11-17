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
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php-version: ['8.2', '8.3', '8.4']
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: json
      
      - name: Install dependencies
        run: composer install
      
      - name: Run tests
        run: composer test
      
      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          file: ./coverage.xml
          flags: unittests
```

### GitLab CI

```yaml
stages:
  - test

test:
  stage: test
  image: php:8.4
  
  before_script:
    - apt-get update && apt-get install -y git
    - curl -fsSL https://getcomposer.org/installer | php
    - php composer.phar install
  
  script:
    - composer test
  
  coverage: '/Tests: .*?\n.*?Assertions: (\d+)/'
```

### GitLab CI (More complete with coverage)

```yaml
stages:
  - test

test:
  stage: test
  image: php:8.4
  
  before_script:
    - apt-get update && apt-get install -y git unzip
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install --no-progress
  
  script:
    - vendor/bin/phpunit --coverage-clover=coverage.xml
  
  coverage: '/Tests: (\d+), Assertions: (\d+)/'
  
  artifacts:
    reports:
      coverage_report:
        coverage_format: cobertura
        path: coverage.xml
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
PHPUnit X.X.X by Sebastian Bergmann and contributors.

...........................                                       27 / 27 (100%)

Time: 00:00.XXX, Memory: XX.00 MB

OK (27 tests, 35 assertions)
```

## 📚 Resources

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Orchestra Testbench](https://github.com/orchestral/testbench)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
