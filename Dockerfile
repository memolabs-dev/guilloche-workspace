# Image de production : FrankenPHP (serveur recommandé par Symfony), assets compilés par Encore.
# Trois étapes : dépendances PHP, build des assets (qui lit vendor/symfony/ux-turbo), runtime.

FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-progress --ignore-platform-reqs

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
COPY --from=vendor /app/vendor/symfony/ux-turbo/assets vendor/symfony/ux-turbo/assets
RUN npm ci --no-audit --no-fund
COPY webpack.config.js ./
COPY assets assets
COPY --from=vendor /app/vendor vendor
RUN npm run build

FROM dunglas/frankenphp:1-php8.4-bookworm AS app
ENV APP_ENV=prod \
    APP_DEBUG=0 \
    SERVER_NAME=:80 \
    DEFAULT_URI=https://guilloche.memolabs.dev
WORKDIR /app
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
COPY --from=vendor /app/vendor vendor
COPY . .
COPY --from=assets /app/public/build public/build
RUN rm -rf node_modules var .agent .impeccable .hallmark tests \
 && composer dump-autoload --no-dev --classmap-authoritative \
 && APP_SECRET=build php bin/console cache:warmup \
 && chown -R www-data:www-data var
# Aucun état métier : le secret ne protège que des jetons de démonstration, tiré au démarrage
# s'il n'est pas fourni par l'environnement de la stack.
CMD ["sh", "-c", "export APP_SECRET=\"${APP_SECRET:-$(head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \\n')}\"; exec frankenphp run --config /etc/frankenphp/Caddyfile"]
EXPOSE 80
