<?php

declare(strict_types=1);

namespace Vasoft\JokeAvatar\Tests;

use PHPUnit\Framework\TestCase;
use Vasoft\Joke\Support\FileSystem;

abstract class ModuleTestCase extends TestCase
{
    protected string $basePath = '';
    protected FileSystem $fs;

    protected function setUp(): void
    {
        $this->fs = new FileSystem($this->basePath);
    }

    protected function makeBase(string $name): void
    {
        $this->basePath = sprintf(
            '%s/joke-avtr-%s-%s',
            sys_get_temp_dir(),
            $name,
            uniqid('', true),
        );
        @mkdir($this->basePath);
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
        if ('' === $path || !is_dir($path)) {
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
}
