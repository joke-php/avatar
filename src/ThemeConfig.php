<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

/**
 * Value Object, представляющий тему оформления аватара.
 *
 * Содержит все параметры визуального стиля: цвета фона, цвет текста,
 * цвета декоративных элементов, а также диапазоны для случайной генерации.
 * Объект неизменяем (immutable) после создания.
 */
final class ThemeConfig
{
    /** @var list<string> Массив возможных цветов фона в HEX-формате */
    private readonly array $backgroundColors;

    /** @var string Цвет текста в HEX-формате */
    private readonly string $textColor;

    /** @var list<string> Массив возможных цветов декоративных квадратов в HEX-формате */
    private readonly array $decorativeColors;

    /** @var int Минимальное количество декоративных квадратов */
    private readonly int $decorativeMin;

    /** @var int Максимальное количество декоративных квадратов */
    private readonly int $decorativeMax;

    /** @var int Минимальный размер декоративного квадрата в процентах от размера аватара */
    private readonly int $decorativeSizeMin;

    /** @var int Максимальный размер декоративного квадрата в процентах от размера аватара */
    private readonly int $decorativeSizeMax;

    /**
     * @param array{
     *     background_colors: list<string>,
     *     text_color: string,
     *     decorative_colors: list<string>,
     *     decorative_min: int,
     *     decorative_max: int,
     *     decorative_size_min: int,
     *     decorative_size_max: int
     * } $config Массив параметров темы
     *
     * @throws AvatarException Если передан некорректный цвет
     */
    public function __construct(array $config)
    {
        $this->backgroundColors = $config['background_colors'];
        $this->textColor = $config['text_color'];
        $this->decorativeColors = $config['decorative_colors'];
        $this->decorativeMin = $config['decorative_min'];
        $this->decorativeMax = $config['decorative_max'];
        $this->decorativeSizeMin = $config['decorative_size_min'];
        $this->decorativeSizeMax = $config['decorative_size_max'];

        $this->validate();
    }

    /**
     * Выполняет валидацию параметров темы.
     *
     * @throws AvatarException Если параметры темы некорректны
     */
    private function validate(): void
    {
        if ($this->decorativeMin < 0) {
            throw new AvatarException('decorative_min не может быть отрицательным');
        }
        if ($this->decorativeMax < $this->decorativeMin) {
            throw new AvatarException('decorative_max не может быть меньше decorative_min');
        }
        if ($this->decorativeSizeMin < 1 || $this->decorativeSizeMax > 100) {
            throw new AvatarException('Размер декоративных элементов должен быть в диапазоне 1-100%');
        }
        if ($this->decorativeSizeMax < $this->decorativeSizeMin) {
            throw new AvatarException('decorative_size_max не может быть меньше decorative_size_min');
        }
        if ([] === $this->backgroundColors) {
            throw new AvatarException('Должен быть указан хотя бы один цвет фона');
        }
        if ([] === $this->decorativeColors) {
            throw new AvatarException('Должен быть указан хотя бы один декоративный цвет');
        }
    }

    /**
     * Возвращает массив возможных цветов фона.
     *
     * @return list<string>
     */
    public function getBackgroundColors(): array
    {
        return $this->backgroundColors;
    }

    /**
     * Возвращает цвет текста.
     */
    public function getTextColor(): string
    {
        return $this->textColor;
    }

    /**
     * Возвращает массив возможных декоративных цветов.
     *
     * @return list<string>
     */
    public function getDecorativeColors(): array
    {
        return $this->decorativeColors;
    }

    /**
     * Возвращает минимальное количество декоративных квадратов.
     */
    public function getDecorativeMin(): int
    {
        return $this->decorativeMin;
    }

    /**
     * Возвращает максимальное количество декоративных квадратов.
     */
    public function getDecorativeMax(): int
    {
        return $this->decorativeMax;
    }

    /**
     * Возвращает минимальный размер декоративного квадрата в процентах.
     */
    public function getDecorativeSizeMin(): int
    {
        return $this->decorativeSizeMin;
    }

    /**
     * Возвращает максимальный размер декоративного квадрата в процентах.
     */
    public function getDecorativeSizeMax(): int
    {
        return $this->decorativeSizeMax;
    }
}
