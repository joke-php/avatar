# =============================================================================
# Этап 1: Установка зависимостей Composer
# =============================================================================
FROM php:8.5-cli-alpine AS vendor

RUN set -eux \
    && apk add --no-cache \
        libpng-dev \
        freetype-dev \
        libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd

COPY composer.json composer.lock /app/
WORKDIR /app

RUN set -eux \
    && apk add --no-cache unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --no-interaction --no-progress --optimize-autoloader \
    && composer clear-cache

# =============================================================================
# Этап 2: Финальный образ
# =============================================================================
FROM php:8.5-cli-alpine

LABEL org.opencontainers.image.title="Joke Avatar"
LABEL org.opencontainers.image.description="Avatar generation microservice for the Joke framework"
LABEL org.opencontainers.image.source="https://github.com/joke-php/avatar"
LABEL org.opencontainers.image.licenses="MIT"

# Установка расширения GD и curl для healthcheck
RUN set -eux \
    && apk add --no-cache \
        libpng \
        freetype \
        libjpeg-turbo \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd

# Копирование зависимостей из этапа vendor
COPY --from=vendor /app/vendor/ /app/vendor/

# Копирование исходного кода
COPY . /app/

WORKDIR /app

# Создание непривилегированного пользователя
RUN set -eux \
    && adduser -D -h /app avatar \
    && mkdir -p /app/var/cache/avatars \
    && chown -R avatar:avatar /app/var

USER avatar

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

ENTRYPOINT ["/app/docker/entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]