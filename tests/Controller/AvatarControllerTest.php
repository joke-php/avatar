<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests\Controller;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use Vasoft\Joke\Collections\PropsCollection;
use Vasoft\Joke\Http\HttpRequest;
use Vasoft\Joke\Http\Response\ResponseBuilder;
use Vasoft\JokeAvatar\AvatarGenerator;
use Vasoft\JokeAvatar\Controller\AvatarController;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;
use Vasoft\JokeAvatar\Tests\ModuleTestCase;

/**
 * Тесты контроллера AvatarController.
 *
 * @covers \Vasoft\JokeAvatar\Controller\AvatarController
 *
 * @internal
 */
final class AvatarControllerTest extends ModuleTestCase
{
    private AvatarGenerator|MockObject $generator;
    private HttpRequest $request;
    private AvatarController $controller;
    private ResponseBuilder|MockObject $builder;

    protected function setUp(): void
    {
        $this->makeBase('controller');
        parent::setUp();

        $this->builder = $this->createMock(ResponseBuilder::class);
        $this->generator = $this->createMock(AvatarGenerator::class);
        $this->request = new HttpRequest([], [], [], [], []);
        $this->controller = new AvatarController($this->generator, $this->request, $this->builder);
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

    #[TestDox('Проверяет, что __invoke возвращает ImageResponse.')]
    public function testInvokeReturnsImageResponse(): void
    {
        $nickname = 'TestUser';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturn('');

        $this->setGetCollection($getCollection);

        $response = self::createStub(ImageResponse::class);
        $this->builder
            ->expects(self::once())
            ->method('buildResponse')
            ->with(ImageResponse::class)
            ->willReturn($response);
        $this->generator
            ->expects(self::once())
            ->method('generate')
            ->with($nickname, null)
            ->willReturn($pngPath);

        $result = ($this->controller)($nickname);

        self::assertInstanceOf(ImageResponse::class, $result);

        @unlink($pngPath);
    }

    #[TestDox('Проверяет, что __invoke передаёт тему из query-параметра.')]
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
        $response = self::createStub(ImageResponse::class);

        $this->builder
            ->expects(self::once())
            ->method('buildResponse')
            ->with(ImageResponse::class)
            ->willReturn($response);

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
    #[AllowMockObjectsWithoutExpectations]
    public function testInvokeDecodesUrlEncodedNickname(): void
    {
        $encodedNickname = '%D0%90%D0%BB%D0%B5%D0%BA%D1%81%D0%B5%D0%B9';
        $pngPath = $this->createTempPng();

        $getCollection = self::createStub(PropsCollection::class);
        $getCollection
            ->method('getString')
            ->willReturn('');

        $this->setGetCollection($getCollection);
        $response = self::createStub(ImageResponse::class);
        $this->builder
            ->expects(self::once())
            ->method('buildResponse')
            ->with(ImageResponse::class)
            ->willReturn($response);

        $result = ($this->controller)($encodedNickname);

        self::assertInstanceOf(ImageResponse::class, $result);

        @unlink($pngPath);
    }
}
