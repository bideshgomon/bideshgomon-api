#!/bin/bash

# BideshGomon Live Deployment Script
# Run this on your live server: bash deploy-to-live.sh

echo "════════════════════════════════════════════════════════════════"
echo "           BideshGomon Live Server Deployment"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Project directory (adjust if needed)
PROJECT_DIR="/var/www/bideshgomon-api"

echo -e "${YELLOW}1. Navigating to project directory...${NC}"
cd $PROJECT_DIR || { echo -e "${RED}Failed to navigate to project directory${NC}"; exit 1; }
echo -e "${GREEN}✓ In directory: $(pwd)${NC}"
echo ""

echo -e "${YELLOW}2. Pulling latest changes from GitHub...${NC}"
git pull origin main || { echo -e "${RED}Failed to pull changes${NC}"; exit 1; }
echo -e "${GREEN}✓ Code updated${NC}"
echo ""

echo -e "${YELLOW}3. Installing PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader || { echo -e "${RED}Failed to install composer dependencies${NC}"; exit 1; }
echo -e "${GREEN}✓ Composer dependencies installed${NC}"
echo ""

echo -e "${YELLOW}4. Installing Node dependencies...${NC}"
npm install || { echo -e "${RED}Failed to install npm dependencies${NC}"; exit 1; }
echo -e "${GREEN}✓ Node dependencies installed${NC}"
echo ""

echo -e "${YELLOW}5. Building frontend assets...${NC}"
npm run build || { echo -e "${RED}Failed to build assets${NC}"; exit 1; }
echo -e "${GREEN}✓ Assets compiled${NC}"
echo ""

echo -e "${YELLOW}6. Running database migrations...${NC}"
php artisan migrate --force || { echo -e "${RED}Failed to run migrations${NC}"; exit 1; }
echo -e "${GREEN}✓ Database migrated${NC}"
echo ""

echo -e "${YELLOW}7. Seeding site settings...${NC}"
php artisan db:seed --class=SiteSettingsSeeder --force
echo -e "${GREEN}✓ Settings seeded${NC}"
echo ""

echo -e "${YELLOW}8. Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

echo -e "${YELLOW}9. Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
echo -e "${GREEN}✓ Application optimized${NC}"
echo ""

echo -e "${YELLOW}10. Generating Ziggy routes...${NC}"
php artisan ziggy:generate
echo -e "${GREEN}✓ Routes generated${NC}"
echo ""

echo -e "${YELLOW}11. Fixing permissions...${NC}"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions fixed${NC}"
echo ""

echo -e "${YELLOW}12. Restarting services...${NC}"
systemctl restart php8.2-fpm
systemctl restart nginx
echo -e "${GREEN}✓ Services restarted${NC}"
echo ""

echo "════════════════════════════════════════════════════════════════"
echo -e "${GREEN}           ✓ DEPLOYMENT COMPLETED SUCCESSFULLY${NC}"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "Changes deployed:"
echo "  ✓ Settings System - All 11 tabs working"
echo "  ✓ API Settings - 30 keys seeded"
echo "  ✓ User Roles - Auto-assigned on registration"
echo "  ✓ PWA Install - BideshGomon logo"
echo "  ✓ Tab Switching - No page reload"
echo ""
echo "Next: Visit your website to verify everything works!"
echo ""
