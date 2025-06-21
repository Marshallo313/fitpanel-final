# Wybór oficjalnego obrazu PHP z Apache
FROM php:8.2-apache

# Włączenie mod_rewrite
RUN a2enmod rewrite

# Skopiowanie plików do katalogu /var/www/html
COPY . /var/www/html/

# Ustawienie praw
RUN chown -R www-data:www-data /var/www/html

# Skopiowanie pliku konfiguracyjnego PHP (opcjonalnie)
# COPY ./php.ini /usr/local/etc/php/

# Otwórz port 80
EXPOSE 80