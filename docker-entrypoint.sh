#!/bin/sh
set -eu

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY must be set to a persistent Laravel application key." >&2
    exit 1
fi

export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
export DB_DATABASE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"

if [ "$DB_CONNECTION" = "sqlite" ]; then
    db_directory="$(dirname "$DB_DATABASE")"
    mkdir -p "$db_directory"
    touch "$DB_DATABASE"
    chown www-data:www-data "$db_directory" "$DB_DATABASE"
fi

chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force
php artisan db:seed --class=RbacSeeder --force

exec "$@"
