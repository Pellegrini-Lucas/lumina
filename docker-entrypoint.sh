#!/bin/bash

set -e

echo "🚀 Iniciando aplicación Laravel..."

# Crear directorio de base de datos si no existe
mkdir -p /var/www/database
touch /var/www/database/database.sqlite

# Dar permisos
chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/database

# Limpiar cache anterior
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar aplicación
echo "🔧 Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Crear usuario admin si no existe (sin usar seeders)
echo "👤 Creando usuario administrador..."
php artisan admin:create "pepe@dba.com" "password" "Pepe - El Mejor Alumno"

# Usar variable PORT de Render, o 8080 por defecto
PORT=${PORT:-8080}

# Iniciar servidor
echo "✅ Servidor listo en puerto $PORT"
php artisan serve --host=0.0.0.0 --port=$PORT
