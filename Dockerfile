FROM richarvey/nginx-php-fpm:latest
COPY . /var/www/html
WORKDIR /var/www/html
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html
EXPOSE 80