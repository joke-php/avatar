<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests\Http\Response;

use Vasoft\Joke\Http\Cookies\CookieCollection;
use Vasoft\Joke\Http\Cookies\CookieConfig;
use Vasoft\Joke\Http\Response\BinaryResponse;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;
use Vasoft\JokeAvatar\Tests\ModuleTestCase;

/**
 * Тесты HTTP-ответа ImageResponse.
 *
 * @coversDefaultClass \Vasoft\JokeAvatar\Http\Response\ImageResponse
 *
 * @internal
 */
final class ImageResponseTest extends ModuleTestCase
{
    private ImageResponse $response;

    protected function setUp(): void
    {
        $this->makeBase('response');
        parent::setUp();
        $cookies = new CookieCollection(new CookieConfig(), false);
        $this->response = new ImageResponse($cookies, $this->fs);
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
        self::assertInstanceOf(BinaryResponse::class, $this->response);
    }
}
