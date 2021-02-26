FROM php:7.2-apache

#Install php-redis
#RUN pecl install redis curl
#RUN docker-php-ext-install mysqli
#RUN docker-php-ext-enable redis
#RUN \
#    apt-get update && \
#    apt-get install libldap2-dev -y && \
#    rm -rf /var/lib/apt/lists/* && \
#    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
#    docker-php-ext-install ldap

#Add php.ini file
#COPY php.ini $PHP_INI_DIR/php.ini

#Move in source code
COPY ./html /var/www/html

#Enable rewrite 
RUN a2enmod rewrite
RUN cat /etc/issue
