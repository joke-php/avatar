# Joke Avatar

Библиотека для генерации квадратных PNG-аватаров на основе никнейма для PHP-фреймворка [Joke](https://github.com/joke-php/joke).

## Возможности

- Генерация квадратных PNG-аватаров на основе никнейма
- 6 встроенных тем оформления: `default`, `dark`, `warm`, `nature`, `ocean`, `sunset`
- Декоративные квадраты на аватаре (случайное количество, размер и цвет)
- Отображение первых двух символов никнейма
- Кэширование сгенерированных аватаров в `var/cache/avatars/`
- Поддержка TrueType-шрифтов с автоматическим поиском системных шрифтов
- Проверка доступной памяти перед генерацией больших изображений
- PSR-4 автозагрузка, строгая типизация (`strict_types=1`), PER-CS3

## Требования

- PHP ^8.5
- PHP-расширение `ext-gd`
- Фреймворк `joke-php/joke` ^1.3

## Установка

```bash
composer require joke-php/avatar
```

## Использование

### 1. Регистрация сервис-провайдера

Добавьте `AvatarProvider` в файл `bootstrap/kernel.php`:

```php
use Vasoft\JokeAvatar\AvatarProvider;

$config->addProvider(AvatarProvider::class);
```

### 2. Конфигурация (опционально)

Создайте файл `config/avatar.php` для настройки размера и тем:

```php
use Vasoft\JokeAvatar\AvatarConfig;

return [
    AvatarConfig::class => static function () use ($env): AvatarConfig {
        return (new AvatarConfig())
            ->setSize(200)
            ->setThemes([
                'default' => [
                    'background_colors'    => ['#3498db', '#2980b9', '#1abc9c', '#16a085'],
                    'text_color'           => '#ffffff',
                    'decorative_colors'    => ['#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'],
                    'decorative_min'       => 3,
                    'decorative_max'       => 6,
                    'decorative_size_min'  => 20,
                    'decorative_size_max'  => 60,
                ],
                // ... другие темы
            ]);
    },
];
```

### 3. Генерация аватара в контроллере

```php
use Vasoft\JokeAvatar\AvatarGenerator;
use Vasoft\JokeAvatar\Http\Response\ImageResponse;

class AvatarController
{
    public function __construct(
        private readonly AvatarGenerator $generator,
    ) {}

    public function __invoke(string $nickname): ImageResponse
    {
        $path = $this->generator->generate($nickname, 'dark');
        return (new ImageResponse())->load($path);
    }
}
```

### 4. Маршрут

```php
$router->get('/avatar/{nickname}', AvatarController::class, 'avatar.generate');
```

## Структура модуля

```
src/
├── AvatarConfig.php              # Конфигурация (размер, темы)
├── AvatarException.php           # Базовое исключение
├── AvatarGenerationException.php # Исключение при ошибках GD
├── AvatarGenerator.php           # Бизнес-логика генерации
├── AvatarProvider.php            # Сервис-провайдер
├── SeededRandom.php              # Детерминированный PRNG
├── ThemeConfig.php               # Value Object темы оформления
├── Controller/
│   ├── AvatarController.php      # Контроллер генерации аватара
│   └── HealthController.php      # Healthcheck для Docker
└── Http/Response/
    └── ImageResponse.php         # HTTP-ответ с PNG
docker/
├── Dockerfile                    # Мультистейдж сборка для микросервиса
├── docker-compose.yml            # Docker Compose для запуска
└── entrypoint.sh                 # Точка входа контейнера
```

## Темы оформления

Каждая тема содержит:

| Параметр              | Описание                              |
|-----------------------|---------------------------------------|
| `background_colors`   | Массив цветов фона (выбирается случайно) |
| `text_color`          | Цвет текста                           |
| `decorative_colors`   | Массив цветов декоративных квадратов  |
| `decorative_min`      | Минимальное количество квадратов      |
| `decorative_max`      | Максимальное количество квадратов     |
| `decorative_size_min` | Минимальный размер квадрата (px)      |
| `decorative_size_max` | Максимальный размер квадрата (px)     |

## Кэширование

Сгенерированные аватары кэшируются в директории `var/cache/avatars/`.
Ключ кэша — MD5-хеш от комбинации никнейма и темы.
При повторном запросе возвращается сохранённый файл.

## Исключения

- `AvatarException` — базовое исключение модуля (наследует `JokeException`)
- `AvatarGenerationException` — ошибка при работе с GD (наследует `AvatarException`)

## Docker

Модуль можно запустить как самостоятельный микросервис через Docker.

### Требования

- Docker Engine 24+
- Docker Compose v2+

### Запуск

```bash
docker compose -f docker/docker-compose.yml up -d
```

После запуска сервис доступен на `http://localhost:8000`.

### Примеры запросов

```bash
# Сгенерировать аватар для Alice
curl -o alice.png http://localhost:8000/avatar/Alice

# Сгенерировать аватар для Bob в тёмной теме
curl -o bob.png "http://localhost:8000/avatar/Bob?theme=dark"

# Проверить работоспособность
curl http://localhost:8000/health
```

### Смена порта

Если порт 8000 занят, укажите другой через `ports` в `docker-compose.override.yml`:

```yaml
# docker-compose.override.yml
services:
  avatar:
    ports:
      - "8080:8000"
```

После этого сервис будет доступен на `http://localhost:8080`.

### Привязка домена

Для доступа по доменному имени используйте reverse proxy (например, Nginx):

```nginx
server {
    listen 80;
    server_name avatar.example.com;

    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Либо укажите домен в `docker-compose.override.yml` через сети:

```yaml
# docker-compose.override.yml
services:
  avatar:
    networks:
      - proxy

networks:
  proxy:
    external: true
```

### Остановка

```bash
docker compose -f docker/docker-compose.yml down
```

### Особенности

- Мультистейдж сборка: финальный образ содержит только runtime-зависимости
- Кэш аватаров сохраняется в Docker volume `avatar-cache`
- HEALTHCHECK через эндпоинт `/health`
- Запуск от непривилегированного пользователя `avatar`
- Для production рекомендуется использовать Nginx в качестве reverse proxy

## Тестовое приложение

В репозитории доступно тестовое приложение для локальной проверки:

```bash
composer run dev
```

После запуска откройте в браузере:

- `http://localhost:8000/avatar/Alice`
- `http://localhost:8000/avatar/Bob?theme=dark`
- `http://localhost:8000/avatar/Charlie?theme=ocean`

## Разработка

```bash
# Запуск тестов
composer run test

# Запуск dev-сервера
composer run dev

# Запуск через Docker
docker compose -f docker/docker-compose.yml up
```

## Генерация кода

Весь исходный код данного модуля полностью создан AI-агентом **SourceCraft Code Assistant** (на базе Claude 3.5 Sonnet) в рамках проекта [Joke](https://github.com/joke-php/joke).

- **Архитектура и планирование**: AI-агент в режиме Architect
- **Реализация**: AI-агент в режиме Code
- **Отладка и тестирование**: AI-агент в режиме Debug
- **Оркестрация сложных задач**: AI-агент в режиме Orchestrator

Генерация выполнялась в среде разработки VS Code с использованием Roo Code (расширение для AI-ассистированной разработки). Человек выступал в роли ревьюера и постановщика задач.

## Лицензия

MIT