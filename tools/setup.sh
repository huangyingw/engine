#!/bin/sh

# Clear vendor cache
rm -rf ../vendor

# Setup composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    php composer.phar install --ignore-platform-reqs
