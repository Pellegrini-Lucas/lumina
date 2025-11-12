# Usar imagen oficial de PHP con FPM
FROM php:8.4-fpm

# Argumentos de build
ARG NODE_VERSION=20

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    libzip-dev \
    libicu-dev \
    sqlite3 \
    zip \
    unzip \
    && curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd intl zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos composer primero (para aprovechar cache de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias de PHP (solo producción)
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copiar archivos package.json
COPY package.json package-lock.json ./

# Instalar dependencias de Node
RUN npm ci --omit=dev

# Copiar el resto de archivos de la aplicación
COPY . .

# Compilar assets
RUN npm run build && rm -rf node_modules

# Ejecutar scripts post-install de composer
RUN composer dump-autoload --optimize

# Crear directorio para SQLite y dar permisos
RUN mkdir -p /var/www/database && \
    touch /var/www/database/database.sqlite && \
    chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Copiar y dar permisos al script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exponer puerto (Render usa la variable PORT)
EXPOSE 8080

# Comando para ejecutar la aplicación
CMD ["/usr/local/bin/docker-entrypoint.sh"]
