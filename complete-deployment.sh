#!/bin/bash

# BideshGomon Complete Deployment Script
# Run this on your server: bash complete-deployment.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo "════════════════════════════════════════════════════════════"
echo "           BideshGomon Complete Deployment"
echo "════════════════════════════════════════════════════════════"
echo ""

PROJECT_DIR="/var/www/bideshgomon-api"

echo -e "${YELLOW}1. Navigating to project directory...${NC}"
cd $PROJECT_DIR || { echo -e "${RED}Failed to navigate to project directory${NC}"; exit 1; }
echo -e "${GREEN}✓ In directory: $(pwd)${NC}"
echo ""

echo -e "${YELLOW}2. Pulling latest changes...${NC}"
git pull origin main || { echo -e "${RED}Failed to pull changes${NC}"; exit 1; }
echo -e "${GREEN}✓ Code updated${NC}"
echo ""

echo -e "${YELLOW}3. Clearing config cache...${NC}"
php artisan config:clear
php artisan cache:clear
echo -e "${GREEN}✓ Cache cleared${NC}"
echo ""

echo -e "${YELLOW}4. Creating MySQL database...${NC}"
mysql -u root -p <<EOF
CREATE DATABASE IF NOT EXISTS bideshgomondb;
CREATE USER IF NOT EXISTS 'bideshgomonuser'@'localhost' IDENTIFIED BY 'bideshgomonpassword';
GRANT ALL PRIVILEGES ON bideshgomondb.* TO 'bideshgomonuser'@'localhost';
FLUSH PRIVILEGES;
EOF
echo -e "${GREEN}✓ Database ready${NC}"
echo ""

echo -e "${YELLOW}5. Running migrations...${NC}"
php artisan migrate --force || { echo -e "${RED}Failed to run migrations${NC}"; exit 1; }
echo -e "${GREEN}✓ Database migrated${NC}"
echo ""

echo -e "${YELLOW}6. Linking storage...${NC}"
php artisan storage:link || { echo -e "${RED}Failed to link storage${NC}"; }
echo -e "${GREEN}✓ Storage linked${NC}"
echo ""

echo -e "${YELLOW}7. Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✓ Application optimized${NC}"
echo ""

echo -e "${YELLOW}8. Generating Ziggy routes...${NC}"
php artisan ziggy:generate
echo -e "${GREEN}✓ Routes generated${NC}"
echo ""

echo -e "${YELLOW}9. Fixing permissions...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions fixed${NC}"
echo ""

echo -e "${YELLOW}10. Restarting services...${NC}"
systemctl restart php8.2-fpm
systemctl restart nginx
echo -e "${GREEN}✓ Services restarted${NC}"
echo ""

echo "════════════════════════════════════════════════════════════"
echo -e "${GREEN}           ✓ DEPLOYMENT COMPLETED SUCCESSFULLY${NC}"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Your BideshGomon platform is now live!"
echo "Visit: https://bideshgomon.com"
echo ""
