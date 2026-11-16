# Image Manager - PHP + Apache
FROM php:8.3-apache

# mysqli is needed to talk to MySQL
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

# Never execute PHP inside the uploads folder (defence in depth)
RUN printf '<Directory /var/www/html/uploads>\n    php_admin_flag engine off\n    Options -ExecCGI\n</Directory>\n' \
      > /etc/apache2/conf-available/uploads-no-php.conf \
 && a2enconf uploads-no-php

# Copy only the application source (see .dockerignore)
COPY src/ ./

# Uploads must be writable by the Apache user
RUN mkdir -p uploads && chown -R www-data:www-data uploads

EXPOSE 80

HEALTHCHECK --interval=15s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -fs http://localhost/login.php > /dev/null || exit 1

# Apache in the foreground (base image entrypoint)
CMD ["apache2-foreground"]
