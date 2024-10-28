# Указываем базовый образ с PHP и Alpine Linux
FROM php:8.2-fpm-alpine

# Устанавливаем необходимые пакеты для PHP и Composer
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    bash \
    curl \
    postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем рабочую директорию для приложения
WORKDIR /var/www

# Копируем файлы приложения в контейнер
COPY . .

# Устанавливаем зависимости с помощью Composer
RUN composer install


# Открываем порт для PHP-FPM
EXPOSE 8000

