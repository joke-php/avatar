<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\JokeAvatar\AvatarException;
use Vasoft\JokeAvatar\AvatarGenerationException;

/**
 * Тесты исключения AvatarGenerationException.
 *
 * @covers \Vasoft\JokeAvatar\AvatarGenerationException
 *
 * @internal
 */
final class AvatarGenerationExceptionTest extends TestCase
{
    /**
     * Проверяет, что AvatarGenerationException наследует AvatarException.
     */
    public function testExtendsAvatarException(): void
    {
        $exception = new AvatarGenerationException();
        self::assertInstanceOf(AvatarException::class, $exception);
    }

    /**
     * Проверяет сообщение по умолчанию.
     */
    public function testDefaultMessage(): void
    {
        $exception = new AvatarGenerationException();
        self::assertSame('Ошибка генерации изображения аватара', $exception->getMessage());
    }

    /**
     * Проверяет создание исключения с произвольным сообщением.
     */
    public function testCustomMessage(): void
    {
        $message = 'Не удалось создать изображение';
        $exception = new AvatarGenerationException($message);
        self::assertSame($message, $exception->getMessage());
    }

    /**
     * Проверяет создание исключения с кодом ошибки.
     */
    public function testErrorCode(): void
    {
        $code = 500;
        $exception = new AvatarGenerationException('Ошибка', $code);
        self::assertSame($code, $exception->getCode());
    }

    /**
     * Проверяет создание исключения с предыдущим исключением.
     */
    public function testPreviousException(): void
    {
        $previous = new \RuntimeException('GD library error');
        $exception = new AvatarGenerationException('Ошибка GD', 0, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
