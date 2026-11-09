# Apache with PHP baked in (mod_php) — simplest setup for a teaching quickstart.
# Apache = web tier (speaks HTTP). PHP = application tier (runs your code).
# They live in one container here for simplicity; see README for how to split
# them into separate Apache + PHP-FPM containers if you want to teach that
# tier separation explicitly.

FROM php:8.2-apache

# git + a MariaDB client (used by the editor and terminal in Codespaces)
RUN apt-get update \
 && apt-get install -y --no-install-recommends git mariadb-client \
 && rm -rf /var/lib/apt/lists/*

# Common extensions students will need for a typical CRUD app
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable .htaccess / mod_rewrite (needed for any routing, login redirects, etc.)
RUN a2enmod rewrite

# Show errors on screen — fine for a local teaching sandbox, NEVER in production
RUN { \
    echo 'display_errors = On'; \
    echo 'error_reporting = E_ALL'; \
} > /usr/local/etc/php/conf.d/dev-errors.ini

WORKDIR /var/www/html
