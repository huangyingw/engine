FROM php:7.3-fpm-alpine3.10

RUN apk add --update make

RUN apk add  --virtual build-deps \
    libzip-dev \
    zlib-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    $PHPIZE_DEPS \
    && apk add  \
    libzip \
    coreutils \
    imagemagick \
    nodejs \
    npm \
    ffmpeg \
    icu-dev \
    && docker-php-ext-install -j$(nproc) bcmath \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install -j$(nproc) exif \
    && docker-php-ext-install -j$(nproc) sockets \
    && docker-php-ext-install -j$(nproc) intl \
    && apk del build-deps

# PECL Extensions
RUN apk add  --virtual build-deps \
    $PHPIZE_DEPS \
    imagemagick-dev \
    && pecl install mongodb \
    && pecl install redis \
    && pecl install apcu \
    && pecl install imagick \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-enable redis \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable imagick \
    && apk del build-deps

# Cassandra extension
ENV MAKEFLAGS -j4
ENV INSTALL_DIR /usr/src/datastax-php-driver
ENV BUILD_DEPS \
    bash \
    cmake \
    autoconf \
    g++ \
    gcc \
    make \
    pcre-dev \
    libuv-dev \
    git \
    gmp-dev \
    autoconf \
    libtool \
    openssl-dev \
    zlib-dev \
    $PHPIZE_DEPS

RUN apk add  --virtual build-deps $BUILD_DEPS \
    && apk add  libuv gmp \
    && git clone https://github.com/datastax/php-driver.git $INSTALL_DIR \
    && cd $INSTALL_DIR \
    && git reset --hard f73debf39e395817dd2ced76acfc64249d4d3a0a \
    && git submodule update --init \
    && cd ext && bash $INSTALL_DIR/ext/install.sh \
    && docker-php-ext-enable cassandra \
    && apk del build-deps \
    && rm -rf $INSTALL_DIR

# ZMQ extension
ENV INSTALL_DIR /usr/src/php-zmq
RUN apk add  --virtual build-deps \
    zeromq-dev \
    git \
    $PHPIZE_DEPS \
    && apk add  zeromq \
    && git clone https://github.com/zeromq/php-zmq.git $INSTALL_DIR \
    && cd $INSTALL_DIR \
    && phpize \
    && ./configure \
    && make -j$(nproc) \
    && make install \
    && docker-php-ext-enable zmq \
    && apk del build-deps \
    && rm -rf $INSTALL_DIR

# Install awscli (downstream containers require for the moment)

RUN apk update && apk add  py-pip && pip install --upgrade pip && pip install awscli

# Build Pulsar

ENV BUILD_DEPS \
    bash \
    cmake \
    autoconf \
    g++ \
    gcc \
    make \
    pcre-dev \
    libuv-dev \
    git \
    gmp-dev \
    autoconf \
    libtool \
    openssl-dev \
    zlib-dev \
    boost-dev \
    py3-setuptools \
    python3-dev \ 
    protobuf-dev \
    curl-dev \
    gtest-dev gmock \
    $PHPIZE_DEPS

RUN apk add --no-cache --virtual build-deps $BUILD_DEPS \
    # PHP CPP
    && git clone https://github.com/CopernicaMarketingSoftware/PHP-CPP.git \
    && cd PHP-CPP \
    && make -j4 \
    && make install \
    && cd .. \
    # Pulsar CPP Client
    && git clone --depth 1 https://github.com/apache/pulsar.git \
    && cd pulsar/pulsar-client-cpp \
    && cmake . -DBUILD_TESTS=OFF \
    && make -j2 \
    && make install \
    && cd ../.. \
    && git clone https://gitlab.com/minds/pulsar-php-client \
    && cd pulsar-php-client \
    && make -j4 \
    && cp output/pulsar.so $(php-config --extension-dir)/pulsar.so \
    && cd .. \
    && rm -rf PHP-CPP pulsar pulsar-php-client \
    && apk del build-deps

RUN apk add protobuf-dev

# PHP INI
COPY php.ini /usr/local/etc/php/
COPY opcache.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
COPY pulsar.ini /usr/local/etc/php/conf.d/pulsar.ini

WORKDIR /var/www/Minds
