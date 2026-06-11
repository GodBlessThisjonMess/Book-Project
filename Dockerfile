# Gunakan image PHP resmi dengan Apache
FROM php:8.2-apache

# Instal dependensi sistem dan ekstensi PHP pdo_mysql
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -y gd pdo pdo_mysql

# Aktifkan Apache mod_rewrite untuk perutean (.htaccess)
RUN a2enmod rewrite

# Ubah konfigurasi Apache agar mengizinkan .htaccess (AllowOverride All)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Tentukan direktori kerja aplikasi
WORKDIR /var/www/html

# Salin seluruh kode sumber proyek ke kontainer
COPY . /var/www/html

# Buat direktori unggah foto profil (avatar) dan atur kepemilikan agar dapat ditulis oleh Apache
RUN mkdir -p /var/www/html/uploads/avatars \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads

# Buka port 80 untuk lalu lintas web
EXPOSE 80

# Jalankan Apache Web Server secara default
CMD ["apache2-foreground"]
