# syntax=docker/dockerfile:1.2
ARG BASE_DOCKER_IMAGE=alpine:3.14.2

FROM ${BASE_DOCKER_IMAGE} AS runtime
WORKDIR /app
RUN adduser -u 82 -H -D -S -G www-data www-data && \
    echo "@edge http://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories && \
    apk add --update --no-cache \
        'php8>=8.0.10' \
        php8-cli \
        php8-ctype \
        php8-curl \
        php8-dom \
        php8-iconv \
        php8-intl \
        php8-json \
        php8-mbstring \
        php8-opcache \
        php8-pdo_pgsql \
        php8-pecl-apcu \
        php8-pecl-swoole@edge \
        php8-simplexml \
        php8-tokenizer \
        php8-xml \
        tini && \
    ln -s /usr/bin/php8 /usr/bin/php

FROM runtime AS installer
WORKDIR /app
RUN apk add --update --no-cache \
        php8-curl \
        php8-openssl \
        php8-phar \
        php8-zip
COPY --from=composer/composer:2.1.6 /usr/bin/composer /usr/bin/composer
COPY composer.* /app/
RUN --mount=type=cache,target=/composer COMPOSER_HOME=/composer composer install --no-dev --no-interaction --no-scripts --prefer-dist
COPY . /app/
RUN --mount=type=cache,target=/composer COMPOSER_HOME=/composer composer dump-autoload --classmap-authoritative
RUN APP_ENV=prod bin/console cache:warmup --no-interaction -vvv

FROM runtime AS app
EXPOSE 8000
WORKDIR /app
COPY --from=installer /app/vendor /app/vendor
COPY --from=installer /app/public /app/public
COPY --from=installer /app/src /app/src
COPY --from=installer /app/config /app/config
COPY --from=installer /app/bin /app/bin
COPY --from=installer /app/var /app/var
COPY --from=installer /app/composer.json /app/composer.json
ENV APP_RUNTIME="Runtime\Swoole\Runtime"
ENV SWOOLE_HOST="0.0.0.0"
ENTRYPOINT ["/sbin/tini", "--"]
CMD ["/usr/bin/php8", "/app/public/index.php"]
