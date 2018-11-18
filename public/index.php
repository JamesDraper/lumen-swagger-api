<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new App\SwaggerApplication(__DIR__ . '/../');

$app->configure('app');
$app->configure('logging');

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\ExceptionHandler::class
);

$app->run();
