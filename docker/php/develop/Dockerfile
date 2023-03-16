FROM --platform=linux/amd64 php:8.2.3-fpm
RUN pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libzip-dev \
    unzip \
    wget \
    xfonts-75dpi \
    xfonts-base \
    gnupg \
    vim \
    default-mysql-client \
    less \
    cron

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN pecl install zlib zip
RUN docker-php-ext-install pdo pdo_mysql gd zip
RUN touch /usr/local/etc/php/conf.d/uploads.ini && echo "upload_max_filesize = 200M;" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 200M;" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "memory_limit = 500M;" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "max_input_vars = 6000;" >> /usr/local/etc/php/conf.d/uploads.ini

ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="1" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="10000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10" \
    PHP_OPCACHE_ENABLE="0"

RUN docker-php-ext-install opcache
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0"
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# (Total RAM – Memory used) / process size
# (2000 - 500) / 80
RUN echo 'pm.max_children = 15' >> /usr/local/etc/php-fpm.d/zz-docker.conf && \
    echo 'pm = static' >> /usr/local/etc/php-fpm.d/zz-docker.conf

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN addgroup --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user
RUN usermod -aG www-data user

## Install NGINX Version
ENV NGINX_VERSION="1.23.3-1~bullseye"
RUN wget http://nginx.org/keys/nginx_signing.key
RUN apt-key add nginx_signing.key
RUN wget -O- https://nginx.org/keys/nginx_signing.key | gpg --dearmor | tee /usr/share/keyrings/nginx-archive-keyring.gpg
RUN echo deb http://nginx.org/packages/mainline/debian bullseye nginx | tee /etc/apt/sources.list.d/nginx-mainline.list
RUN apt-get update
RUN apt-get install -y nginx=${NGINX_VERSION}
RUN mkdir -p /var/cache/nginx
RUN chown root:www-data /etc/nginx /etc/nginx/conf.d/ /etc/nginx/nginx.conf  && chmod 775 /etc/nginx/ /etc/nginx/conf.d/ && chmod 664 /etc/nginx/nginx.conf
RUN chown user:www-data /var/log/nginx/access.log && chown user:www-data /var/log/nginx/error.log && chown user:www-data -R /var/cache/nginx && chown user:www-data -R /run

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --2
RUN curl -sL https://deb.nodesource.com/setup_19.x | bash -
RUN apt-get -y install nodejs
RUN node -v
RUN npm -v

RUN mkdir /var/www/laravel && chown -R user:user /var/www/laravel &&  chown user:user /etc/init.d/nginx

USER user

COPY --chown=user:user ./app /var/www/laravel/app
COPY --chown=user:www-data ./bootstrap /var/www/laravel/bootstrap
COPY --chown=user:user ./config /var/www/laravel/config
COPY --chown=user:user ./database /var/www/laravel/database
COPY --chown=user:www-data ./public /var/www/laravel/public
COPY --chown=user:user ./resources /var/www/laravel/resources
COPY --chown=user:user ./routes /var/www/laravel/routes
COPY --chown=user:www-data ./storage /var/www/laravel/storage
COPY --chown=user:user ./tests /var/www/laravel/tests
COPY --chown=user:user ./.env /var/www/laravel/
COPY --chown=user:user ./artisan /var/www/laravel/
COPY --chown=user:user ./composer.json /var/www/laravel/
COPY --chown=user:user ./composer.lock /var/www/laravel/
COPY --chown=user:user ./package.json /var/www/laravel/
COPY --chown=user:user ./vite.config.js /var/www/laravel/
COPY --chown=user:user  ./phpunit.xml /var/www/laravel/
WORKDIR /var/www/laravel

RUN chmod -R 775 storage
RUN chmod -R 775 bootstrap/cache

RUN composer install

CMD /etc/init.d/nginx start ; php-fpm
