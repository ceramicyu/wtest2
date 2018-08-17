######
# See: https://hub.docker.com/_/php/
######

FROM php:7-fpm

######
# You can install php extensions using docker-php-ext-install
######

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libjpeg-dev \
        libpq-dev \
        libcurl4-gnutls-dev \
        libicu-dev \
        libxml2-dev \
        openssl \
        libssl-dev \
        libpq-dev \
        zlib1g-dev \
        libmemcached-dev \
        git \
        gcc \
        g++ \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd iconv pdo pdo_mysql pdo_pgsql pgsql intl curl json opcache xml mbstring zip \
    && pecl install -o -f redis && docker-php-ext-enable redis \
    && apt-get autoremove && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/pear

RUN echo "Asia/Taipei" > /etc/timezone; dpkg-reconfigure -f noninteractive tzdata

WORKDIR /usr/share/nginx/html/
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar composer

ADD run.sh ./run.sh
CMD ["sh","./run.sh"]
