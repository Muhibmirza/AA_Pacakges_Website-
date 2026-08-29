FROM php:8.3-cli

RUN docker-php-ext-install pdo_mysql

COPY . /var/www/html/
COPY deploy/entrypoint.sh /usr/local/bin/aapackages-entrypoint

RUN chmod +x /usr/local/bin/aapackages-entrypoint \
    && mkdir -p /var/www/html/storage/logs /var/www/html/storage/rate_limits /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/uploads

ENV AA_BASE_URL=""
EXPOSE 80

ENTRYPOINT ["aapackages-entrypoint"]
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html", "/var/www/html/deploy/router.php"]
