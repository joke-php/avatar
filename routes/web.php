<?php

declare(strict_types=1);

/**
 * Файл маршрутов тестового приложения.
 *
 * Доступные переменные:
 *   - $router (Vasoft\Joke\Routing\Router) — маршрутизатор приложения
 *   - $this   (RouterServiceProvider) — сервис-провайдер маршрутизации
 *
 * Маршруты автоматически получают группу 'web' (StdGroup::WEB).
 */

use Vasoft\JokeAvatar\Controller\AvatarController;

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