#!/bin/bash

# ============================================
# Quick Deploy Script (For Updates)
# ============================================

set -e

echo "======================================"
echo "  Deploying Bidesh Gomon..."
echo "======================================"

cd /var/www/bideshgomon

# Enable maintenance mode
php artisan down

echo "✓ Maintenance mode enabled"

# Pull latest changes (if using Git)
if [ -d ".git" ]; then
    git pull origin main
    echo "✓ Git pulled"
fi

# Install/update dependencies
composer install --optimize-autoloader --no-dev --no-interaction
echo "✓ Composer updated"

npm install
echo "✓ NPM updated"

npm run build
echo "✓ Frontend built"

# Run migrations
php artisan migrate --force
echo "✓ Migrations run"

# Clear and cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Cache refreshed"

# Set permissions
chown -R www-data:www-data /var/www/bideshgomon/storage
chown -R www-data:www-data /var/www/bideshgomon/bootstrap/cache
chmod -R 775 /var/www/bideshgomon/storage
chmod -R 775 /var/www/bideshgomon/bootstrap/cache
echo "✓ Permissions set"

# Restart services
systemctl restart php8.2-fpm
echo "✓ Services restarted"

# Disable maintenance mode
php artisan up

echo ""
echo "======================================"
echo "  ✓ Deployment Complete!"
echo "======================================"
