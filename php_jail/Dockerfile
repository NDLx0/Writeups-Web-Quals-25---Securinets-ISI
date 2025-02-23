FROM php:8.1-apache
WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod 444 /var/www/html/flag.txt
# Add custom Apache configuration to block flag.txt
COPY apache-config.conf /etc/apache2/conf-available/
RUN a2enconf apache-config
EXPOSE 2020
CMD ["apache2-foreground"]

