<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\JokeAvatar\AvatarException;
use Vasoft\JokeAvatar\ThemeConfig;

/**
 * Тесты Value Object ThemeConfig.
 *
 * @covers \Vasoft\JokeAvatar\ThemeConfig
 *
 * @internal
 */
final class ThemeConfigTest extends TestCase
{
    /**
     * Базовая конфигурация темы для тестов.
     *
     * @var array<string, mixed>
     */
    private array $baseConfig = [
        'background_colors' => ['#E8F4F8', '#F0E6F6'],
        'text_color' => '#2C3E50',
        'decorative_colors' => ['#BBDEFB', '#C8E6C9'],
        'decorative_min' => 3,
        'decorative_max' => 8,
        'decorative_size_min' => 10,
        'decorative_size_max' => 30,
    ];

    /**
     * Проверяет успешное создание ThemeConfig с корректными параметрами.
     */
    public function testCreateWithValidConfig(): void
    {
        $config = new ThemeConfig($this->baseConfig);

        self::assertSame(['#E8F4F8', '#F0E6F6'], $config->getBackgroundColors());
        self::assertSame('#2C3E50', $config->getTextColor());
        self::assertSame(['#BBDEFB', '#C8E6C9'], $config->getDecorativeColors());
        self::assertSame(3, $config->getDecorativeMin());
        self::assertSame(8, $config->getDecorativeMax());
        self::assertSame(10, $config->getDecorativeSizeMin());
        self::assertSame(30, $config->getDecorativeSizeMax());
    }

    /**
     * Проверяет выброс исключения при отрицательном decorative_min.
     */
    public function testThrowsExceptionWhenDecorativeMinIsNegative(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('decorative_min не может быть отрицательным');

        $this->baseConfig['decorative_min'] = -1;
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения при decorative_min = 0 (допустимо).
     */
    public function testAllowsZeroDecorativeMin(): void
    {
        $this->baseConfig['decorative_min'] = 0;
        $config = new ThemeConfig($this->baseConfig);

        self::assertSame(0, $config->getDecorativeMin());
    }

    /**
     * Проверяет выброс исключения, когда decorative_max меньше decorative_min.
     */
    public function testThrowsExceptionWhenDecorativeMaxLessThanMin(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('decorative_max не может быть меньше decorative_min');

        $this->baseConfig['decorative_min'] = 5;
        $this->baseConfig['decorative_max'] = 3;
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения при decorative_size_min < 1.
     */
    public function testThrowsExceptionWhenDecorativeSizeMinLessThanOne(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Размер декоративных элементов должен быть в диапазоне 1-100%');

        $this->baseConfig['decorative_size_min'] = 0;
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения при decorative_size_max > 100.
     */
    public function testThrowsExceptionWhenDecorativeSizeMaxGreaterThan100(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Размер декоративных элементов должен быть в диапазоне 1-100%');

        $this->baseConfig['decorative_size_max'] = 101;
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения, когда decorative_size_max меньше decorative_size_min.
     */
    public function testThrowsExceptionWhenDecorativeSizeMaxLessThanMin(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('decorative_size_max не может быть меньше decorative_size_min');

        $this->baseConfig['decorative_size_min'] = 50;
        $this->baseConfig['decorative_size_max'] = 30;
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения при пустом массиве цветов фона.
     */
    public function testThrowsExceptionWhenBackgroundColorsIsEmpty(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Должен быть указан хотя бы один цвет фона');

        $this->baseConfig['background_colors'] = [];
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет выброс исключения при пустом массиве декоративных цветов.
     */
    public function testThrowsExceptionWhenDecorativeColorsIsEmpty(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Должен быть указан хотя бы один декоративный цвет');

        $this->baseConfig['decorative_colors'] = [];
        new ThemeConfig($this->baseConfig);
    }

    /**
     * Проверяет, что decorative_min и decorative_max могут быть равны.
     */
    public function testAllowsEqualMinAndMaxDecorative(): void
    {
        $this->baseConfig['decorative_min'] = 5;
        $this->baseConfig['decorative_max'] = 5;
        $config = new ThemeConfig($this->baseConfig);

        self::assertSame(5, $config->getDecorativeMin());
        self::assertSame(5, $config->getDecorativeMax());
    }

    /**
     * Проверяет, что decorative_size_min и decorative_size_max могут быть равны.
     */
    public function testAllowsEqualMinAndMaxDecorativeSize(): void
    {
        $this->baseConfig['decorative_size_min'] = 50;
        $this->baseConfig['decorative_size_max'] = 50;
        $config = new ThemeConfig($this->baseConfig);

        self::assertSame(50, $config->getDecorativeSizeMin());
        self::assertSame(50, $config->getDecorativeSizeMax());
    }

    /**
     * Проверяет граничные значения decorative_size_min = 1 и decorative_size_max = 100.
     */
    public function testAllowsBoundarySizeValues(): void
    {
        $this->baseConfig['decorative_size_min'] = 1;
        $this->baseConfig['decorative_size_max'] = 100;
        $config = new ThemeConfig($this->baseConfig);

        self::assertSame(1, $config->getDecorativeSizeMin());
        self::assertSame(100, $config->getDecorativeSizeMax());
    }
}
