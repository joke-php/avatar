<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Controller;

use Vasoft\Joke\Http\HttpRequest;
use Vasoft\Joke\Http\Response\ResponseBuilder;
use Vasoft\JokeAvatar\AvatarGenerator;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;

/**
 * Контроллер для тестовой генерации аватара.
 *
 * Обрабатывает GET-запросы по маршруту /avatar/{nickname}
 * и возвращает сгенерированное PNG-изображение.
 *
 * Параметр nickname извлекается из маршрута, theme — из query-параметра.
 */
readonly class AvatarController
{
    /**
     * @param AvatarGenerator $generator Сервис генерации аватаров
     * @param HttpRequest $request Текущий HTTP-запрос (регистрируется в контейнере перед вызовом контроллера)
     */
    public function __construct(
        private AvatarGenerator $generator,
        private HttpRequest $request,
        private ResponseBuilder $builder,
    ) {
    }

    /**
     * Обрабатывает запрос на генерацию аватара.
     *
     * Параметр nickname извлекается из маршрута (через props запроса),
     * theme — из query-параметра ?theme= (опционально).
     *
     * @param string $nickname Имя пользователя (из маршрута)
     *
     * @return ImageResponse Ответ с PNG-изображением
     */
    public function __invoke(string $nickname): ImageResponse
    {
        $nickname = rawurldecode($nickname);
        $theme = $this->request->get->getString('theme', '');
        $path = $this->generator->generate($nickname, '' !== $theme ? $theme : null);

        return $this->builder->buildResponse(ImageResponse::class)->load($path);
    }
}
