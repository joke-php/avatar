# 1.2.1 (2026-06-04)

### Fixes
- переименование пакета voral/joke-avatar → joke-php/avatar

# 1.2.0 (2026-06-04)

### New features
- детерминированная генерация аватаров через SeededRandom

### Other changes
- добавлен кэш PHPUnit в .gitignore

# 1.1.1 (2026-06-04)

### Other changes
- translate scripts-descriptions to English

# 1.1.0 (2026-06-04)

### New features
- добавление phpstan в зависимости и скрипты composer
- добавлено тестовое приложение для локальной проверки модуля
- добавлен AvatarProvider для регистрации сервиса в DI-контейнере
- реализован AvatarGenerator с кэшированием и обработкой ошибок GD
- добавлен AvatarConfig с поддержкой тем оформления
- добавлен ThemeConfig - value object для темы оформления
- добавлены исключения AvatarException и AvatarGenerationException
- инициализация проекта joke-avatar

### Fixes
- удалён вызов imagedestroy(), deprecated с PHP 8.0
- исправление ошибок статического анализа до уровня 8
- URL-decoding UTF-8 nickname parameter in AvatarController

### Other changes
- установка phpunit/phpunit ^13.1 в require-dev
- добавление кэша php-cs-fixer в .gitignore
- обновление composer.lock после установки php-cs-fixer

### Documentation
- добавлен README.md с описанием модуля

### Styling
- добавление пустой строки в конце ImageResponse
- исправление форматирования AvatarController
- исправление форматирования AvatarGenerator
- исправление форматирования AvatarProvider
- удаление избыточных PHPDoc тегов в AvatarConfig
- удаление неиспользуемого импорта в ThemeConfig
- исправление импортов и PHPDoc в AvatarGenerationException
- приведение пустого класса AvatarException к одной строке

### Tests
- удалены вызовы deprecated функций setAccessible() и imagedestroy()
- заменены createMock на createStub и добавлены expects() для устранения PHPUnit notices
- добавление тестов для AvatarController
- добавление тестов для AvatarProvider
- добавление тестов для ImageResponse
- добавление тестов для AvatarGenerator
- добавление тестов для AvatarConfig
- добавление тестов для ThemeConfig
- добавление тестов для AvatarGenerationException
- добавление тестов для AvatarException
- добавление конфигурации phpunit.xml и тестовых фикстур

### Change build system
- добавлен voral/version-increment для автоматического управления версиями

### Other
- Добавление php-cs-fixer и скрипта для его запуска

