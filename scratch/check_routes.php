<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = Route::getRoutes();
$uris = [];
$names = [];

foreach ($routes as $route) {
    $methods = implode('|', $route->methods());
    $uri = $route->uri();
    $name = $route->getName();
    
    $key = $methods . ' ' . $uri;
    if (isset($uris[$key])) {
        echo "Duplicate URI: [$methods] $uri\n";
    }
    $uris[$key] = true;
    
    if ($name) {
        if (isset($names[$name])) {
            echo "Duplicate Name: $name (at $uri)\n";
        }
        $names[$name] = true;
    }
}
