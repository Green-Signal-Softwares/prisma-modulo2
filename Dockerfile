# =============================================================================
#  PRISMA — Dockerfile (mínimo)
#  PHP 8.3-FPM | Só o necessário para Laravel + MySQL
# =============================================================================

FROM php:8.4-fpm-alpine

# Copia o binário do Composer direto da imagem oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# install-php-extensions resolve headers e dependências automaticamente
ADD --chmod=0755 \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    bcmath \
    zip \
    gd \
    opcache

# PHP config básico
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-prisma.ini

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN mkdir -p storage/framework/{cache,sessions,views} \
             storage/logs \
             bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
