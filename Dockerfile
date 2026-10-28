# Image de développement / test — non destinée à la production.

# 1. Dépendances PHP
FROM php:8.4-cli AS app

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize \
    && chmod +x docker/entrypoint.sh

# 2. Assets front (Tailwind scanne aussi vendor/, d'où la copie depuis l'étape 1)
FROM node:24-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY --from=app /var/www/html /app
RUN npm run build

# 3. Image finale
FROM app

COPY --from=assets /app/public/build public/build

EXPOSE 8000

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
