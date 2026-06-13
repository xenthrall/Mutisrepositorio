#!/bin/sh

echo "Esperando MySQL..."

until php artisan migrate:status > /dev/null 2>&1
do
  sleep 2
done

echo "Generando cache ERP..."
php artisan module:cache

echo "Optimizando Laravel..."
php artisan optimize

php artisan app:setup-roles-and-permissions

echo "Iniciando PHP-FPM..."
exec php-fpm