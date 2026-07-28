git fetch origin
git reset --hard origin/main

composer update --no-dev --prefer-dist --optimize-autoloader

php artisan migrate --force

npm ci

npm run build

php artisan optimize
