<?php

declare(strict_types=1);

/**
 * Точка входа тестового приложения.
 *
 * Создаёт DI-контейнер, инициализирует приложение Joke
 * и обрабатывает входящий HTTP-запрос.
 */

use Vasoft\Joke\Application\Application;
use Vasoft\Joke\Container\ServiceContainer;
use Vasoft\Joke\Http\HttpRequest;

require_once __DIR__ . '/../vendor/autoload.php';

$basePath = dirname(__DIR__);
$container = new ServiceContainer();

$app = new Application(
    basePath: $basePath,
    serviceContainer: $container,
);

$request = HttpRequest::fromGlobals();

$app->handle($request);