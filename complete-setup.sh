#!/bin/bash

# ============================================
# Complete Laravel Setup
# ============================================

set -e

cd /var/www/bideshgomon

echo "======================================"
echo "  Completing Laravel Setup..."
echo "======================================"

# Update .env file
echo "Configuring environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's|APP_URL=.*|APP_URL=http://bideshgomon.com|' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=bideshgomon/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=bideshgomon_user/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=Nakib##123@@@/' .env

# Generate app key
php artisan key:generate --force

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/bideshgomon
chmod -R 755 /var/www/bideshgomon
chmod -R 775 /var/www/bideshgomon/storage
chmod -R 775 /var/www/bideshgomon/bootstrap/cache

# Install Composer dependencies
echo "Installing Composer dependencies..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --optimize-autoloader --no-dev --no-interaction

# Install NPM dependencies
echo "Installing NPM dependencies..."
npm install --legacy-peer-deps

# Build frontend
echo "Building frontend assets..."
npm run build

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Seed demo data
echo "Seeding demo account..."
php artisan db:seed --class=DemoUserSeeder --force

# Cache everything
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Final permissions
chown -R www-data:www-data /var/www/bideshgomon/storage
chown -R www-data:www-data /var/www/bideshgomon/bootstrap/cache

# Configure Nginx
echo "Configuring Nginx..."
cat > /etc/nginx/sites-available/bideshgomon <<'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    
    server_name bideshgomon.com 148.135.136.95;
    root /var/www/bideshgomon/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }

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

    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
NGINX_EOF

# Enable site
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/bideshgomon /etc/nginx/sites-enabled/

# Test Nginx
nginx -t

# Restart services
echo "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

echo ""
echo "======================================"
echo "  ✓ Installation Complete!"
echo "======================================"
echo ""
echo "Your application is now live at:"
echo "  http://bideshgomon.com"
echo "  http://148.135.136.95"
echo ""
echo "Demo Account:"
echo "  Email: demo@bideshgomon.com"
echo "  Password: password123"
echo ""
echo "======================================"
