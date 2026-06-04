<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;

/**
 * Тесты HTTP-ответа ImageResponse.
 *
 * @covers \Vasoft\JokeAvatar\Http\Response\ImageResponse
 */
final class ImageResponseTest extends TestCase
{
    private ImageResponse $response;

    protected function setUp(): void
    {
        $this->response = new ImageResponse();
    }

    /**
     * Проверяет, что getContentType возвращает image/png.
     */
    public function testGetContentTypeReturnsPng(): void
    {
        self::assertSame('image/png', $this->response->getContentType());
    }

    /**
     * Проверяет, что класс наследует BinaryResponse.
     */
    public function testExtendsBinaryResponse(): void
    {
        self::assertInstanceOf(\Vasoft\Joke\Http\Response\BinaryResponse::class, $this->response);
    }
}