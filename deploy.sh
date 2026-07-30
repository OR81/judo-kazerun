#!/bin/bash
# متوقف کردن اسکریپت در صورت بروز هرگونه خطا
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

echo "🚀 Starting build and setup processes..."
php artisan down || true

COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

php artisan migrate --force

npm ci
npm run build

php artisan optimize
php artisan up

echo "✅ All tasks in deploy.sh completed successfully!"
