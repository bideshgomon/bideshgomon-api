#!/bin/bash

# Google OAuth VPS Setup Script
# Run this on your VPS after deploying the code

echo "🔧 Setting up Google OAuth on VPS..."
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: Not in Laravel root directory${NC}"
    echo "Please run this script from /var/www/bideshgomon-api"
    exit 1
fi

echo -e "${YELLOW}📝 This script will help you configure Google OAuth${NC}"
echo ""

# Check if .env exists
if [ ! -f ".env" ]; then
    echo -e "${RED}❌ .env file not found${NC}"
    exit 1
fi

# Prompt for Google credentials
echo -e "${YELLOW}Enter your Google OAuth credentials:${NC}"
echo ""

read -p "Google Client ID: " GOOGLE_CLIENT_ID
read -p "Google Client Secret: " GOOGLE_CLIENT_SECRET
read -p "App URL (e.g., http://148.135.136.95): " APP_URL

# Add to .env if not exists
if grep -q "GOOGLE_CLIENT_ID" .env; then
    echo -e "${YELLOW}⚠️  Google OAuth variables already exist in .env${NC}"
    read -p "Do you want to update them? (y/n): " UPDATE
    
    if [ "$UPDATE" = "y" ]; then
        sed -i "s|GOOGLE_CLIENT_ID=.*|GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID}|" .env
        sed -i "s|GOOGLE_CLIENT_SECRET=.*|GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET}|" .env
        sed -i "s|GOOGLE_REDIRECT_URI=.*|GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback|" .env
        echo -e "${GREEN}✅ Updated Google OAuth credentials${NC}"
    fi
else
    echo "" >> .env
    echo "# Google OAuth" >> .env
    echo "GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID}" >> .env
    echo "GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET}" >> .env
    echo "GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback" >> .env
    echo -e "${GREEN}✅ Added Google OAuth credentials to .env${NC}"
fi

echo ""
echo -e "${YELLOW}🔄 Installing Laravel Socialite...${NC}"
composer require laravel/socialite --no-interaction

echo ""
echo -e "${YELLOW}🔄 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo ""
echo -e "${YELLOW}🔄 Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo -e "${YELLOW}🔄 Restarting PHP-FPM...${NC}"
sudo systemctl restart php8.3-fpm

echo ""
echo -e "${YELLOW}🔄 Reloading Nginx...${NC}"
sudo systemctl reload nginx

echo ""
echo -e "${GREEN}✅ Google OAuth setup complete!${NC}"
echo ""
echo -e "${YELLOW}📋 Important reminders:${NC}"
echo "1. Add this redirect URI in Google Cloud Console:"
echo "   ${APP_URL}/auth/google/callback"
echo ""
echo "2. Add this as authorized origin:"
echo "   ${APP_URL}"
echo ""
echo "3. Test the login at:"
echo "   ${APP_URL}/login"
echo ""
echo "4. Monitor logs for errors:"
echo "   tail -f storage/logs/laravel.log"
echo ""
echo -e "${GREEN}🎉 Done!${NC}"
