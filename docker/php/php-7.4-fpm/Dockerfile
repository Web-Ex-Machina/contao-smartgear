ARG image
################################
# Minimal image
################################
FROM $image AS php_base
ARG workdir_bundle
ARG workdir_bundle_end_slash

RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

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
    # Use the default development configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    # Install Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && mkdir -p $workdir_bundle \
    && cd $workdir_bundle

WORKDIR $workdir_bundle_end_slash

# ENTRYPOINT ["/docker-entrypoint.sh"]
################################
# Full image base for test & dev
################################
FROM php_base AS php_base_full
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
ARG php_ini_max_execution_time
ARG php_ini_memory_limit
ARG php_ini_disable_functions
ARG php_ini_post_max_size
ARG php_ini_upload_max_filesize
ARG php_ini_max_input_vars

# Here we can have additional work to be done specifically for the test container
RUN apt-get update && apt-get install -y \
    git \
    npm \
    && mkdir -p ~/.ssh \
    && mkdir -p /var/www/.npm \
    && ssh-keyscan github.com >> ~/.ssh/known_host \
    # Install contao
    cd / \
    && mkdir -p $workdir_init \
    && mkdir -p $workdir_contao \
    # We give the www-data the same user UUID as ours
    && usermod -u 1000 www-data \
    && groupmod -g 1000 www-data \
    && chown -R www-data:www-data $workdir_contao \
    && chown -R www-data:www-data /usr/local/bin/composer \
    && chown -R www-data:www-data /var/www/.npm \
    # get contao into a fake dir and then move it to the real install-dir
    && mkdir -p /fake \
    && cd /fake \
    && composer create-project contao/managed-edition . $contao_version \
    # if "public" directory is present (contao >= 4.12)
    && [ -d /fake/public ] && [ -f /fake/public/contao-manager.phar.php ] && echo "contao manager already present." || mkdir -p /fake/public && curl -o /fake/public/contao-manager.phar.php https://download.contao.org/contao-manager/stable/contao-manager.phar \
    && chown -R www-data:www-data /fake \
    && rm -r $workdir_contao/* \
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
    && chown -R www-data:www-data $workdir_contao \
    && sed -i -r -e "s/([;]{0,1})max_execution_time =(.*)/max_execution_time = $php_ini_max_execution_time/" $PHP_INI_DIR/php.ini \
    && sed -i -r -e "s/([;]{0,1})memory_limit =(.*)/memory_limit = $php_ini_memory_limit/" $PHP_INI_DIR/php.ini \
    && sed -i -r -e "s/([;]{0,1})disable_functions =(.*)/disable_functions = $php_ini_disable_functions/" $PHP_INI_DIR/php.ini \
    && sed -i -r -e "s/([;]{0,1})post_max_size =(.*)/post_max_size = $php_ini_post_max_size/" $PHP_INI_DIR/php.ini \
    && sed -i -r -e "s/([;]{0,1})upload_max_filesize =(.*)/upload_max_filesize = $php_ini_upload_max_filesize/" $PHP_INI_DIR/php.ini \
    && sed -i -r -e "s/([;]{0,1})max_input_vars =(.*)/max_input_vars = $php_ini_max_input_vars/" $PHP_INI_DIR/php.ini

WORKDIR $workdir_contao

CMD php-fpm -F
################################
# Image for test
################################
FROM php_base_full AS php_test

WORKDIR $workdir_contao

CMD php-fpm -F

################################
# Image for dev
################################
FROM php_base_full AS php_dev

RUN apt-get update && apt-get install -y nano

WORKDIR $workdir_contao

CMD php-fpm -F

################################
# Image for unit tests
################################
FROM php_base AS php_test_unit

WORKDIR $workdir_end_slash

# ENTRYPOINT ["/docker-entrypoint.sh"]
