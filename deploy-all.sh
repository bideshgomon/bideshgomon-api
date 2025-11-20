#!/bin/bash

# ============================================
# Complete VPS Deployment Script
# Run this single script to deploy everything
# ============================================

set -e

echo "======================================"
echo "  Bidesh Gomon - Full Deployment"
echo "======================================"
echo ""

DB_PASSWORD="Nakib##123@@@"
APP_DOMAIN="148.135.136.95"

# Fix MySQL
echo "Step 1: Fixing MySQL..."
systemctl stop mysql 2>/dev/null || true
systemctl stop mariadb 2>/dev/null || true
rm -f /etc/mysql/FROZEN
if [ -d "/var/lib/mysql" ]; then
    mv /var/lib/mysql /var/lib/mysql.backup.$(date +%s) 2>/dev/null || true
fi
mkdir -p /var/lib/mysql
chown -R mysql:mysql /var/lib/mysql
mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql
systemctl start mysql
systemctl enable mysql
sleep 5

# Create database
echo "Step 2: Creating database..."
mysql << 'EOF'
CREATE DATABASE IF NOT EXISTS bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'bideshgomon_user'@'localhost';
DROP USER IF EXISTS 'bidesh_gomon_user'@'localhost';
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY 'Nakib##123@@@';
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "✓ Database ready"

# Setup Laravel
echo "Step 3: Setting up Laravel..."
cd /var/www/bideshgomon

# Create .env
cat > .env << 'ENVEOF'
APP_NAME="Bidesh Gomon"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://148.135.136.95

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon
DB_USERNAME=bideshgomon_user
DB_PASSWORD=Nakib##123@@@

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@bideshgomon.com"
MAIL_FROM_NAME="${APP_NAME}"
ENVEOF

# Generate key
php artisan key:generate --force

# Set permissions
echo "Step 4: Setting permissions..."
chown -R www-data:www-data /var/www/bideshgomon
chmod -R 755 /var/www/bideshgomon
chmod -R 775 /var/www/bideshgomon/storage
chmod -R 775 /var/www/bideshgomon/bootstrap/cache

# Install dependencies
echo "Step 5: Installing dependencies..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --optimize-autoloader --no-dev --no-interaction --quiet

echo "Step 6: Installing NPM packages..."
npm install --legacy-peer-deps --quiet

echo "Step 7: Building frontend..."
npm run build

# Database
echo "Step 8: Running migrations..."
php artisan migrate --force

echo "Step 9: Seeding demo account..."
php artisan db:seed --class=DemoUserSeeder --force

# Cache
echo "Step 10: Caching..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Nginx
echo "Step 11: Configuring Nginx..."
cat > /etc/nginx/sites-available/bideshgomon <<'NGINXEOF'
server {
    listen 80;
    listen [::]:80;
    
    server_name 148.135.136.95 bideshgomon.com;
    root /var/www/bideshgomon/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
NGINXEOF

rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/bideshgomon /etc/nginx/sites-enabled/
nginx -t

# Restart services
echo "Step 12: Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

# Final permissions
chown -R www-data:www-data /var/www/bideshgomon/storage
chown -R www-data:www-data /var/www/bideshgomon/bootstrap/cache

echo ""
echo "======================================"
echo "  ✓ DEPLOYMENT COMPLETE!"
echo "======================================"
echo ""
echo "🌐 Your site is live at:"
echo "   http://148.135.136.95"
echo ""
echo "👤 Demo Account:"
echo "   Email: demo@bideshgomon.com"
echo "   Password: password123"
echo ""
echo "======================================"
