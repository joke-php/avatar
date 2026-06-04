#!/bin/sh
set -e

# Обеспечиваем наличие директории кэша
mkdir -p /app/var/cache/avatars

# Если передан аргумент — выполняем его как команду
if [ $# -gt 0 ]; then
    exec "$@"
fi

# По умолчанию запускаем встроенный PHP-сервер
exec php -S 0.0.0.0:8000 -t /app/public/