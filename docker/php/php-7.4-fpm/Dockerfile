ARG image
FROM $image AS php_base
ARG workdir_bundle
ARG workdir_bundle_end_slash

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libicu-dev \
        zip \
        unzip \
        libzip-dev \
        libxml2-dev \
        libgmp-dev \
        re2c \
        libmhash-dev \
        file \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure pdo \
    && docker-php-ext-install pdo \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install pdo_mysql\
    && docker-php-ext-configure gmp \
    && docker-php-ext-install gmp \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip
    # already loaded
    # && docker-php-ext-configure xml \
    # && docker-php-ext-install xml \
    # && docker-php-ext-configure dom \
    # && docker-php-ext-install dom \
    # added recently
    # && docker-php-ext-configure pcre \
    # && docker-php-ext-install pcre \
    # && docker-php-ext-configure zlib \
    # && docker-php-ext-install zlib \
    # && docker-php-ext-configure json \
    # && docker-php-ext-install json \
    # && docker-php-ext-configure curl \
    # && docker-php-ext-install curl \
    # && docker-php-ext-configure mbstring \
    # && docker-php-ext-instabill mbstring 
    # 
    # Use the default development configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    # Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p $workdir_bundle \
    && cd $workdir_bundle

WORKDIR $workdir_bundle_end_slash

# ENTRYPOINT ["/docker-entrypoint.sh"]

FROM php_base AS php_test
ARG database_url
ARG db_user
ARG db_password
ARG db_database
ARG db_host
ARG app_secret
ARG contao_version
ARG workdir_contao
ARG workdir_simlink_base
ARG workdir_bundle
ARG workdir_init

# Here we can have additional work to be done specifically for the test container
# Install contao
RUN cd / \
    && mkdir -p $workdir_contao \
    # We give the www-data the same user UUID as ours
    && usermod -u 1000 www-data \
    && groupmod -g 1000 www-data \
    && chown -R www-data:www-data $workdir_contao \
    && chown -R www-data:www-data /usr/local/bin/composer \
    # get contao into a fake dir and then move it to the real install-dir
    && mkdir -p /fake \
    && cd /fake \
    && composer create-project contao/managed-edition . $contao_version \
    && curl -o web/contao-manager.phar.php https://download.contao.org/contao-manager/stable/contao-manager.phar \
    && cp -ar /fake/. $workdir_contao \
    && cd $workdir_contao \
    # Also clean up cache
    && rm -fr /var/cache/*

COPY --chown=www-data:www-data ./.env $workdir_contao/.env

RUN sed -i "s~{{DB_USER}}~$db_user~g" $workdir_contao/.env \
    && sed -i "s~{{DB_PASSWORD}}~$db_password~g" $workdir_contao/.env \
    && sed -i "s~{{DB_DATABASE}}~$db_database~g" $workdir_contao/.env \
    && sed -i "s~{{DB_HOST}}~$db_host~g" $workdir_contao/.env \
    && sed -i "s~{{APP_SECRET}}~$app_secret~g" $workdir_contao/.env \
    && chown -R www-data:www-data $workdir_contao 
    # \
    # && mkdir -p $workdir_simlink_base \
    # && chown -R www-data:www-data $workdir_simlink_base \
    # && runuser -u www-data -- ln -sf $workdir_bundle $workdir_simlink_base

WORKDIR $workdir_contao

CMD php-fpm -F

FROM php_base AS php_test_unit

# Here we can have additional work to be done specifically for the test container

WORKDIR $workdir_end_slash

# ENTRYPOINT ["/docker-entrypoint.sh"]
