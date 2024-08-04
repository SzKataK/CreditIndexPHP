FROM php:apache
COPY --chown=root:root . /var/www/html