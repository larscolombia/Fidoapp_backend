# Usa la imagen base oficial de PHP con soporte para Laravel
FROM php:8.0-fpm

# Instalar extensiones de PHP requeridas por Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip && \
    docker-php-ext-install pdo mbstring gd

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/balance-dog

# Copiar los archivos de Laravel al contenedor
COPY . .

# Asignar permisos
RUN chown -R www-data:www-data /var/www/balance-dog \
    && chmod -R 755 /var/www/balance-dog/storage

# Exponer el puerto 9000 (PHP-FPM)
EXPOSE 9000

# Iniciar PHP-FPM
CMD ["php-fpm"]
