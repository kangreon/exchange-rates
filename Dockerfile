FROM php:7
RUN apt-get update -y && apt-get install -y openssl zip unzip git cron
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN apt-get install -y libpq-dev
RUN docker-php-ext-install pdo mbstring pdo_pgsql pgsql
WORKDIR /app
COPY . /app
RUN composer install

ADD crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron
RUN chmod +x /etc/cron.d/laravel-cron
RUN touch /var/log/cron.log

CMD cron && php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181
