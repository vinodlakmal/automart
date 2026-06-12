#!/usr/bin/env bash
#
# Pull latest code and deploy. Run from the project root on the VM:
#   ./deploy.sh
#
set -euo pipefail

BRANCH="${1:-main}"
echo ">>> Deploying branch: $BRANCH"

# 1. Pull latest code
git fetch --all
git reset --hard "origin/$BRANCH"

# 2. Maintenance mode (ignore if app not bootstrapped yet)
php artisan down || true

# 3. PHP dependencies (production)
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 4. Front-end assets
if [ -f package.json ]; then
  npm ci
  npm run build
fi

# 5. Database migrations
php artisan migrate --force

# 6. Storage symlink (idempotent)
php artisan storage:link || true

# 7. Cache config / routes / views for speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Fix permissions for the web user
sudo chown -R www-data:www-data storage bootstrap/cache public/ads
sudo chmod -R 775 storage bootstrap/cache public/ads

# 9. Bring the app back up
php artisan up

echo ">>> Deploy complete."
