<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

use Vasoft\Joke\Config\AbstractConfig;
use Vasoft\Joke\Config\Environment;
use Vasoft\Joke\Container\ServiceContainer;
use Vasoft\Joke\Contract\Container\DiContainerInterface;
use Vasoft\Joke\Contract\Provider\ConfigurableServiceProviderInterface;
use Vasoft\Joke\Provider\AbstractProvider;

/**
 * Сервис-провайдер модуля генерации аватаров.
 *
 * Регистрирует AvatarGenerator в DI-контейнере как синглтон
 * и предоставляет конфигурацию AvatarConfig.
 *
 * @see AbstractProvider
 * @see ConfigurableServiceProviderInterface
 * @see AvatarGenerator
 * @see AvatarConfig
 */
class AvatarProvider extends AbstractProvider implements ConfigurableServiceProviderInterface
{
    /**
     * @param DiContainerInterface $container Контейнер зависимостей (внедряется через DI)
     */
    public function __construct(
        private readonly DiContainerInterface $container,
    ) {}

    /**
     * Регистрирует сервисы модуля в DI-контейнере.
     *
     * Регистрирует AvatarGenerator как синглтон. При создании экземпляра
     * AvatarGenerator используются AvatarConfig и Environment из контейнера.
     */
    public function register(): void
    {
        $this->container->registerSingleton(
            AvatarGenerator::class,
            function (): AvatarGenerator {
                $config = $this->container->get(AvatarConfig::class);
                $env = $this->container->get(Environment::class);

                return new AvatarGenerator($config, $env);
            },
        );
    }

    /**
     * Возвращает список сервисов, предоставляемых провайдером.
     *
     * @return array<int, class-string> Список классов, зарегистрированных провайдером
     */
    public function provides(): array
    {
        return [
            AvatarGenerator::class,
        ];
    }

    /**
     * Возвращает список зависимостей от других провайдеров.
     *
     * @return array<int, class-string> Список классов провайдеров, от которых зависит данный
     */
    public function requires(): array
    {
        return [];
    }

    /**
     * Выполняет действия после регистрации всех провайдеров.
     *
     * Используется для настройки middleware, маршрутов и других действий,
     * требующих полной инициализации контейнера.
     */
    public function boot(): void {}

    /**
     * Возвращает список классов конфигурации, предоставляемых провайдером.
     *
     * @return array<int, class-string<AbstractConfig>> Список классов конфигурации
     */
    public static function provideConfigs(): array
    {
        return [
            AvatarConfig::class,
        ];
    }

    /**
     * Создаёт экземпляр конфигурации AvatarConfig со значениями по умолчанию.
     *
     * @param string          $configClass Полное имя класса конфигурации
     * @param ServiceContainer $container  Контейнер зависимостей
     *
     * @return AvatarConfig Конфигурация модуля с настройками по умолчанию
     */
    public static function buildConfig(string $configClass, ServiceContainer $container): AvatarConfig
    {
        return new AvatarConfig();
    }
}