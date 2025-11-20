#!/bin/bash

# Quick fix for database credentials

cd /var/www/bideshgomon

echo "Fixing database credentials..."

# Update .env with correct username
sed -i 's/DB_USERNAME=bidesh_gomon_user/DB_USERNAME=bideshgomon_user/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=bideshgomon_user/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=Nakib##123@@@/' .env

# Verify the change
echo "Current database config:"
grep "^DB_" .env

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Restart PHP
systemctl restart php8.2-fpm

echo ""
echo "✓ Fixed! Refresh your browser now."
