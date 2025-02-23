FROM ubuntu:20.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y \
    apache2 \
    php7.4 \
    libapache2-mod-php7.4 \
    php7.4-mysql \
    php7.4-common \
    && rm -rf /var/lib/apt/lists/*
RUN a2enmod php7.4 rewrite
WORKDIR /var/www/html
COPY src/ /var/www/html/
RUN rm -f /var/www/html/index.html && \
    echo "DirectoryIndex index.php" > /etc/apache2/conf-available/directory-index.conf && \
    a2enconf directory-index
COPY flag.txt /var/flag.txt
RUN chown -R www-data:www-data /var/www/html /var/flag.txt && \
    chmod -R 775 /var/www/html/uploads && \
    chmod 644 /var/flag.txt
RUN mkdir -p /var/lib/php/sessions && \
    chown www-data:www-data /var/lib/php/sessions && \
    chmod 733 /var/lib/php/sessions
EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
