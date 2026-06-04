<?php

declare(strict_types=1);

/**
 * Конфигурация ядра приложения.
 *
 * Регистрирует сервис-провайдеры и указывает пути к конфигурационным файлам.
 * Файл выполняется в замыкании, где доступна переменная $env (Environment).
 *
 * @var Vasoft\Joke\Config\Environment $env
 */

use Vasoft\Joke\Application\KernelConfig;
use Vasoft\JokeAvatar\AvatarProvider;

$config = new KernelConfig();

// Регистрация сервис-провайдера библиотеки генерации аватаров
$config->addProvider(AvatarProvider::class);

// Путь к базовым конфигурациям (загружаются при старте приложения)
$config->setBaseConfigPath('config/');

// Путь к ленивым конфигурациям (загружаются по требованию)
$config->setLazyConfigPath('config/');

return $config;