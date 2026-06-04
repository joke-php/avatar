<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

use Vasoft\Joke\Exceptions\JokeException;

/**
 * Базовое исключение модуля генерации аватаров.
 *
 * Является родительским для всех исключений, выбрасываемых в модуле joke-avatar.
 * Наследует Vasoft\Joke\Exceptions\JokeException для корректной обработки
 * фреймворком Joke через ExceptionMiddleware.
 */
class AvatarException extends JokeException
{
}