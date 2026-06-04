<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

/**
 * Исключение, выбрасываемое при ошибках генерации изображения аватара.
 *
 * Возникает при сбоях в работе расширения GD:
 * - Недостаточно памяти для создания изображения
 * - Ошибка записи PNG-файла
 * - Ошибка выделения цветов
 * - Прочие ошибки GD-функций
 */
class AvatarGenerationException extends AvatarException
{
    /**
     * @param string          $message  Сообщение об ошибке
     * @param int             $code     Код ошибки
     * @param null|\Throwable $previous Предыдущее исключение
     */
    public function __construct(
        string $message = 'Ошибка генерации изображения аватара',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
