<?php

declare(strict_types=1);

/**
 * Файл маршрутов тестового приложения.
 *
 * Доступные переменные:
 * @var  Vasoft\Joke\Routing\Router $router маршрутизатор приложения
 *
 * Маршруты автоматически получают группу 'web' (StdGroup::WEB).
 */

use Vasoft\JokeAvatar\Controller\AvatarController;
use Vasoft\JokeAvatar\Controller\HealthController;

/*
 * GET /health
 *
 * Эндпоинт для проверки работоспособности сервиса.
 * Используется Docker HEALTHCHECK и мониторинговыми системами.
 * Возвращает JSON { "status": "ok", "service": "joke-avatar", "timestamp": "..." }.
 */
$router->get(
    path: '/health',
    handler: HealthController::class,
    name: 'avatar.health',
);

/*
 * GET /avatar/{nickname}
 *
 * Генерирует аватар для указанного nickname.
 * Опциональный query-параметр ?theme=default|dark|warm|nature|ocean|sunset
 *
 * Примеры:
 *   /avatar/Alice
 *   /avatar/Bob?theme=dark
 *   /avatar/Charlie?theme=ocean
 */
$router->get(
    path: '/avatar/{nickname}',
    handler: AvatarController::class,
    name: 'avatar.generate',
);