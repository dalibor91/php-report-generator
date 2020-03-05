FROM debian:9

MAINTAINER Dalibor Menkovic <dalibor.menkovic@gmail.com>

RUN apt-get update && \
    apt-get install -y \
    locales \
    apt-transport-https \
    lsb-release \
    ca-certificates \
    gpg \
    curl \
    wget \
    apache2 \
    zip \
    unzip \
    nfs-client \
    python-pip \
    python-yaml \
    locales \
    cron

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" >> /etc/apt/sources.list.d/php.list

RUN apt-get update && \
    apt-get install -y \
    php7.3 php-common php7.3-cli php7.3-mysql \
    php-pear php7.3-dev php7.3-gd php7.3-memcached \
    php-gettext php7.3-xml php7.3-zip \
    libapache2-mod-php7.3 php7.3-curl \
    php-intl php7.3-imagick php7.3-mbstring php7.3-mongodb php7.3-json php-log

RUN php -r " file_put_contents('cs.php', file_get_contents('https://getcomposer.org/installer')); if (hash_file('SHA384', 'cs.php') !== trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Corupted'.PHP_EOL; unlink('cs.php'); } " && \
    php cs.php --install-dir=/usr/local/bin --filename=composer  && \
    rm cs.php

ADD . /opt/app

WORKDIR /opt/app

RUN composer install

CMD ["/bin/bash"]