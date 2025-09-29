FROM php:8.2-cli

# Instalar extensión para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app
COPY . /app

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
