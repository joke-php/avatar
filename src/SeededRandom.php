<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar;

/**
 * Детерминированный генератор псевдослучайных чисел на основе seed-значения.
 *
 * Использует алгоритм xorshift64* для генерации последовательности чисел,
 * полностью определяемой начальным seed-значением. Это гарантирует, что
 * для одного и того же seed будет сгенерирована одна и та же последовательность.
 *
 * Используется для детерминированной генерации аватаров: одинаковый никнейм + тема
 * всегда дают одинаковое изображение, даже после очистки кэша.
 */
final class SeededRandom
{
    /**
     * @var int Текущее состояние генератора (64-битное)
     */
    private int $state;

    /**
     * @param string $seed Строка-зерно для инициализации генератора
     */
    public function __construct(string $seed)
    {
        // Используем crc32 для получения стабильного числа из строки,
        // затем расширяем до 64 бит через хеш
        $hash = md5($seed, binary: true);
        $this->state = 0;
        for ($i = 0; $i < 8; ++$i) {
            $this->state = ($this->state << 8) | (ord($hash[$i]) & 0xFF);
        }
        // Убеждаемся, что состояние не нулевое (xorshift не работает с 0)
        if (0 === $this->state) {
            $this->state = 0x4A6F6B6541766174; // "JokeAvat" в hex
        }
    }

    /**
     * Возвращает случайное целое число в диапазоне [min, max] (включительно).
     *
     * @param int $min Минимальное значение
     * @param int $max Максимальное значение
     *
     * @return int Псевдослучайное число в заданном диапазоне
     */
    public function nextInt(int $min, int $max): int
    {
        if ($min > $max) {
            throw new AvatarException(
                sprintf('min (%d) не может быть больше max (%d)', $min, $max),
            );
        }

        $range = $max - $min + 1;
        if (0 === $range) {
            return $min;
        }

        return $min + ($this->next() % $range);
    }

    /**
     * Выбирает случайный элемент из массива.
     *
     * @template T
     *
     * @param array<int, T> $items Массив элементов для выбора
     *
     * @return T Случайный элемент массива
     */
    public function pick(array $items): mixed
    {
        if ([] === $items) {
            throw new AvatarException('Нельзя выбрать элемент из пустого массива');
        }

        return $items[$this->nextInt(0, count($items) - 1)];
    }

    /**
     * Генерирует следующее псевдослучайное число (линейный конгруэнтный генератор).
     *
     * Алгоритм: LCG Park-Miller с Schrage's method для избежания переполнения:
     *   X_{n+1} = (a * X_n) mod m
     *   a = 16807, m = 2^31 - 1 = 2147483647
     *
     * Schrage's method: представим m = a * q + r, где q = m / a, r = m % a
     *   q = 127773, r = 2836
     *   X_{n+1} = a * (X_n % q) - r * (X_n / q)
     *   Если результат < 0, добавляем m
     *
     * Период: 2^31 - 2 (максимальный для 31-битного LCG)
     * Все значения помещаются в 31-битный диапазон без переполнения.
     *
     * @return int Положительное 31-битное число [1, 2^31 - 2]
     */
    private function next(): int
    {
        $q = 127773;
        $r = 2836;
        $m = 2147483647;

        $x = $this->state;
        $lo = $x % $q;
        $hi = intdiv($x, $q);
        $x = 16807 * $lo - $r * $hi;
        if ($x < 0) {
            $x += $m;
        }
        $this->state = $x;

        return $this->state;
    }
}
