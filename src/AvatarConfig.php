<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

use Vasoft\Joke\Config\AbstractConfig;

/**
 * Конфигурация модуля генерации аватаров.
 *
 * Хранит настройки размера аватара и список доступных тем оформления.
 * После загрузки конфигурация замораживается (immutable).
 *
 * @see AbstractConfig
 */
class AvatarConfig extends AbstractConfig
{
    /**
     * Размер аватара в пикселях (ширина и высота квадрата).
     */
    private int $size = 200;

    /**
     * Массив тем оформления.
     * Ключ — название темы, значение — массив параметров для ThemeConfig.
     *
     * @var array<string, array{
     *     background_colors: list<string>,
     *     text_color: string,
     *     decorative_colors: list<string>,
     *     decorative_min: int,
     *     decorative_max: int,
     *     decorative_size_min: int,
     *     decorative_size_max: int
     * }>
     */
    private array $themes = [
        'default' => [
            'background_colors' => ['#E8F4F8', '#F0E6F6', '#E6F0FA', '#FFF3E0', '#F3E5F5'],
            'text_color' => '#2C3E50',
            'decorative_colors' => ['#BBDEFB', '#C8E6C9', '#FFE0B2', '#E1BEE7', '#B2EBF2'],
            'decorative_min' => 3,
            'decorative_max' => 8,
            'decorative_size_min' => 10,
            'decorative_size_max' => 30,
        ],
        'dark' => [
            'background_colors' => ['#2C3E50', '#34495E', '#1A252F', '#212F3D'],
            'text_color' => '#ECF0F1',
            'decorative_colors' => ['#5D6D7E', '#7F8C8D', '#85929E', '#AEB6BF'],
            'decorative_min' => 2,
            'decorative_max' => 6,
            'decorative_size_min' => 10,
            'decorative_size_max' => 25,
        ],
        'warm' => [
            'background_colors' => ['#FFF3E0', '#FFECB3', '#FFE0B2', '#FFCC80'],
            'text_color' => '#4E342E',
            'decorative_colors' => ['#FFAB91', '#FFCCBC', '#D7CCC8', '#BCAAA4'],
            'decorative_min' => 3,
            'decorative_max' => 7,
            'decorative_size_min' => 8,
            'decorative_size_max' => 28,
        ],
        'nature' => [
            'background_colors' => ['#E8F5E9', '#C8E6C9', '#DCEDC8', '#F1F8E9'],
            'text_color' => '#1B5E20',
            'decorative_colors' => ['#A5D6A7', '#81C784', '#66BB6A', '#C5E1A5'],
            'decorative_min' => 2,
            'decorative_max' => 5,
            'decorative_size_min' => 15,
            'decorative_size_max' => 35,
        ],
    ];

    /**
     * Устанавливает размер аватара в пикселях.
     *
     * @param int $size Размер в пикселях (от 50 до 1000)
     *
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->guard();
        $this->size = max(50, min(1000, $size));

        return $this;
    }

    /**
     * Возвращает размер аватара в пикселях.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Устанавливает массив тем оформления.
     *
     * @param array<string, array{
     *     background_colors: list<string>,
     *     text_color: string,
     *     decorative_colors: list<string>,
     *     decorative_min: int,
     *     decorative_max: int,
     *     decorative_size_min: int,
     *     decorative_size_max: int
     * }> $themes Массив тем
     *
     * @return $this
     */
    public function setThemes(array $themes): self
    {
        $this->guard();
        $this->themes = $themes;

        return $this;
    }

    /**
     * Возвращает массив всех тем оформления.
     *
     * @return array<string, array{
     *     background_colors: list<string>,
     *     text_color: string,
     *     decorative_colors: list<string>,
     *     decorative_min: int,
     *     decorative_max: int,
     *     decorative_size_min: int,
     *     decorative_size_max: int
     * }>
     */
    public function getThemes(): array
    {
        return $this->themes;
    }

    /**
     * Возвращает параметры указанной темы.
     *
     * @param string $themeName Название темы
     *
     * @return null|array Массив параметров темы или null, если тема не найдена
     */
    public function getTheme(string $themeName): ?array
    {
        return $this->themes[$themeName] ?? null;
    }

    /**
     * Проверяет, существует ли тема с указанным именем.
     *
     * @param string $themeName Название темы
     */
    public function hasTheme(string $themeName): bool
    {
        return isset($this->themes[$themeName]);
    }
}
