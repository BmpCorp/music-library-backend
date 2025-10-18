FROM php:8.3.4-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libldap2-dev curl wget git htop cron jpegoptim optipng pngquant gifsicle gnupg \
    build-essential python3 python3-full python3-dev python3-pip python3-setuptools \
    python3-wheel python3-cffi libcairo2 libpango-1.0-0 libpangocairo-1.0-0 libgdk-pixbuf2.0-0 \
    libffi-dev libxml2-dev shared-mime-info libfreetype6-dev libwebp-dev libjpeg62-turbo-dev \
    libpng-dev libonig-dev libzip-dev libmcrypt-dev libjudy-dev libmagickwand-dev zip unzip \
    nano graphicsmagick imagemagick ghostscript redis-tools cron supervisor default-mysql-client \
    weasyprint \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions ftp gd redis ldap soap bcmath exif iconv mbstring mysqli \
       pdo_mysql zip xml simplexml xmlreader pcntl sodium sockets xdebug Imagick/imagick@master

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/log/php /var/www/backend \
    && touch /var/log/php/error.log \
    && chown -R www-data:www-data /var/log/php /var/www \
    && chmod -R 755 /var/log/php /var/www

WORKDIR /var/www/backend
COPY entrypoint.sh /root/entrypoint.sh
RUN chmod +x /root/entrypoint.sh
CMD ["/root/entrypoint.sh"]

