# 🚀 HOSTINGER VPS DEPLOYMENT GUIDE

## Your Server Information
- **IP Address:** 148.135.136.95
- **VPS Provider:** Hostinger
- **Project:** Bidesh Gomon (Laravel + Vue + Inertia)

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### On Your Local Machine (Windows)
- [x] PHP 8.2.12 installed
- [x] Node.js v22.21.0 installed
- [x] Project builds successfully (0 errors)
- [x] Demo account created and tested
- [ ] Git repository ready (optional)

---

## 🔧 STEP 1: CONNECT TO YOUR HOSTINGER VPS

### Option A: Using PuTTY (Windows)
1. Download PuTTY from: https://www.putty.org/
2. Open PuTTY
3. Enter Host: `148.135.136.95`
4. Port: `22`
5. Click "Open"
6. Login with your Hostinger credentials

### Option B: Using Windows PowerShell
```powershell
ssh root@148.135.136.95
# Enter your password when prompted
```

### Option C: Using Hostinger hPanel
1. Login to Hostinger hPanel
2. Go to "VPS" section
3. Click "Browser SSH Terminal"

---

## 🛠️ STEP 2: SETUP SERVER (Run on VPS)

### 2.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

### 2.2 Install PHP 8.2 and Extensions
```bash
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
  php8.2-mbstring php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath \
  php8.2-soap php8.2-readline php8.2-cli -y

# Verify PHP installation
php -v
```

### 2.3 Install MySQL
```bash
sudo apt install mysql-server -y

# Secure MySQL installation
sudo mysql_secure_installation
# Follow prompts:
# - Set root password
# - Remove anonymous users: Yes
# - Disallow root login remotely: Yes
# - Remove test database: Yes
# - Reload privilege tables: Yes
```

### 2.4 Install Node.js 22
```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install nodejs -y

# Verify installation
node -v
npm -v
```

### 2.5 Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

### 2.6 Install and Configure Nginx
```bash
sudo apt install nginx -y

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Verify Nginx is running
sudo systemctl status nginx
```

### 2.7 Configure Firewall
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
sudo ufw status
```

---

## 📦 STEP 3: UPLOAD YOUR PROJECT

### Method 1: Using Git (Recommended)

#### On VPS:
```bash
# Install Git
sudo apt install git -y

# Create web directory
sudo mkdir -p /var/www/bideshgomon
cd /var/www/bideshgomon

# If you have GitHub repository
git clone https://github.com/bideshgomon/bideshgomon-api.git .

# Or create empty directory for manual upload
```

### Method 2: Using SCP (From Windows)

#### On Your Windows Machine (PowerShell):
```powershell
cd C:\xampp\htdocs\bgplatfrom-new\bideshgomon-saas

# Create deployment package (excluding node_modules and vendor)
$exclude = @('node_modules', 'vendor', 'storage/logs/*', '.git')
Compress-Archive -Path * -DestinationPath bideshgomon-deploy.zip -Force

# Upload to VPS
scp bideshgomon-deploy.zip root@148.135.136.95:/tmp/
```

#### On VPS:
```bash
# Create directory and extract
sudo mkdir -p /var/www/bideshgomon
cd /var/www/bideshgomon
sudo unzip /tmp/bideshgomon-deploy.zip -d .
sudo rm /tmp/bideshgomon-deploy.zip
```

### Method 3: Using FileZilla (GUI)
1. Download FileZilla: https://filezilla-project.org/
2. Connect using SFTP:
   - Host: `sftp://148.135.136.95`
   - Username: `root`
   - Password: Your VPS password
   - Port: `22`
3. Navigate to `/var/www/bideshgomon`
4. Upload all files (except node_modules and vendor)

---

## ⚙️ STEP 4: CONFIGURE PROJECT (Run on VPS)

### 4.1 Set Permissions
```bash
cd /var/www/bideshgomon

# Set ownership
sudo chown -R www-data:www-data /var/www/bideshgomon
sudo chown -R $USER:www-data /var/www/bideshgomon

# Set directory permissions
sudo find /var/www/bideshgomon -type d -exec chmod 755 {} \;
sudo find /var/www/bideshgomon -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/bideshgomon/storage
sudo chmod -R 775 /var/www/bideshgomon/bootstrap/cache
```

### 4.2 Install Dependencies
```bash
cd /var/www/bideshgomon

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### 4.3 Configure Environment
```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

**Update these values in .env:**
```env
APP_NAME="Bidesh Gomon"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://148.135.136.95

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon
DB_USERNAME=bideshgomon_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@bideshgomon.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Save file: `Ctrl+X`, then `Y`, then `Enter`

### 4.4 Generate Application Key
```bash
php artisan key:generate
```

---

## 🗄️ STEP 5: SETUP DATABASE

### 5.1 Create Database and User
```bash
sudo mysql

# In MySQL prompt, run:
```

```sql
-- Create database
CREATE DATABASE bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';

-- Grant privileges
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit
EXIT;
```

### 5.2 Run Migrations
```bash
cd /var/www/bideshgomon

# Run migrations
php artisan migrate --force

# Seed demo data
php artisan db:seed --class=DemoUserSeeder

# Clear and cache config
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 STEP 6: CONFIGURE NGINX

### 6.1 Create Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/bideshgomon
```

**Paste this configuration:**
```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name 148.135.136.95;
    root /var/www/bideshgomon/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase upload size limits
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
        
        # Increase timeout for long requests
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

Save file: `Ctrl+X`, then `Y`, then `Enter`

### 6.2 Enable Site and Restart Services
```bash
# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Enable bideshgomon site
sudo ln -s /etc/nginx/sites-available/bideshgomon /etc/nginx/sites-enabled/

# Test Nginx configuration
sudo nginx -t

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# Check status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
```

---

## 🎉 STEP 7: VERIFY DEPLOYMENT

### 7.1 Check Website
Open browser and visit: **http://148.135.136.95**

You should see the Bidesh Gomon login page!

### 7.2 Login with Demo Account
- **Email:** demo@bideshgomon.com
- **Password:** password123

### 7.3 Test All Profile Sections
1. ✅ Basic Information
2. ✅ Profile Details
3. ✅ Education
4. ✅ Work Experience
5. ✅ Skills
6. ✅ Language Proficiency
7. ✅ Family Information
8. ✅ Phone Numbers
9. ✅ Travel History
10. ✅ Financial Information
11. ✅ Security & Background

---

## 🔍 STEP 8: TROUBLESHOOTING

### If website shows 403 Forbidden:
```bash
sudo chown -R www-data:www-data /var/www/bideshgomon
sudo chmod -R 755 /var/www/bideshgomon
sudo chmod -R 775 /var/www/bideshgomon/storage
sudo chmod -R 775 /var/www/bideshgomon/bootstrap/cache
```

### If website shows 500 Error:
```bash
# Check Laravel logs
sudo tail -f /var/www/bideshgomon/storage/logs/laravel.log

# Check Nginx logs
sudo tail -f /var/nginx/error.log

# Clear cache
cd /var/www/bideshgomon
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### If CSS/JS not loading:
```bash
cd /var/www/bideshgomon
npm run build
php artisan storage:link
sudo chmod -R 755 /var/www/bideshgomon/public
```

### If database connection fails:
```bash
# Test database connection
cd /var/www/bideshgomon
php artisan tinker
# In tinker prompt:
DB::connection()->getPdo();
exit
```

### Check PHP-FPM Status:
```bash
sudo systemctl status php8.2-fpm
sudo tail -f /var/log/php8.2-fpm.log
```

### Check Nginx Status:
```bash
sudo systemctl status nginx
sudo tail -f /var/log/nginx/error.log
```

---

## 🔐 STEP 9: SECURITY HARDENING

### 9.1 Setup SSL Certificate (Free with Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate (replace with your domain)
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# If you only have IP, skip SSL for now
# Once you have a domain, run the command above
```

### 9.2 Secure MySQL
```bash
# Login to MySQL
sudo mysql

# Change root password
ALTER USER 'root'@'localhost' IDENTIFIED BY 'VERY_STRONG_PASSWORD';
FLUSH PRIVILEGES;
EXIT;
```

### 9.3 Setup Fail2Ban (Prevent Brute Force)
```bash
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 9.4 Disable Directory Listing
Already configured in Nginx config above ✅

### 9.5 Hide Server Information
```bash
sudo nano /etc/nginx/nginx.conf
```

Add inside `http` block:
```nginx
server_tokens off;
```

Restart Nginx:
```bash
sudo systemctl restart nginx
```

---

## 🔄 STEP 10: SETUP AUTO-DEPLOYMENT (Optional)

### Create deployment script:
```bash
nano /var/www/deploy.sh
```

**Paste this script:**
```bash
#!/bin/bash

echo "🚀 Starting deployment..."

cd /var/www/bideshgomon

# Enable maintenance mode
php artisan down

# Pull latest changes (if using Git)
# git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/bideshgomon
sudo chmod -R 775 /var/www/bideshgomon/storage
sudo chmod -R 775 /var/www/bideshgomon/bootstrap/cache

# Restart services
sudo systemctl restart php8.2-fpm

# Disable maintenance mode
php artisan up

echo "✅ Deployment completed!"
```

Make it executable:
```bash
chmod +x /var/www/deploy.sh
```

Run deployment:
```bash
/var/www/deploy.sh
```

---

## 📊 STEP 11: MONITORING & MAINTENANCE

### Check Disk Space:
```bash
df -h
```

### Check Memory Usage:
```bash
free -h
```

### Check Running Processes:
```bash
htop
# or
top
```

### Monitor Logs in Real-Time:
```bash
# Laravel logs
tail -f /var/www/bideshgomon/storage/logs/laravel.log

# Nginx access logs
tail -f /var/log/nginx/access.log

# Nginx error logs
tail -f /var/log/nginx/error.log
```

### Setup Automatic Backups:
```bash
# Create backup script
nano /var/www/backup.sh
```

**Paste this:**
```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/bideshgomon"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u bideshgomon_user -p'YOUR_PASSWORD' bideshgomon > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/bideshgomon

# Keep only last 7 backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "✅ Backup completed: $DATE"
```

Make executable and setup cron:
```bash
chmod +x /var/www/backup.sh

# Add to crontab (daily at 2 AM)
crontab -e
```

Add this line:
```
0 2 * * * /var/www/backup.sh >> /var/log/backup.log 2>&1
```

---

## 🎯 STEP 12: POST-DEPLOYMENT CHECKLIST

- [ ] Website accessible at http://148.135.136.95
- [ ] Can login with demo account
- [ ] All 11 profile sections working
- [ ] Can create/edit/delete records in each section
- [ ] File uploads working
- [ ] No JavaScript console errors
- [ ] No PHP errors in logs
- [ ] Database migrations completed
- [ ] Assets (CSS/JS) loading correctly
- [ ] Forms submitting successfully
- [ ] Validation messages showing
- [ ] Email notifications working (if configured)
- [ ] SSL certificate installed (if domain available)
- [ ] Backups configured
- [ ] Monitoring setup

---

## 📞 SUPPORT COMMANDS

### Restart Everything:
```bash
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
sudo systemctl restart mysql
```

### Check All Services:
```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

### Clear Everything:
```bash
cd /var/www/bideshgomon
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Re-seed Demo Account:
```bash
cd /var/www/bideshgomon
php artisan db:seed --class=DemoUserSeeder --force
```

---

## 🎊 CONGRATULATIONS!

Your Bidesh Gomon application is now live at:
**http://148.135.136.95**

### Next Steps:
1. **Get a Domain Name** (e.g., bideshgomon.com)
2. **Point Domain to IP:** 148.135.136.95
3. **Install SSL Certificate** (Let's Encrypt - Free)
4. **Update APP_URL** in .env to your domain
5. **Setup Email Service** (Mailgun, SendGrid, etc.)
6. **Configure Backups**
7. **Setup Monitoring** (UptimeRobot, Pingdom)

---

## 🆘 NEED HELP?

If you encounter any issues:

1. **Check Logs:**
   ```bash
   sudo tail -f /var/www/bideshgomon/storage/logs/laravel.log
   ```

2. **Check Nginx Errors:**
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

3. **Test PHP:**
   ```bash
   php artisan tinker
   ```

4. **Verify Database:**
   ```bash
   sudo mysql -u bideshgomon_user -p bideshgomon
   ```

Let me know if you encounter any errors!
