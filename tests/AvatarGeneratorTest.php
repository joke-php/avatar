<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Config\Environment;
use Vasoft\JokeAvatar\AvatarConfig;
use Vasoft\JokeAvatar\AvatarException;
use Vasoft\JokeAvatar\AvatarGenerationException;
use Vasoft\JokeAvatar\AvatarGenerator;

/**
 * Тесты сервиса генерации аватаров AvatarGenerator.
 *
 * @covers \Vasoft\JokeAvatar\AvatarGenerator
 */
final class AvatarGeneratorTest extends TestCase
{
    private AvatarConfig $config;
    private Environment $env;
    private AvatarGenerator $generator;
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = sys_get_temp_dir() . '/joke-avatar-test-' . uniqid('', true) . '/';

        $this->config = $this->createStub(AvatarConfig::class);
        $this->env = $this->createStub(Environment::class);

        $this->env
            ->method('getBasePath')
            ->willReturn($this->basePath);

        $this->generator = new AvatarGenerator($this->config, $this->env);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->basePath);
    }

    /**
     * Удаляет директорию рекурсивно.
     */
    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir((string) $item);
            } else {
                @unlink((string) $item);
            }
        }

        @rmdir($path);
    }

    /**
     * Настраивает мок конфигурации для темы default.
     */
    private function configureDefaultTheme(): void
    {
        $this->config
            ->method('getSize')
            ->willReturn(200);

        $this->config
            ->method('hasTheme')
            ->willReturnCallback(fn (string $theme): bool => 'default' === $theme);

        $this->config
            ->method('getTheme')
            ->willReturnCallback(fn (string $theme): ?array => 'default' === $theme ? [
                'background_colors' => ['#E8F4F8'],
                'text_color' => '#2C3E50',
                'decorative_colors' => ['#BBDEFB'],
                'decorative_min' => 1,
                'decorative_max' => 1,
                'decorative_size_min' => 10,
                'decorative_size_max' => 10,
            ] : null);
    }

    /**
     * Проверяет успешную генерацию аватара с темой по умолчанию.
     */
    public function testGenerateWithDefaultTheme(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('TestUser');

        self::assertFileExists($result);
        self::assertStringEndsWith('.png', $result);

        $imageInfo = getimagesize($result);
        self::assertNotFalse($imageInfo);
        self::assertSame(200, $imageInfo[0]);
        self::assertSame(200, $imageInfo[1]);
        self::assertSame('image/png', $imageInfo['mime']);
    }

    /**
     * Проверяет генерацию аватара с указанной темой.
     */
    public function testGenerateWithSpecifiedTheme(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('TestUser', 'default');

        self::assertFileExists($result);
        self::assertStringEndsWith('.png', $result);
    }

    /**
     * Проверяет, что при неизвестной теме используется 'default'.
     */
    public function testGenerateFallsBackToDefaultForUnknownTheme(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('TestUser', 'unknown_theme');

        self::assertFileExists($result);
    }

    /**
     * Проверяет, что при theme = null используется 'default'.
     */
    public function testGenerateFallsBackToDefaultForNullTheme(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('TestUser', null);

        self::assertFileExists($result);
    }

    /**
     * Проверяет выброс исключения при пустом никнейме.
     */
    public function testGenerateThrowsExceptionForEmptyNickname(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Никнейм не может быть пустым');

        $this->generator->generate('');
    }

    /**
     * Проверяет выброс исключения при никнейме из пробелов.
     */
    public function testGenerateThrowsExceptionForWhitespaceNickname(): void
    {
        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Никнейм не может быть пустым');

        $this->generator->generate('   ');
    }

    /**
     * Проверяет кэширование: повторный вызов возвращает тот же файл.
     */
    public function testGenerateReturnsCachedFileOnSecondCall(): void
    {
        $this->configureDefaultTheme();

        $result1 = $this->generator->generate('CacheTest');
        $result2 = $this->generator->generate('CacheTest');

        self::assertSame($result1, $result2);
        self::assertFileExists($result1);
    }

    /**
     * Проверяет, что разные никнеймы генерируют разные файлы.
     */
    public function testGenerateDifferentNicknamesProduceDifferentFiles(): void
    {
        $this->configureDefaultTheme();

        $result1 = $this->generator->generate('Alice');
        $result2 = $this->generator->generate('Bob');

        self::assertNotSame($result1, $result2);
        self::assertFileExists($result1);
        self::assertFileExists($result2);
    }

    /**
     * Проверяет, что никнейм обрезается (trim).
     */
    public function testGenerateTrimsNickname(): void
    {
        $this->configureDefaultTheme();

        $result1 = $this->generator->generate('  TestUser  ');
        $result2 = $this->generator->generate('TestUser');

        // После trim оба должны дать одинаковый кэш-ключ
        self::assertSame($result1, $result2);
    }

    /**
     * Проверяет регистронезависимость кэширования (через mb_strtolower).
     */
    public function testGenerateCacheKeyIsCaseInsensitive(): void
    {
        $this->configureDefaultTheme();

        $result1 = $this->generator->generate('Alice');
        $result2 = $this->generator->generate('alice');

        self::assertSame($result1, $result2);
    }

    /**
     * Проверяет выброс исключения при отсутствии темы в конфиге.
     */
    public function testGenerateThrowsExceptionWhenThemeNotFound(): void
    {
        $this->config
            ->method('hasTheme')
            ->willReturn(false);

        $this->config
            ->method('getTheme')
            ->willReturn(null);

        $this->config
            ->method('getSize')
            ->willReturn(200);

        $this->expectException(AvatarException::class);
        $this->expectExceptionMessage('Тема "default" не найдена');

        $this->generator->generate('TestUser');
    }

    /**
     * Проверяет генерацию аватара с минимальным размером.
     */
    public function testGenerateWithMinimumSize(): void
    {
        $this->config
            ->method('getSize')
            ->willReturn(50);

        $this->config
            ->method('hasTheme')
            ->willReturn(true);

        $this->config
            ->method('getTheme')
            ->willReturn([
                'background_colors' => ['#E8F4F8'],
                'text_color' => '#2C3E50',
                'decorative_colors' => ['#BBDEFB'],
                'decorative_min' => 1,
                'decorative_max' => 1,
                'decorative_size_min' => 10,
                'decorative_size_max' => 10,
            ]);

        $result = $this->generator->generate('Mini');

        self::assertFileExists($result);

        $imageInfo = getimagesize($result);
        self::assertNotFalse($imageInfo);
        self::assertSame(50, $imageInfo[0]);
        self::assertSame(50, $imageInfo[1]);
    }

    /**
     * Проверяет генерацию аватара с максимальным размером.
     */
    public function testGenerateWithMaximumSize(): void
    {
        $this->config
            ->method('getSize')
            ->willReturn(1000);

        $this->config
            ->method('hasTheme')
            ->willReturn(true);

        $this->config
            ->method('getTheme')
            ->willReturn([
                'background_colors' => ['#E8F4F8'],
                'text_color' => '#2C3E50',
                'decorative_colors' => ['#BBDEFB'],
                'decorative_min' => 1,
                'decorative_max' => 1,
                'decorative_size_min' => 10,
                'decorative_size_max' => 10,
            ]);

        $result = $this->generator->generate('Maxi');

        self::assertFileExists($result);

        $imageInfo = getimagesize($result);
        self::assertNotFalse($imageInfo);
        self::assertSame(1000, $imageInfo[0]);
        self::assertSame(1000, $imageInfo[1]);
    }

    /**
     * Проверяет, что аватар содержит текст (первые 2 символа никнейма).
     */
    public function testGeneratedImageContainsText(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('AB');

        self::assertFileExists($result);

        // Проверяем, что файл не пустой
        self::assertGreaterThan(100, filesize($result));
    }

    /**
     * Проверяет, что для однобуквенного никнейма используется единственный символ.
     */
    public function testGenerateWithSingleCharacterNickname(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('A');

        self::assertFileExists($result);
        self::assertGreaterThan(100, filesize($result));
    }

    /**
     * Проверяет, что для никнейма с юникодом корректно берутся первые 2 символа.
     */
    public function testGenerateWithUnicodeNickname(): void
    {
        $this->configureDefaultTheme();

        $result = $this->generator->generate('Алексей');

        self::assertFileExists($result);
        self::assertGreaterThan(100, filesize($result));
    }

    /**
     * Проверяет, что при ошибке создания директории кэша выбрасывается исключение.
     *
     * Создаём файл-препятствие на пути к директории кэша,
     * чтобы mkdir не смог создать директорию.
     */
    public function testGenerateThrowsExceptionWhenCacheDirCannotBeCreated(): void
    {
        // Создаём файл вместо директории var/cache/avatars
        $cacheDir = $this->basePath . 'var/cache/';
        @mkdir($cacheDir, 0o755, true);
        $obstacleFile = $cacheDir . 'avatars';
        file_put_contents($obstacleFile, '');

        $this->configureDefaultTheme();

        $this->expectException(AvatarGenerationException::class);

        try {
            $this->generator->generate('TestUser');
        } finally {
            // Очищаем созданный файл-препятствие
            @unlink($obstacleFile);
        }
    }
}