# Usar PHP 8.2
FROM php:8.2-cli

# Instalar dependencias necesarias (incluido PostgreSQL)
RUN apt-get update && apt-get install -y libpq-dev unzip git \
    && docker-php-ext-install pdo_pgsql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar carpeta de trabajo
WORKDIR /app

# Copiar archivos al contenedor
COPY . /app

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Exponer puerto
EXPOSE 10000

# Comando de inicio (Render asigna $PORT automáticamente)
CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
