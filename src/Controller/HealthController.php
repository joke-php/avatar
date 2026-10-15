<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Controller;

use Vasoft\Joke\Http\Cookies\CookieConfig;
use Vasoft\Joke\Http\Response\JsonResponse;
use Vasoft\Joke\Http\Response\ResponseBuilder;

/**
 * Контроллер для проверки работоспособности сервиса (healthcheck).
 *
 * Используется Docker HEALTHCHECK и мониторинговыми системами.
 * Возвращает JSON с состоянием сервиса.
 */
class HealthController
{
    public function __construct(
        private readonly ResponseBuilder $responseBuilder,
    ) {
    }

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
        /** @var JsonResponse $response */
        $response = $this->responseBuilder->buildResponse(JsonResponse::class);
        $response->setBody($data);

        return $response;
    }
}
