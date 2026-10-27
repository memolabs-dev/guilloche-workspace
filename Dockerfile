# Image de production : FrankenPHP, assets compilés par Encore.
# https://symfony.com/doc/current/deployment.html

FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-progress
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative \
 && composer dump-env prod

# package.json lit le paquet JavaScript de symfony/ux-turbo depuis vendor/.
FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
COPY --from=vendor /app/vendor/symfony/ux-turbo/assets vendor/symfony/ux-turbo/assets
RUN npm ci --no-audit --no-fund
COPY webpack.config.js ./
COPY assets assets
RUN npm run build

FROM dunglas/frankenphp:1-php8.4-bookworm AS app
ENV SERVER_NAME=:80 \
    DEFAULT_URI=https://guilloche.memolabs.dev
WORKDIR /app
# Seul ce qui sert à l'exécution entre dans l'image finale.
COPY --from=vendor /app/bin bin
COPY --from=vendor /app/config config
COPY --from=vendor /app/public public
COPY --from=vendor /app/src src
COPY --from=vendor /app/templates templates
COPY --from=vendor /app/vendor vendor
COPY --from=vendor /app/composer.json /app/.env /app/.env.local.php ./
COPY --from=assets /app/public/build public/build
RUN APP_SECRET=build php bin/console cache:warmup \
 && chown -R www-data:www-data var
# Application sans état : un APP_SECRET fourni par l'environnement prime ;
# à défaut, il est tiré au démarrage.
CMD ["sh", "-c", "export APP_SECRET=\"${APP_SECRET:-$(head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \\n')}\"; exec frankenphp run --config /etc/frankenphp/Caddyfile"]
EXPOSE 80
