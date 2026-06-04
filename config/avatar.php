<?php

declare(strict_types=1);

/**
 * Конфигурация генерации аватаров.
 *
 * Файл возвращает ассоциативный массив, где ключ — полное имя класса конфигурации,
 * а значение — замыкание, возвращающее экземпляр AvatarConfig.
 * В замыкании доступна переменная $env (Environment).
 *
 * @var Vasoft\Joke\Config\Environment $env
 */

use Vasoft\JokeAvatar\AvatarConfig;

return [
    AvatarConfig::class => static function () use ($env): AvatarConfig {
        return (new AvatarConfig())
            ->setSize(200)
            ->setThemes([
                'default' => [
                    'background_colors'    => ['#3498db', '#2980b9', '#1abc9c', '#16a085'],
                    'text_color'           => '#ffffff',
                    'decorative_colors'    => ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'],
                    'decorative_min'       => 3,
                    'decorative_max'       => 6,
                    'decorative_size_min'  => 20,
                    'decorative_size_max'  => 60,
                ],
                'dark' => [
                    'background_colors'    => ['#2c3e50', '#34495e', '#1a1a2e', '#16213e'],
                    'text_color'           => '#ecf0f1',
                    'decorative_colors'    => ['#e74c3c', '#c0392b', '#f39c12', '#d35400'],
                    'decorative_min'       => 2,
                    'decorative_max'       => 5,
                    'decorative_size_min'  => 15,
                    'decorative_size_max'  => 50,
                ],
                'warm' => [
                    'background_colors'    => ['#e74c3c', '#c0392b', '#e67e22', '#d35400'],
                    'text_color'           => '#fff5e6',
                    'decorative_colors'    => ['#f1c40f', '#f39c12', '#e67e22', '#d35400'],
                    'decorative_min'       => 3,
                    'decorative_max'       => 7,
                    'decorative_size_min'  => 10,
                    'decorative_size_max'  => 40,
                ],
                'nature' => [
                    'background_colors'    => ['#27ae60', '#2ecc71', '#1abc9c', '#16a085'],
                    'text_color'           => '#f0fff0',
                    'decorative_colors'    => ['#8e44ad', '#9b59b6', '#3498db', '#2980b9'],
                    'decorative_min'       => 2,
                    'decorative_max'       => 4,
                    'decorative_size_min'  => 25,
                    'decorative_size_max'  => 70,
                ],
                'ocean' => [
                    'background_colors'    => ['#006994', '#0077b6', '#00b4d8', '#48cae4'],
                    'text_color'           => '#ffffff',
                    'decorative_colors'    => ['#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff'],
                    'decorative_min'       => 4,
                    'decorative_max'       => 8,
                    'decorative_size_min'  => 15,
                    'decorative_size_max'  => 45,
                ],
                'sunset' => [
                    'background_colors'    => ['#ff6b6b', '#ee5a24', '#f0932b', '#f9ca24'],
                    'text_color'           => '#2d3436',
                    'decorative_colors'    => ['#6c5ce7', '#a29bfe', '#fd79a8', '#e17055'],
                    'decorative_min'       => 3,
                    'decorative_max'       => 6,
                    'decorative_size_min'  => 20,
                    'decorative_size_max'  => 55,
                ],
            ]);
    },
];