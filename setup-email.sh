#!/bin/bash

# Email Configuration Script for BideshGomon VPS
# This script helps you configure email sending for password resets and notifications

echo "============================================"
echo "  BideshGomon Email Configuration"
echo "============================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Choose your email service:${NC}"
echo "1) Gmail SMTP (Quick & Free)"
echo "2) Resend (Professional, 3k free/month)"
echo "3) SendGrid (Scalable, 100/day free)"
echo "4) Mailtrap (Testing only)"
echo ""
read -p "Enter choice [1-4]: " choice

case $choice in
  1)
    echo ""
    echo -e "${BLUE}=== Gmail SMTP Configuration ===${NC}"
    echo ""
    echo -e "${YELLOW}First, create a Gmail App Password:${NC}"
    echo "1. Go to https://myaccount.google.com/security"
    echo "2. Enable 2-Step Verification (if not enabled)"
    echo "3. Click 'App passwords'"
    echo "4. Generate password for 'Mail' / 'Other (BideshGomon)'"
    echo "5. Copy the 16-character password"
    echo ""
    
    read -p "Enter your Gmail address: " gmail_address
    read -p "Enter your Gmail App Password (16 chars): " gmail_password
    
    # Update .env
    sed -i "s|MAIL_MAILER=.*|MAIL_MAILER=smtp|" .env
    sed -i "s|MAIL_HOST=.*|MAIL_HOST=smtp.gmail.com|" .env
    sed -i "s|MAIL_PORT=.*|MAIL_PORT=587|" .env
    sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=${gmail_address}|" .env
    sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=${gmail_password}|" .env
    sed -i "s|MAIL_ENCRYPTION=.*|MAIL_ENCRYPTION=tls|" .env
    sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"noreply@bideshgomon.com\"|" .env
    
    echo ""
    echo -e "${GREEN}✅ Gmail SMTP configured!${NC}"
    ;;
    
  2)
    echo ""
    echo -e "${BLUE}=== Resend Configuration ===${NC}"
    echo ""
    echo -e "${YELLOW}First, get your Resend API key:${NC}"
    echo "1. Go to https://resend.com/signup"
    echo "2. Sign up and verify email"
    echo "3. Create API Key in dashboard"
    echo "4. Copy the key (starts with 're_')"
    echo ""
    
    read -p "Enter your Resend API key: " resend_key
    
    # Check if RESEND_API_KEY exists in .env
    if grep -q "RESEND_API_KEY" .env; then
        sed -i "s|RESEND_API_KEY=.*|RESEND_API_KEY=${resend_key}|" .env
    else
        echo "RESEND_API_KEY=${resend_key}" >> .env
    fi
    
    sed -i "s|MAIL_MAILER=.*|MAIL_MAILER=resend|" .env
    sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"noreply@bideshgomon.com\"|" .env
    
    echo ""
    echo -e "${GREEN}✅ Resend configured!${NC}"
    ;;
    
  3)
    echo ""
    echo -e "${BLUE}=== SendGrid Configuration ===${NC}"
    echo ""
    echo -e "${YELLOW}First, get your SendGrid API key:${NC}"
    echo "1. Go to https://signup.sendgrid.com/"
    echo "2. Sign up and verify"
    echo "3. Create API Key (Settings → API Keys)"
    echo "4. Verify Sender Identity"
    echo "5. Copy the API key (starts with 'SG.')"
    echo ""
    
    read -p "Enter your SendGrid API key: " sendgrid_key
    
    sed -i "s|MAIL_MAILER=.*|MAIL_MAILER=smtp|" .env
    sed -i "s|MAIL_HOST=.*|MAIL_HOST=smtp.sendgrid.net|" .env
    sed -i "s|MAIL_PORT=.*|MAIL_PORT=587|" .env
    sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=apikey|" .env
    sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=${sendgrid_key}|" .env
    sed -i "s|MAIL_ENCRYPTION=.*|MAIL_ENCRYPTION=tls|" .env
    sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"noreply@bideshgomon.com\"|" .env
    
    echo ""
    echo -e "${GREEN}✅ SendGrid configured!${NC}"
    ;;
    
  4)
    echo ""
    echo -e "${BLUE}=== Mailtrap Configuration ===${NC}"
    echo ""
    echo -e "${YELLOW}Get your Mailtrap credentials:${NC}"
    echo "1. Go to https://mailtrap.io/"
    echo "2. Sign up free"
    echo "3. Go to Email Testing → Inboxes → My Inbox"
    echo "4. Copy SMTP credentials"
    echo ""
    
    read -p "Enter Mailtrap username: " mailtrap_user
    read -p "Enter Mailtrap password: " mailtrap_pass
    
    sed -i "s|MAIL_MAILER=.*|MAIL_MAILER=smtp|" .env
    sed -i "s|MAIL_HOST=.*|MAIL_HOST=sandbox.smtp.mailtrap.io|" .env
    sed -i "s|MAIL_PORT=.*|MAIL_PORT=2525|" .env
    sed -i "s|MAIL_USERNAME=.*|MAIL_USERNAME=${mailtrap_user}|" .env
    sed -i "s|MAIL_PASSWORD=.*|MAIL_PASSWORD=${mailtrap_pass}|" .env
    sed -i "s|MAIL_ENCRYPTION=.*|MAIL_ENCRYPTION=tls|" .env
    sed -i "s|MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"noreply@bideshgomon.com\"|" .env
    
    echo ""
    echo -e "${GREEN}✅ Mailtrap configured!${NC}"
    ;;
    
  *)
    echo -e "${RED}Invalid choice!${NC}"
    exit 1
    ;;
esac

echo ""
echo -e "${YELLOW}Clearing configuration cache...${NC}"
php artisan config:clear
php artisan config:cache

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}  Email Configuration Complete!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${YELLOW}📧 Test it now:${NC}"
echo "1. Visit: http://148.135.136.95/forgot-password"
echo "2. Enter your email address"
echo "3. Check your inbox for password reset email"
echo ""
echo -e "${YELLOW}🐛 Debug:${NC}"
echo "tail -f storage/logs/laravel.log"
echo ""
echo -e "${GREEN}Done!${NC}"
