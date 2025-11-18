<div align="center">

[![GitHub License](https://img.shields.io/github/license/Gecko-Digital-Solutions/laravel-routes?labelColor=2d2d2d)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/geckods/laravel-routes?label=Composer&logo=composer&logoColor=white&labelColor=2d2d2d)](https://packagist.org/packages/geckods/laravel-routes)
[![PHP Version](https://img.shields.io/packagist/php-v/geckods/laravel-routes?logo=php&logoColor=w3F448Dhite&labelColor=2d2d2d&color=3F448D)](composer.json)
![Laravel](https://img.shields.io/packagist/dependency-v/geckods/laravel-routes/laravel/framework?label=Laravel&logo=laravel&logoColor=FF2D20&labelColor=2d2d2d&color=FF2D20)

[![Tests](https://github.com/Gecko-Digital-Solutions/laravel-routes/actions/workflows/ci.yml/badge.svg)](https://github.com/Gecko-Digital-Solutions/laravel-routes/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/Gecko-Digital-Solutions/laravel-routes/badge.svg?branch=main)](https://coveralls.io/github/Gecko-Digital-Solutions/laravel-routes?branch=main)

</div>

# Laravel Routes



## Overview

**Laravel Routes** is a lightweight Laravel package that helps you organize application routes by feature or functionality instead of keeping them all in a single centralized file. This approach promotes better code organization, maintainability, and scalability in larger Laravel projects.

### Why Use Laravel Routes?

- Feature-Based Organization: Group related routes by feature or module
- Modular Structure: Each feature can have its own route definitions
- Easy Configuration: Simple configuration to define where routes are located
- Well Tested: Comprehensive test suite with 27 passing tests
- Production Ready: Stable and reliable for production applications

## Requirements

- PHP 8.2 or higher
- Laravel 10.0 or higher
- Composer

## Installation

Install the package via Composer:

```bash
composer require geckods/laravel-routes
```

The package will be automatically registered through Laravel's auto-discovery feature. No manual service provider registration is needed.

## Configuration

### Publish Config File

Publish the configuration file to your application:

```bash
php artisan vendor:publish --provider='GeckoDS\LaravelRoutes\Providers\LaravelRoutesProvider' --tag='config'
```

This creates `config/routes.php` with the following options:

```php
return [
    // Path to search for route classes (supports glob patterns)
    'path' => app_path('Http/**/*.php'),
    
    // Namespace prefix for discovered route classes
    'prefix_namespace' => 'App\\',
    
    // Additional route classes not found in the path above
    'other_routes' => [],
];
```

### Auto-Publishing on Update

To automatically publish the config on each Composer update, add this to your `composer.json`:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan vendor:publish --provider='GeckoDS\\LaravelRoutes\\Providers\\LaravelRoutesProvider' --tag='config' --ansi"
        ]
    }
}
```

## Usage

### Basic Setup

In your route files (e.g., `routes/api.php` or `routes/web.php`), initialize the route controller:

```php
<?php

use GeckoDS\LaravelRoutes\Routes\RouteController;

// Load and execute all routes
(new RouteController())->handle();
```

### Creating Route Classes

Create route classes by extending `AbstractRouteController`:

```php
<?php

namespace App\Http\Routes;

use Illuminate\Support\Facades\Route;
use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;

class UserRoutes extends AbstractRouteController
{
    public function handle()
    {
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/{id}', [UserController::class, 'show']);
            Route::put('/users/{id}', [UserController::class, 'update']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
        });
    }
}
```

Another example with different middleware:

```php
<?php

namespace App\Http\Routes;

use Illuminate\Support\Facades\Route;
use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;

class PublicRoutes extends AbstractRouteController
{
    public function handle()
    {
        Route::group(['middleware' => 'throttle:60,1'], function () {
            Route::get('/posts', [PostController::class, 'index']);
            Route::get('/posts/{id}', [PostController::class, 'show']);
        });
    }
}
```

## How Route Discovery Works

The package uses glob patterns to discover route files. Here's how it works:

1. Uses the `path` config to find PHP files
2. Converts file paths to class names using `prefix_namespace` config
3. Requires the file and checks if the class exists
4. Verifies the class extends `AbstractRouteController`
5. Instantiates the class and calls `handle()`

### Example Mapping

With default config:
- Config path: `app_path('Http/**/*.php')`
- Config prefix_namespace: `App\\`

A file at `app/Http/Routes/UserRoutes.php` containing class `App\Http\Routes\UserRoutes` will be discovered.

## Configuration Options Explained

### `path`

Defines where the package searches for route classes. Supports standard glob patterns.

The glob patterns work as follows:
- `*` matches any characters except path separators
- `**` matches any characters including path separators (recursive)
- `?` matches any single character

Examples:

```php
// Single directory (non-recursive)
'path' => app_path('Http/Routes/*.php'),

// Recursive directory
'path' => app_path('Http/**/*.php'),

// Specific pattern
'path' => app_path('Http/Routes/*Routes.php'),

// Custom location
'path' => app_path('Features/*/Routes/*.php'),
```

### `prefix_namespace`

The namespace prefix that will be prepended to the discovered class names. This must match your actual class namespaces.

When the package discovers a file, it:
1. Gets the relative path from app directory
2. Replaces slashes with backslashes
3. Removes the .php extension
4. Prepends the prefix_namespace

Examples:

```php
// For classes in App\Http\Routes\*
'prefix_namespace' => 'App\\Http\\Routes\\',

// For classes in App\*
'prefix_namespace' => 'App\\',

// For classes in App\Features\*\Routes\*
'prefix_namespace' => 'App\\Features\\',
```

### `other_routes`

Manually register route classes that either:
- Are not located in the path specified above
- Should not be auto-discovered for some reason

Usage:

```php
'other_routes' => [
    App\Http\Routes\AdminRoutes::class,
    App\Http\Routes\WebhookRoutes::class,
],
```

## Project Structure Example

A typical project structure might look like:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── PostController.php
│   │   ├── UserController.php
│   │   └── CommentController.php
│   └── Routes/
│       ├── PostRoutes.php
│       ├── UserRoutes.php
│       └── CommentRoutes.php
└── Features/
    ├── Notifications/
    │   ├── Controllers/
    │   │   └── NotificationController.php
    │   └── Routes/
    │       └── NotificationRoutes.php
    └── Payments/
        ├── Controllers/
        │   └── PaymentController.php
        └── Routes/
            └── PaymentRoutes.php

routes/
├── api.php
├── web.php
└── console.php
```

Then in `routes/api.php`:

```php
<?php

use GeckoDS\LaravelRoutes\Routes\RouteController;

Route::prefix('api')->group(function () {
    (new RouteController())->handle();
});
```

With config:

```php
'path' => app_path('Http/**/*.php'),
'prefix_namespace' => 'App\\',
```

The package will discover and execute:
- `App\Http\Routes\PostRoutes`
- `App\Http\Routes\UserRoutes`
- `App\Http\Routes\CommentRoutes`
- `App\Features\Notifications\Routes\NotificationRoutes`
- `App\Features\Payments\Routes\PaymentRoutes`

## Advanced Usage

### Conditional Route Registration

Register routes conditionally based on environment or feature flags:

```php
<?php

namespace App\Http\Routes;

use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;
use Illuminate\Support\Facades\Route;

class FeatureRoutes extends AbstractRouteController
{
    public function handle()
    {
        if (config('features.new_api')) {
            Route::prefix('v2')->group(function () {
                Route::get('/data', [DataController::class, 'index']);
            });
        }
    }
}
```

### Nested Route Groups

Organize complex route hierarchies:

```php
<?php

namespace App\Http\Routes;

use GeckoDS\LaravelRoutes\Routes\AbstractRouteController;
use Illuminate\Support\Facades\Route;

class AdminRoutes extends AbstractRouteController
{
    public function handle()
    {
        Route::prefix('admin')->middleware('admin')->group(function () {
            Route::prefix('users')->group(function () {
                Route::get('/', [AdminUserController::class, 'index']);
                Route::post('/', [AdminUserController::class, 'store']);
            });
            
            Route::prefix('settings')->group(function () {
                Route::get('/', [AdminSettingsController::class, 'index']);
                Route::post('/', [AdminSettingsController::class, 'update']);
            });
        });
    }
}
```

## Testing

The package includes a comprehensive test suite with 27 tests covering:

- Route discovery and execution
- Configuration validation
- Service provider integration
- Edge cases and error handling

### Running Tests

```bash
# Run all tests
composer test

# Or use PHPUnit directly
vendor/bin/phpunit

# Run specific test class
vendor/bin/phpunit tests/Unit/RouteControllerTest.php

# Run tests matching pattern
vendor/bin/phpunit --filter "testDiscovers"

# Generate code coverage report
vendor/bin/phpunit --coverage-html=coverage
```

For detailed testing documentation, see [TESTING.md](TESTING.md).

## Documentation

- [TESTS.md](TESTS.md) - Comprehensive test suite documentation
- [TESTING.md](TESTING.md) - Test execution guide with CI/CD examples
- [LICENSE](LICENSE) - MIT License

## Migration from Other Packages

If you are migrating from centralized route files:

1. Create route classes in your desired structure
2. Extend `AbstractRouteController` with your route definitions
3. Update route files to use `RouteController::handle()`
4. Test thoroughly to ensure all routes work as expected

## Performance Considerations

- Route discovery uses glob patterns and class introspection
- Discovered routes are processed once per request
- For large projects, consider grouping routes logically
- Use `other_routes` config for routes outside the standard path

## Troubleshooting

### Routes Not Being Discovered

1. Check that your route class extends `AbstractRouteController`
2. Verify the `path` config matches your route class locations
3. Ensure the `prefix_namespace` config is correct
4. Run `php artisan config:clear` to clear cached config
5. Verify the class namespace matches what the discovery algorithm produces

### Namespace Errors

1. Verify class namespace matches `prefix_namespace` config
2. Check that files are in the location specified by `path` config
3. Ensure autoloader is updated: `composer dump-autoload`

### Routes Not Executing

1. Confirm `RouteController::handle()` is called in your route files
2. Check middleware order and authentication requirements
3. Verify route controller has a valid `handle()` method

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request to the [GitHub repository](https://github.com/Gecko-Digital-Solutions/laravel-routes).

### Development Setup

```bash
# Clone the repository
git clone https://github.com/Gecko-Digital-Solutions/laravel-routes.git

# Install dependencies
composer install

# Run tests
composer test
```

## Support

For issues, questions, or suggestions, please open an issue on the [GitHub repository](https://github.com/Gecko-Digital-Solutions/laravel-routes/issues).

## License

This package is open source software licensed under the [MIT license](LICENSE).

## About

Created and maintained by [Gecko Digital Solutions](https://geckods.com).

Last Updated: November 2025
Version: 1.0.0
