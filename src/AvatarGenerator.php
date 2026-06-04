<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

use Vasoft\Joke\Config\Environment;

/**
 * Сервис генерации аватаров на основе никнейма.
 *
 * Создаёт квадратное PNG-изображение аватара с использованием расширения GD.
 * Алгоритм:
 * 1. Выбор темы (default, если не указана или неизвестна)
 * 2. Случайный цвет фона из палитры темы
 * 3. Случайные декоративные квадраты поверх фона
 * 4. Первые два символа никнейма цветом текста из темы
 * 5. Кэширование результата в var/cache/avatars/
 *
 * @see AvatarConfig
 * @see ThemeConfig
 */
class AvatarGenerator
{
    /**
     * @var string Путь к директории кэша аватаров
     */
    private readonly string $cachePath;

    /**
     * @param AvatarConfig $config Конфигурация модуля
     * @param Environment  $env    Окружение приложения (для определения корневого пути)
     */
    public function __construct(
        private readonly AvatarConfig $config,
        private readonly Environment $env,
    ) {
        $this->cachePath = $env->getBasePath() . 'var/cache/avatars/';
    }

    /**
     * Генерирует аватар для указанного никнейма.
     *
     * @param string      $nickname Никнейм пользователя (обязательный параметр)
     * @param null|string $theme    Название темы оформления (если null или неизвестна — используется 'default')
     *
     * @return string Абсолютный путь к сгенерированному PNG-файлу
     *
     * @throws AvatarException           Если никнейм пуст
     * @throws AvatarGenerationException При ошибках работы GD
     */
    public function generate(string $nickname, ?string $theme = null): string
    {
        $nickname = trim($nickname);

        if ('' === $nickname) {
            throw new AvatarException('Никнейм не может быть пустым');
        }

        $themeName = $this->resolveThemeName($theme);
        $cacheKey = $this->buildCacheKey($nickname, $themeName);
        $cacheFile = $this->cachePath . $cacheKey . '.png';

        if (file_exists($cacheFile)) {
            return $cacheFile;
        }

        $themeData = $this->config->getTheme($themeName);
        if (null === $themeData) {
            throw new AvatarException(sprintf('Тема "%s" не найдена', $themeName));
        }

        $themeConfig = new ThemeConfig($themeData);
        $size = $this->config->getSize();

        $this->ensureCacheDirectoryExists();
        $this->generateImage($size, $themeConfig, $nickname, $cacheFile);

        return $cacheFile;
    }

    /**
     * Определяет название темы для использования.
     *
     * @param null|string $theme Переданное название темы
     *
     * @return string Название темы ('default', если не указана или неизвестна)
     */
    private function resolveThemeName(?string $theme): string
    {
        if (null !== $theme && $this->config->hasTheme($theme)) {
            return $theme;
        }

        return 'default';
    }

    /**
     * Формирует ключ кэша на основе никнейма и темы.
     *
     * @param string $nickname Никнейм
     * @param string $theme    Название темы
     *
     * @return string MD5-хеш для использования в имени файла
     */
    private function buildCacheKey(string $nickname, string $theme): string
    {
        return md5(mb_strtolower($nickname) . '_' . $theme);
    }

    /**
     * Создаёт директорию кэша, если она не существует.
     *
     * @throws AvatarGenerationException Если не удалось создать директорию
     */
    private function ensureCacheDirectoryExists(): void
    {
        if (!is_dir($this->cachePath)) {
            $created = @mkdir($this->cachePath, 0o755, true);
            if (!$created && !is_dir($this->cachePath)) {
                throw new AvatarGenerationException(
                    sprintf('Не удалось создать директорию кэша: %s', $this->cachePath),
                );
            }
        }
    }

    /**
     * Выполняет генерацию изображения аватара.
     *
     * @param int         $size       Размер аватара в пикселях
     * @param ThemeConfig $theme      Конфигурация темы
     * @param string      $nickname   Никнейм
     * @param string      $outputPath Путь для сохранения PNG
     *
     * @throws AvatarGenerationException При ошибках GD
     */
    private function generateImage(int $size, ThemeConfig $theme, string $nickname, string $outputPath): void
    {
        if ($size < 1) {
            throw new AvatarGenerationException(
                sprintf('Некорректный размер изображения: %d', $size),
            );
        }

        $this->checkAvailableMemory($size);

        $image = @imagecreatetruecolor($size, $size);
        if (false === $image) {
            throw new AvatarGenerationException(
                sprintf('Не удалось создать изображение размером %dx%d', $size, $size),
            );
        }

        $this->drawBackground($image, $size, $theme);
        $this->drawDecorations($image, $size, $theme);
        $this->drawText($image, $size, $theme, $nickname);
        $this->savePng($image, $outputPath);
    }

    /**
     * Проверяет доступный объём памяти для создания изображения.
     *
     * @param int $size Размер изображения
     *
     * @throws AvatarGenerationException Если памяти недостаточно
     */
    private function checkAvailableMemory(int $size): void
    {
        $requiredBytes = $size * $size * 4;
        $requiredMb = ceil($requiredBytes / 1024 / 1024);
        $memoryLimit = $this->getMemoryLimit();

        if ($memoryLimit > 0 && $requiredBytes > $memoryLimit) {
            throw new AvatarGenerationException(
                sprintf(
                    'Недостаточно памяти для создания аватара размером %dx%d. Требуется ~%d MB, доступно %d MB',
                    $size,
                    $size,
                    (int) $requiredMb,
                    (int) ($memoryLimit / 1024 / 1024),
                ),
            );
        }
    }

    /**
     * Возвращает лимит памяти PHP в байтах.
     *
     * @return int Лимит памяти в байтах (0 — без лимита)
     */
    private function getMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        if ('-1' === $limit) {
            return 0;
        }

        /** @var string $limit */
        return $this->convertToBytes($limit);
    }

    /**
     * Преобразует строковое значение размера памяти в байты.
     *
     * @param string $value Строковое значение (например, '128M', '1G')
     *
     * @return int Размер в байтах
     */
    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $number = (int) substr($value, 0, -1);

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $value,
        };
    }

    /**
     * Заливает фон аватара случайным цветом из палитры темы.
     *
     * @param \GdImage    $image Ресурс изображения GD
     * @param int         $size  Размер изображения
     * @param ThemeConfig $theme Тема оформления
     *
     * @throws AvatarGenerationException При ошибке выделения цвета
     */
    private function drawBackground(\GdImage $image, int $size, ThemeConfig $theme): void
    {
        $colors = $theme->getBackgroundColors();
        $colorHex = $colors[array_rand($colors)];
        $color = $this->allocateColor($image, $colorHex);

        imagefill($image, 0, 0, $color);
    }

    /**
     * Рисует декоративные квадраты на аватаре.
     *
     * Количество и размер квадратов выбираются случайно в диапазонах,
     * заданных в теме. Цвета выбираются из палитры декоративных цветов.
     *
     * @param \GdImage    $image Ресурс изображения GD
     * @param int         $size  Размер изображения
     * @param ThemeConfig $theme Тема оформления
     *
     * @throws AvatarGenerationException При ошибке выделения цвета
     */
    private function drawDecorations(\GdImage $image, int $size, ThemeConfig $theme): void
    {
        $count = random_int($theme->getDecorativeMin(), $theme->getDecorativeMax());
        $colors = $theme->getDecorativeColors();

        for ($i = 0; $i < $count; ++$i) {
            $decorativeSize = (int) ($size * random_int(
                $theme->getDecorativeSizeMin(),
                $theme->getDecorativeSizeMax(),
            ) / 100);

            $x = random_int(0, $size - $decorativeSize);
            $y = random_int(0, $size - $decorativeSize);

            $colorHex = $colors[array_rand($colors)];
            $color = $this->allocateColor($image, $colorHex);

            imagefilledrectangle($image, $x, $y, $x + $decorativeSize, $y + $decorativeSize, $color);
        }
    }

    /**
     * Накладывает первые два символа никнейма на аватар.
     *
     * Текст центрируется по горизонтали и вертикали.
     * Размер шрифта подбирается пропорционально размеру аватара.
     *
     * @param \GdImage    $image    Ресурс изображения GD
     * @param int         $size     Размер изображения
     * @param ThemeConfig $theme    Тема оформления
     * @param string      $nickname Никнейм
     *
     * @throws AvatarGenerationException При ошибке выделения цвета
     */
    private function drawText(\GdImage $image, int $size, ThemeConfig $theme, string $nickname): void
    {
        $text = mb_strtoupper(mb_substr($nickname, 0, 2));
        $color = $this->allocateColor($image, $theme->getTextColor());

        $fontSize = (int) ($size * 0.4);
        $fontFile = $this->findFont();

        if (null !== $fontFile) {
            $this->drawTrueTypeText($image, $text, $fontFile, $fontSize, $color, $size);
        } else {
            $this->drawBuiltinText($image, $text, $color, $size);
        }
    }

    /**
     * Рисует текст с использованием TrueType-шрифта (imagettftext).
     *
     * @param \GdImage $image    Ресурс изображения GD
     * @param string   $text     Текст для отображения
     * @param string   $fontFile Путь к файлу шрифта
     * @param int      $fontSize Размер шрифта
     * @param int      $color    Цвет (индекс в палитре GD)
     * @param int      $size     Размер изображения
     */
    private function drawTrueTypeText(
        \GdImage $image,
        string $text,
        string $fontFile,
        int $fontSize,
        int $color,
        int $size,
    ): void {
        $bbox = @imagettfbbox($fontSize, 0, $fontFile, $text);
        if (false !== $bbox) {
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            $x = (int) (($size - $textWidth) / 2);
            $y = (int) (($size + $textHeight) / 2);

            @imagettftext($image, $fontSize, 0, $x, $y, $color, $fontFile, $text);
        }
    }

    /**
     * Рисует текст встроенным GD-шрифтом (без TrueType).
     *
     * @param \GdImage $image Ресурс изображения GD
     * @param string   $text  Текст для отображения
     * @param int      $color Цвет (индекс в палитре GD)
     * @param int      $size  Размер изображения
     */
    private function drawBuiltinText(\GdImage $image, string $text, int $color, int $size): void
    {
        $font = 5;
        $charWidth = imagefontwidth($font);
        $charHeight = imagefontheight($font);
        $textWidth = mb_strlen($text) * $charWidth;
        $x = (int) (($size - $textWidth) / 2);
        $y = (int) (($size - $charHeight) / 2);

        imagestring($image, $font, $x, $y, $text, $color);
    }

    /**
     * Выделяет цвет в палитре GD из HEX-строки.
     *
     * @param \GdImage $image Ресурс изображения GD
     * @param string   $hex   Цвет в HEX-формате (например, '#FF0000' или 'FF0000')
     *
     * @return int Индекс цвета в палитре GD
     *
     * @throws AvatarGenerationException Если не удалось выделить цвет
     */
    private function allocateColor(\GdImage $image, string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = min(255, max(0, (int) hexdec(substr($hex, 0, 2))));
        $g = min(255, max(0, (int) hexdec(substr($hex, 2, 2))));
        $b = min(255, max(0, (int) hexdec(substr($hex, 4, 2))));

        $color = @imagecolorallocate($image, $r, $g, $b);
        if (false === $color) {
            throw new AvatarGenerationException(
                sprintf('Не удалось выделить цвет #%s', $hex),
            );
        }

        return $color;
    }

    /**
     * Сохраняет изображение в PNG-файл.
     *
     * @param \GdImage $image      Ресурс изображения GD
     * @param string   $outputPath Путь для сохранения
     *
     * @throws AvatarGenerationException При ошибке сохранения
     */
    private function savePng(\GdImage $image, string $outputPath): void
    {
        $result = @imagepng($image, $outputPath);
        if (false === $result) {
            throw new AvatarGenerationException(
                sprintf('Не удалось сохранить PNG-файл: %s', $outputPath),
            );
        }
    }

    /**
     * Ищет доступный TrueType-шрифт в системе.
     *
     * @return null|string Путь к файлу шрифта или null, если шрифт не найден
     */
    private function findFont(): ?string
    {
        $fontCandidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/TTF/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/ubuntu/Ubuntu-Bold.ttf',
            '/usr/share/fonts/truetype/ubuntu/Ubuntu-Regular.ttf',
        ];

        foreach ($fontCandidates as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        return null;
    }
}
