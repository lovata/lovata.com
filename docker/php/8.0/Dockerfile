ARG MODE=main

FROM php:8.0-fpm as base
ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC
ENV NODE_VERSION=18
ENV POSTGRES_VERSION=15

ONBUILD WORKDIR /var/www/html

ONBUILD RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ONBUILD RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev gnupg gosu curl ca-certificates zip \
    unzip git supervisor libcap2-bin libpng-dev libpq-dev libonig-dev \
    libzip-dev libicu-dev libxml2-dev jpegoptim optipng pngquant gifsicle libjpeg-dev libwebp-dev \
    libfreetype6-dev libssh2-1-dev libssh2-1 libmcrypt-dev libedit-dev python3 python3-dev python3-pip nano mc \
    && mkdir -p ~/.gnupg \
    && chmod 600 ~/.gnupg \
    && echo "disable-ipv6" >> ~/.gnupg/dirmngr.conf \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

ONBUILD RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ONBUILD RUN pecl install -o -f redis xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) mysqli pdo_mysql pgsql pdo pdo_pgsql curl mbstring bcmath zip intl gd xml exif opcache \
    && docker-php-ext-enable pdo pdo_mysql pdo_pgsql curl mbstring bcmath zip intl gd xml exif redis xdebug

ONBUILD COPY ./../start-container /usr/local/bin/start-container
ONBUILD COPY ./../php.ini /usr/local/etc/php/conf.d/99-sail.ini

ONBUILD RUN chmod +x /usr/local/bin/start-container

ONBUILD STOPSIGNAL SIGQUIT

ONBUILD EXPOSE 8000
ONBUILD ENTRYPOINT ["start-container"]

FROM base as main
ENV WWWGROUP=1000
ENV WWWUSER=1000

ONBUILD RUN echo "-> $WWWGROUP"
ONBUILD RUN echo "-> $WWWUSER"
ONBUILD RUN groupadd --force -g $WWWGROUP sail
ONBUILD RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u $WWWUSER sail
ONBUILD RUN sed -i -e 's/user = www-data/user = sail/g' /usr/local/etc/php-fpm.d/www.conf
ONBUILD RUN sed -i -e 's/group = www-data/group = sail/g' /usr/local/etc/php-fpm.d/www.conf

FROM main as app

ONBUILD RUN apt-get update \
    && mkdir /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y nodejs \
    && apt-get install -y default-mysql-client \
    && apt-get install -y postgresql-client

FROM ${MODE}
