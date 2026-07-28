# EnPharChem Platform - single image for cloud & on-premise
FROM php:8.2-apache

# PHP extensions + MySQL client (used by the entrypoint to import schema)
RUN apt-get update && apt-get install -y --no-install-recommends \
        default-mysql-client curl \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# App is served under /enpharchem to preserve the application's base-path URLs
COPY . /var/www/html/enpharchem/
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html

# Safe defaults; override in compose / cloud runtime
ENV APP_URL=http://localhost:8080/enpharchem \
    DB_HOST=db \
    DB_PORT=3306 \
    DB_NAME=enpharchem \
    DB_USER=enpharchem \
    DB_PASS=change-me \
    FLOWSHEET_LICENSE_ENFORCE=true

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
