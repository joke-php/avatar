<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Config\Environment;
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
 */
final class AvatarProviderTest extends TestCase
{
    private AvatarProvider $provider;
    private DiContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(DiContainerInterface::class);
        $this->provider = new AvatarProvider($this->container);
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
        $container = $this->createMock(ServiceContainer::class);
        $config = AvatarProvider::buildConfig(AvatarConfig::class, $container);

        self::assertInstanceOf(AvatarConfig::class, $config);
    }

    /**
     * Проверяет, что register регистрирует AvatarGenerator как синглтон
     * и фабрика создаёт корректный экземпляр.
     */
    public function testRegisterCreatesAvatarGenerator(): void
    {
        $avatarConfig = new AvatarConfig();
        $env = $this->createStub(Environment::class);

        $container = $this->createMock(DiContainerInterface::class);
        $container
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturnMap([
                [AvatarConfig::class, $avatarConfig],
                [Environment::class, $env],
            ]);

        $registeredFactory = null;
        $container
            ->expects(self::once())
            ->method('registerSingleton')
            ->with(
                AvatarGenerator::class,
                self::isInstanceOf(\Closure::class),
            )
            ->willReturnCallback(function (string $class, \Closure $factory) use (&$registeredFactory): void {
                $registeredFactory = $factory;
            });

        $provider = new AvatarProvider($container);
        $provider->register();

        self::assertNotNull($registeredFactory);
        $generator = $registeredFactory();

        self::assertInstanceOf(AvatarGenerator::class, $generator);
    }

    /**
     * Проверяет, что register выбрасывает RuntimeException при неверном типе AvatarConfig.
     */
    public function testRegisterThrowsRuntimeExceptionForInvalidConfig(): void
    {
        $container = $this->createMock(DiContainerInterface::class);
        $container
            ->method('get')
            ->willReturnMap([
                [AvatarConfig::class, new \stdClass()],
                [Environment::class, $this->createStub(Environment::class)],
            ]);

        $registeredFactory = null;
        $container
            ->method('registerSingleton')
            ->with(
                AvatarGenerator::class,
                self::isInstanceOf(\Closure::class),
            )
            ->willReturnCallback(function (string $class, \Closure $factory) use (&$registeredFactory): void {
                $registeredFactory = $factory;
            });

        $provider = new AvatarProvider($container);
        $provider->register();

        self::assertNotNull($registeredFactory);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ожидался экземпляр ' . AvatarConfig::class);

        $registeredFactory();
    }

    /**
     * Проверяет, что register выбрасывает RuntimeException при неверном типе Environment.
     */
    public function testRegisterThrowsRuntimeExceptionForInvalidEnvironment(): void
    {
        $container = $this->createMock(DiContainerInterface::class);
        $container
            ->method('get')
            ->willReturnMap([
                [AvatarConfig::class, new AvatarConfig()],
                [Environment::class, new \stdClass()],
            ]);

        $registeredFactory = null;
        $container
            ->method('registerSingleton')
            ->with(
                AvatarGenerator::class,
                self::isInstanceOf(\Closure::class),
            )
            ->willReturnCallback(function (string $class, \Closure $factory) use (&$registeredFactory): void {
                $registeredFactory = $factory;
            });

        $provider = new AvatarProvider($container);
        $provider->register();

        self::assertNotNull($registeredFactory);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ожидался экземпляр ' . Environment::class);

        $registeredFactory();
    }
}