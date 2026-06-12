#!/bin/sh
set -e
cd /var/www/html

echo ">>> [entrypoint] preparing app..."

# Install PHP dependencies if vendor/ is missing (first run).
if [ ! -f vendor/autoload.php ]; then
    echo ">>> composer install..."
    composer install --no-dev --optimize-autoloader --no-interaction
fi

# Ensure an .env exists.
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate APP_KEY if not set.
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force
fi

# Wait for the database to accept connections, then migrate.
echo ">>> waiting for database..."
until php artisan migrate --force 2>/dev/null; do
    echo "    db not ready yet, retrying in 3s..."
    sleep 3
done

php artisan storage:link 2>/dev/null || true

mkdir -p public/ads storage/framework/cache storage/framework/sessions storage/framework/views
chown -R www-data:www-data storage bootstrap/cache public/ads || true
chmod -R 775 storage bootstrap/cache public/ads || true

# Cache config/routes/views for performance.
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo ">>> [entrypoint] ready."
exec "$@"
