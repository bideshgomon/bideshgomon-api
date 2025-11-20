#!/bin/bash

# ============================================
# Bidesh Gomon - Automated VPS Setup Script
# ============================================

set -e  # Exit on any error

echo "======================================"
echo "  Bidesh Gomon VPS Auto-Setup"
echo "======================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}➜ $1${NC}"
}

# Get user inputs
print_info "Enter MySQL root password (leave empty if not set yet):"
read -s MYSQL_ROOT_PASS
echo ""

print_info "Enter new database password for bideshgomon_user:"
read -s DB_PASSWORD
echo ""

print_info "Enter your domain (or press Enter to use IP only):"
read DOMAIN
if [ -z "$DOMAIN" ]; then
    DOMAIN=$(curl -s ifconfig.me)
    print_info "Using IP address: $DOMAIN"
fi

echo ""
echo "======================================"
echo "Starting automated installation..."
echo "======================================"
echo ""

# Update system
print_info "Updating system packages..."
apt update && apt upgrade -y
print_success "System updated"

# Install software-properties-common
print_info "Installing prerequisites..."
apt install -y software-properties-common curl wget unzip git
print_success "Prerequisites installed"

# Add PHP repository
print_info "Adding PHP 8.2 repository..."
add-apt-repository ppa:ondrej/php -y
apt update
print_success "PHP repository added"

# Install PHP 8.2
print_info "Installing PHP 8.2 and extensions..."
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
    php8.2-mbstring php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath \
    php8.2-soap php8.2-readline php8.2-cli
print_success "PHP 8.2 installed"

# Install MySQL
print_info "Installing MySQL/MariaDB..."
apt install -y mysql-server
systemctl start mysql
systemctl enable mysql
print_success "MySQL installed and started"

# Install Node.js 22
print_info "Installing Node.js 22..."
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs
print_success "Node.js $(node -v) installed"

# Install Composer
print_info "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
print_success "Composer installed"

# Install Nginx
print_info "Installing Nginx..."
apt install -y nginx
systemctl start nginx
systemctl enable nginx
print_success "Nginx installed and started"

# Configure Firewall
print_info "Configuring firewall..."
ufw --force enable
ufw allow OpenSSH
ufw allow 'Nginx Full'
print_success "Firewall configured"

# Setup MySQL Database
print_info "Setting up MySQL database..."
if [ -z "$MYSQL_ROOT_PASS" ]; then
    # No root password set yet
    mysql <<EOF
DROP DATABASE IF EXISTS bideshgomon;
CREATE DATABASE bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'bideshgomon_user'@'localhost';
DROP USER IF EXISTS 'bidesh_gomon_user'@'localhost';
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';
FLUSH PRIVILEGES;
EOF
else
    # Root password already set
    mysql -u root -p"${MYSQL_ROOT_PASS}" <<EOF
DROP DATABASE IF EXISTS bideshgomon;
CREATE DATABASE bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'bideshgomon_user'@'localhost';
DROP USER IF EXISTS 'bidesh_gomon_user'@'localhost';
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';
FLUSH PRIVILEGES;
EOF
fi
print_success "Database created: bideshgomon"
print_success "Database user created: bideshgomon_user"

# Create web directory
print_info "Creating web directory..."
mkdir -p /var/www/bideshgomon
cd /var/www/bideshgomon
print_success "Directory created: /var/www/bideshgomon"

# Check if project files exist
if [ ! -f "composer.json" ]; then
    print_error "Project files not found in /var/www/bideshgomon"
    print_info "Please upload your project files first, then run this script again."
    print_info "You can upload using: scp -r bideshgomon-saas/* root@${DOMAIN}:/var/www/bideshgomon/"
    exit 1
fi

# Set permissions
print_info "Setting file permissions..."
chown -R www-data:www-data /var/www/bideshgomon
chown -R $SUDO_USER:www-data /var/www/bideshgomon
find /var/www/bideshgomon -type d -exec chmod 755 {} \;
find /var/www/bideshgomon -type f -exec chmod 644 {} \;
chmod -R 775 /var/www/bideshgomon/storage
chmod -R 775 /var/www/bideshgomon/bootstrap/cache
print_success "Permissions set"

# Install dependencies
print_info "Installing Composer dependencies..."
cd /var/www/bideshgomon
composer install --optimize-autoloader --no-dev --no-interaction
print_success "Composer dependencies installed"

print_info "Installing NPM dependencies..."
npm install
print_success "NPM dependencies installed"

print_info "Building frontend assets..."
npm run build
print_success "Frontend built successfully"

# Configure environment
print_info "Configuring environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Update .env file
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s|APP_URL=.*|APP_URL=http://${DOMAIN}|" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=bideshgomon/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=bideshgomon_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env

# Generate app key
php artisan key:generate --force
print_success "Environment configured"

# Run migrations
print_info "Running database migrations..."
php artisan migrate --force
print_success "Migrations completed"

# Seed demo data
print_info "Seeding demo data..."
php artisan db:seed --class=DemoUserSeeder --force
print_success "Demo account created"

# Cache configuration
print_info "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized"

# Configure Nginx
print_info "Configuring Nginx..."
cat > /etc/nginx/sites-available/bideshgomon <<'NGINX_EOF'
server {
    listen 80;
    listen [::]:80;
    
    server_name DOMAIN_PLACEHOLDER;
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

# Replace domain placeholder
sed -i "s/DOMAIN_PLACEHOLDER/${DOMAIN}/" /etc/nginx/sites-available/bideshgomon

# Enable site
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/bideshgomon /etc/nginx/sites-enabled/

# Test Nginx config
nginx -t
print_success "Nginx configured"

# Restart services
print_info "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm
print_success "Services restarted"

# Final permissions check
chown -R www-data:www-data /var/www/bideshgomon/storage
chown -R www-data:www-data /var/www/bideshgomon/bootstrap/cache

echo ""
echo "======================================"
echo "  ✓ Installation Complete!"
echo "======================================"
echo ""
echo -e "${GREEN}Your application is now live at:${NC}"
echo -e "${YELLOW}http://${DOMAIN}${NC}"
echo ""
echo -e "${GREEN}Demo Account:${NC}"
echo "  Email: demo@bideshgomon.com"
echo "  Password: password123"
echo ""
echo -e "${GREEN}Database Details:${NC}"
echo "  Database: bideshgomon"
echo "  Username: bideshgomon_user"
echo "  Password: ${DB_PASSWORD}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "  1. Visit http://${DOMAIN} in your browser"
echo "  2. Login with demo account"
echo "  3. Test all profile sections"
echo "  4. Setup SSL with: certbot --nginx -d yourdomain.com"
echo ""
echo "======================================"
