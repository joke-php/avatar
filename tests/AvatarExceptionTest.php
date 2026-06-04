<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Exceptions\JokeException;
use Vasoft\JokeAvatar\AvatarException;

/**
 * Тесты базового исключения модуля AvatarException.
 *
 * @covers \Vasoft\JokeAvatar\AvatarException
 *
 * @internal
 */
final class AvatarExceptionTest extends TestCase
{
    /**
     * Проверяет, что AvatarException наследует JokeException.
     */
    public function testExtendsJokeException(): void
    {
        $exception = new AvatarException();
        self::assertInstanceOf(JokeException::class, $exception);
    }

    /**
     * Проверяет создание исключения с сообщением по умолчанию.
     */
    public function testDefaultMessage(): void
    {
        $exception = new AvatarException();
        self::assertSame('', $exception->getMessage());
    }

    /**
     * Проверяет создание исключения с произвольным сообщением.
     */
    public function testCustomMessage(): void
    {
        $message = 'Тестовое сообщение об ошибке';
        $exception = new AvatarException($message);
        self::assertSame($message, $exception->getMessage());
    }

    /**
     * Проверяет создание исключения с кодом ошибки.
     */
    public function testErrorCode(): void
    {
        $code = 42;
        $exception = new AvatarException('Ошибка', $code);
        self::assertSame($code, $exception->getCode());
    }

    /**
     * Проверяет создание исключения с предыдущим исключением.
     */
    public function testPreviousException(): void
    {
        $previous = new \RuntimeException('Предыдущая ошибка');
        $exception = new AvatarException('Ошибка', 0, $previous);
        self::assertSame($previous, $exception->getPrevious());
    }
}
