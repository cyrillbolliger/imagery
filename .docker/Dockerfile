FROM php:8.0-apache

# update the repository sources list
# and install basic dependencies
RUN apt-get update -y \
  && apt-get install -y \
    zip \
    openssl \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libmagickwand-dev --no-install-recommends

# install & enable mysql driver
RUN docker-php-ext-install pdo_mysql

# install & enable xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# install imagick
#RUN pecl install imagick
# use github version for now until release from https://pecl.php.net/get/imagick is ready for PHP 8
# see https://github.com/Imagick/imagick/issues/331#issuecomment-779190777
RUN mkdir -p /usr/src/php/ext/imagick; \
    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
    docker-php-ext-install imagick
RUN docker-php-ext-enable imagick

# install GD (for laravel tests)
RUN docker-php-ext-configure gd --enable-gd --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

# install bcmath extension
RUN docker-php-ext-install bcmath

# install zip extension
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# install intl extension
RUN docker-php-ext-install intl

# install composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# activate mod rewrite
RUN a2enmod rewrite

# set our application folder as an environment variable
ENV APP_HOME /var/www/html

# set the working dir
WORKDIR $APP_HOME

# change ownership of our application
RUN chown -R www-data:www-data $APP_HOME

# point the document root to the public directory
ENV APACHE_DOCUMENT_ROOT ${APP_HOME}/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
