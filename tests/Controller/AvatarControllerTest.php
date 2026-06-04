<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Collections\PropsCollection;
use Vasoft\Joke\Http\HttpRequest;
use Vasoft\JokeAvatar\AvatarGenerator;
use Vasoft\JokeAvatar\Controller\AvatarController;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;

/**
 * Тесты контроллера AvatarController.
 *
 * @covers \Vasoft\JokeAvatar\Controller\AvatarController
 *
 * @internal
 */
final class AvatarControllerTest extends TestCase
{
    private AvatarGenerator $generator;
    private HttpRequest $request;
    private AvatarController $controller;

    protected function setUp(): void
    {
        $this->generator = $this->createMock(AvatarGenerator::class);
        $this->request = new HttpRequest([], [], [], [], []);
        $this->controller = new AvatarController($this->generator, $this->request);
    }

    /**
     * Устанавливает значение приватного свойства get через рефлексию.
     */
    private function setGetCollection(PropsCollection $collection): void
    {
        $reflection = new \ReflectionProperty(HttpRequest::class, 'get');
        $reflection->setValue($this->request, $collection);
    }

    /**
     * Создаёт временный PNG-файл для тестов.
     *
     * @return string Путь к созданному файлу
     */
    private function createTempPng(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'avatar-test-') . '.png';
        $image = imagecreatetruecolor(10, 10);
        imagepng($image, $path);

        return $path;
    }

    /**
     * Проверяет, что __invoke возвращает ImageResponse.
     */
    public function testInvokeReturnsImageResponse(): void
    {
        $nickname = 'TestUser';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturn('');

        $this->setGetCollection($getCollection);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with($nickname, null)
            ->willReturn($pngPath);

        $result = ($this->controller)($nickname);

        self::assertInstanceOf(ImageResponse::class, $result);

        @unlink($pngPath);
    }

    /**
     * Проверяет, что __invoke передаёт тему из query-параметра.
     */
    public function testInvokePassesThemeFromQuery(): void
    {
        $nickname = 'TestUser';
        $theme = 'dark';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturnCallback(static fn(string $key, string $default = '') => 'theme' === $key ? $theme : $default);

        $this->setGetCollection($getCollection);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with($nickname, $theme)
            ->willReturn($pngPath);

        $result = ($this->controller)($nickname);

        self::assertInstanceOf(ImageResponse::class, $result);

        @unlink($pngPath);
    }

    /**
     * Проверяет, что __invoke декодирует URL-encoded nickname.
     */
    public function testInvokeDecodesUrlEncodedNickname(): void
    {
        $encodedNickname = '%D0%90%D0%BB%D0%B5%D0%BA%D1%81%D0%B5%D0%B9';
        $decodedNickname = 'Алексей';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturn('');

        $this->setGetCollection($getCollection);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with($decodedNickname, null)
            ->willReturn($pngPath);

        $result = ($this->controller)($encodedNickname);

        self::assertInstanceOf(ImageResponse::class, $result);

        @unlink($pngPath);
    }

    /**
     * Проверяет, что __invoke передаёт null, если тема пустая строка.
     */
    public function testInvokePassesNullForEmptyTheme(): void
    {
        $nickname = 'TestUser';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturn('');

        $this->setGetCollection($getCollection);

        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with($nickname, null)
            ->willReturn($pngPath);

        ($this->controller)($nickname);

        @unlink($pngPath);
    }
}
