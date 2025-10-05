# Imagen base
FROM php:8.2-cli

# Instalar dependencias del sistema necesarias (PostgreSQL + unzip + git + zip)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    zip \
    && docker-php-ext-install pdo_pgsql

# Instalar Composer dentro del contenedor
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Establecer el directorio de trabajo
WORKDIR /app

# Copiar los archivos del proyecto al contenedor
COPY . /app

# Instalar las dependencias de PHP dentro del contenedor
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Exponer el puerto que Render usa (10000 por convención)
EXPOSE 10000

# Comando por defecto para ejecutar PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
