<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Controller;

use Vasoft\Joke\Http\Cookies\CookieConfig;
use Vasoft\Joke\Http\Response\JsonResponse;

/**
 * Контроллер для проверки работоспособности сервиса (healthcheck).
 *
 * Используется Docker HEALTHCHECK и мониторинговыми системами.
 * Возвращает JSON с состоянием сервиса.
 */
class HealthController
{
    /**
     * Обрабатывает GET /health запрос.
     *
     * @return JsonResponse JSON с состоянием сервиса
     */
    public function __invoke(): JsonResponse
    {
        $data = [
            'status' => 'ok',
            'service' => 'joke-avatar',
            'timestamp' => date('c'),
        ];

        $response = new JsonResponse(new CookieConfig());
        $response->setBody($data);

        return $response;
    }
}
