MathCrack Deployment Guide

Manual Deployment

1 Download ZIP from GitHub
2 Extract files
3 Upload to server

If server supports terminal run:

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

If no terminal access
Build locally before upload:

composer install --no-dev --optimize-autoloader
npm install
npm run build

Do not upload:

.env
node_modules
storage/framework/*
storage/logs/*
public/hot

