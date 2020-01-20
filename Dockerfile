FROM 698164693695.dkr.ecr.us-east-1.amazonaws.com/cp4/php:latest


WORKDIR /application
RUN mkdir -p config src public bin uploads
RUN mkdir -p /var/log/cp4
COPY config ./config/ 
COPY src ./src/
COPY translations ./translations
COPY public ./public
COPY bin ./bin
COPY uploads ./uploads
COPY composer.json composer.lock symfony.lock .env.dist ./
RUN chown -R www-data ./uploads
RUN chown -R www-data /var/log/cp4
#RUN printf "catch_workers_output = yes\nphp_flag[display_errors] = on\nphp_admin_value[error_log] = /var/log/fpm-php.www.log\nphp_admin_flag[log_errors] = on\nenv[APP_ENV] = /application/.env" >> /usr/local/etc/php-fpm.d/www.conf
#RUN touch /var/log/fpm-php.www.log && chmod 777 /var/log/fpm-php.www.log
RUN php -v
RUN composer install
#CMD [ "php-fpm" ]
