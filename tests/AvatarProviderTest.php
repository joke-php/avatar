<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Container\ServiceContainer;
use Vasoft\Joke\Contract\Container\DiContainerInterface;
use Vasoft\Joke\Contract\Provider\ConfigurableServiceProviderInterface;
use Vasoft\JokeAvatar\AvatarConfig;
use Vasoft\JokeAvatar\AvatarGenerator;
use Vasoft\JokeAvatar\AvatarProvider;

/**
 * Тесты сервис-провайдера AvatarProvider.
 *
 * @covers \Vasoft\JokeAvatar\AvatarProvider
 *
 * @internal
 */
final class AvatarProviderTest extends TestCase
{
    private AvatarProvider $provider;

    protected function setUp(): void
    {
        $container = self::createStub(DiContainerInterface::class);
        $this->provider = new AvatarProvider($container);
    }

    /**
     * Проверяет, что AvatarProvider реализует ConfigurableServiceProviderInterface.
     */
    public function testImplementsConfigurableServiceProviderInterface(): void
    {
        self::assertInstanceOf(ConfigurableServiceProviderInterface::class, $this->provider);
    }

    /**
     * Проверяет, что provides возвращает AvatarGenerator.
     */
    public function testProvidesReturnsAvatarGenerator(): void
    {
        $provides = $this->provider->provides();

        self::assertContains(AvatarGenerator::class, $provides);
        self::assertCount(1, $provides);
    }

    /**
     * Проверяет, что requires возвращает пустой массив.
     */
    public function testRequiresReturnsEmptyArray(): void
    {
        self::assertSame([], $this->provider->requires());
    }

    /**
     * Проверяет, что boot не выбрасывает исключений.
     */
    public function testBootDoesNotThrowException(): void
    {
        $this->provider->boot();

        // Если дошли сюда — исключения нет
        self::assertTrue(true);
    }

    /**
     * Проверяет, что provideConfigs возвращает AvatarConfig.
     */
    public function testProvideConfigsReturnsAvatarConfig(): void
    {
        $configs = AvatarProvider::provideConfigs();

        self::assertContains(AvatarConfig::class, $configs);
        self::assertCount(1, $configs);
    }

    /**
     * Проверяет, что buildConfig создаёт AvatarConfig.
     */
    public function testBuildConfigReturnsAvatarConfig(): void
    {
        $container = self::createStub(ServiceContainer::class);
        $config = AvatarProvider::buildConfig(AvatarConfig::class, $container);

        self::assertInstanceOf(AvatarConfig::class, $config);
    }

    /**
     * Проверяет, что register регистрирует AvatarGenerator как синглтон
     * и фабрика создаёт корректный экземпляр.
     */
    public function testRegisterCreatesAvatarGenerator(): void
    {
        $container = $this->createMock(DiContainerInterface::class);
        $container
            ->expects(self::never())
            ->method('get');
        $registeredFactory = null;
        $container
            ->expects(self::once())
            ->method('registerSingleton')
            ->with(
                AvatarGenerator::class,
                AvatarGenerator::class,
            )
            ->willReturnCallback(
                static function (string $class, string $concrete) use (&$registeredFactory, $container) {
                    $registeredFactory = $concrete;

                    return $container;
                },
            );

        $provider = new AvatarProvider($container);
        $provider->register();

        self::assertNotNull($registeredFactory);
    }
}
