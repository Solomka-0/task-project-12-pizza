# Базовый образ
FROM php:8.0-cli

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений PDO для работы с базой данных
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы composer и устанавливаем зависимости
COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader \
    && rm composer-setup.php

# Копируем остальные файлы приложения
COPY . .

# Указываем порт
EXPOSE 8000

# Запуск встроенного веб-сервера PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]