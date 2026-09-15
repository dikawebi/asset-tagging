#!/usr/bin/env bash
# exit on error
set -o errexit

# Install Node dependencies and build frontend assets
npm ci
npm run build

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force
