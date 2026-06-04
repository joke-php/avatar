<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Http\Response;

use Vasoft\Joke\Http\Response\BinaryResponse;

/**
 * HTTP-ответ для передачи PNG-изображений.
 *
 * Используется для отправки сгенерированных аватаров клиенту.
 * Устанавливает Content-Type: image/png и корректные заголовки кэширования.
 * В отличие от BinaryResponse, не принуждает к скачиванию (inline, а не attachment).
 */
class ImageResponse extends BinaryResponse
{
    /**
     * Возвращает MIME-тип изображения.
     *
     * @return string Content-Type для PNG
     */
    public function getContentType(): string
    {
        return 'image/png';
    }

    /**
     * Отправляет ответ клиенту.
     *
     * Переопределяет родительский метод, чтобы установить Content-Disposition: inline
     * вместо attachment — изображение будет отображаться в браузере, а не скачиваться.
     *
     * @return $this
     */
    public function send(): static
    {
        $this->headers->setContentType($this->getContentType());
        $this->headers->set('Content-Length', (string) strlen($this->body));
        $this->headers->set('Content-Disposition', sprintf('inline; filename="%s"', $this->filename));

        $this->sendHeaders();
        echo $this->getBodyAsString();

        return $this;
    }
}