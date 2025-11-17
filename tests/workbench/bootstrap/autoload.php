<?php

// Defines for the workbench application
if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

// Register PSR-4 classes
$loader = require __DIR__ . '/../../vendor/autoload.php';

// Ensure the Routes namespace exists in our testbench app
if (!is_dir(__DIR__ . '/app')) {
    mkdir(__DIR__ . '/app', 0755, true);
}

if (!is_dir(__DIR__ . '/app/Http')) {
    mkdir(__DIR__ . '/app/Http', 0755, true);
}

if (!is_dir(__DIR__ . '/app/Http/Routes')) {
    mkdir(__DIR__ . '/app/Http/Routes', 0755, true);
}

return $loader;
