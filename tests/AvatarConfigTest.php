<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\JokeAvatar\AvatarConfig;

/**
 * Тесты конфигурации модуля AvatarConfig.
 *
 * @covers \Vasoft\JokeAvatar\AvatarConfig
 */
final class AvatarConfigTest extends TestCase
{
    private AvatarConfig $config;

    protected function setUp(): void
    {
        $this->config = new AvatarConfig();
    }

    /**
     * Проверяет размер по умолчанию.
     */
    public function testDefaultSize(): void
    {
        self::assertSame(200, $this->config->getSize());
    }

    /**
     * Проверяет установку корректного размера.
     */
    public function testSetValidSize(): void
    {
        $result = $this->config->setSize(150);
        self::assertSame(150, $this->config->getSize());
        self::assertSame($this->config, $result, 'setSize должен возвращать $this');
    }

    /**
     * Проверяет, что размер меньше 50 ограничивается до 50.
     */
    public function testSetSizeClampsToMinimum(): void
    {
        $this->config->setSize(10);
        self::assertSame(50, $this->config->getSize());
    }

    /**
     * Проверяет, что размер больше 1000 ограничивается до 1000.
     */
    public function testSetSizeClampsToMaximum(): void
    {
        $this->config->setSize(2000);
        self::assertSame(1000, $this->config->getSize());
    }

    /**
     * Проверяет граничное значение 50.
     */
    public function testSetSizeAtMinimumBoundary(): void
    {
        $this->config->setSize(50);
        self::assertSame(50, $this->config->getSize());
    }

    /**
     * Проверяет граничное значение 1000.
     */
    public function testSetSizeAtMaximumBoundary(): void
    {
        $this->config->setSize(1000);
        self::assertSame(1000, $this->config->getSize());
    }

    /**
     * Проверяет темы по умолчанию.
     */
    public function testDefaultThemes(): void
    {
        $themes = $this->config->getThemes();

        self::assertArrayHasKey('default', $themes);
        self::assertArrayHasKey('dark', $themes);
        self::assertArrayHasKey('warm', $themes);
        self::assertArrayHasKey('nature', $themes);
        self::assertCount(4, $themes);
    }

    /**
     * Проверяет установку пользовательских тем.
     */
    public function testSetThemes(): void
    {
        $customThemes = [
            'custom' => [
                'background_colors' => ['#FF0000'],
                'text_color' => '#FFFFFF',
                'decorative_colors' => ['#00FF00'],
                'decorative_min' => 1,
                'decorative_max' => 3,
                'decorative_size_min' => 5,
                'decorative_size_max' => 20,
            ],
        ];

        $result = $this->config->setThemes($customThemes);
        self::assertSame($customThemes, $this->config->getThemes());
        self::assertSame($this->config, $result, 'setThemes должен возвращать $this');
    }

    /**
     * Проверяет получение существующей темы.
     */
    public function testGetExistingTheme(): void
    {
        $theme = $this->config->getTheme('default');

        self::assertNotNull($theme);
        self::assertIsArray($theme);
        self::assertArrayHasKey('background_colors', $theme);
        self::assertArrayHasKey('text_color', $theme);
        self::assertArrayHasKey('decorative_colors', $theme);
    }

    /**
     * Проверяет получение несуществующей темы (должен вернуть null).
     */
    public function testGetNonExistentThemeReturnsNull(): void
    {
        $theme = $this->config->getTheme('nonexistent');
        self::assertNull($theme);
    }

    /**
     * Проверяет hasTheme для существующей темы.
     */
    public function testHasExistingTheme(): void
    {
        self::assertTrue($this->config->hasTheme('default'));
        self::assertTrue($this->config->hasTheme('dark'));
    }

    /**
     * Проверяет hasTheme для несуществующей темы.
     */
    public function testHasNonExistentTheme(): void
    {
        self::assertFalse($this->config->hasTheme('nonexistent'));
    }

    /**
     * Проверяет структуру темы default.
     */
    public function testDefaultThemeStructure(): void
    {
        $theme = $this->config->getTheme('default');

        self::assertNotNull($theme);
        self::assertIsArray($theme['background_colors']);
        self::assertIsString($theme['text_color']);
        self::assertIsArray($theme['decorative_colors']);
        self::assertIsInt($theme['decorative_min']);
        self::assertIsInt($theme['decorative_max']);
        self::assertIsInt($theme['decorative_size_min']);
        self::assertIsInt($theme['decorative_size_max']);
    }

    /**
     * Проверяет, что после setSize и setThemes значения сохраняются.
     */
    public function testFluentInterface(): void
    {
        $themes = [
            'test' => [
                'background_colors' => ['#000000'],
                'text_color' => '#FFFFFF',
                'decorative_colors' => ['#FFFFFF'],
                'decorative_min' => 1,
                'decorative_max' => 2,
                'decorative_size_min' => 5,
                'decorative_size_max' => 10,
            ],
        ];

        $this->config
            ->setSize(300)
            ->setThemes($themes);

        self::assertSame(300, $this->config->getSize());
        self::assertSame($themes, $this->config->getThemes());
    }
}