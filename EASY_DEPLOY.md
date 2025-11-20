# 🚀 ONE-CLICK DEPLOYMENT GUIDE

## Super Simple 3-Step Deployment

### Step 1: Upload Files to VPS

**On Your Windows Machine:**

```powershell
# Navigate to project
cd C:\xampp\htdocs\bgplatfrom-new\bideshgomon-saas

# Create zip (excluding unnecessary files)
$exclude = @('node_modules', 'vendor', 'storage/logs/*', '.git', 'tests')
$files = Get-ChildItem -Exclude $exclude
Compress-Archive -Path $files -DestinationPath bideshgomon.zip -Force

# Upload to VPS
scp bideshgomon.zip root@148.135.136.95:/tmp/
scp vps-setup.sh root@148.135.136.95:/tmp/
```

---

### Step 2: Extract Files on VPS

**SSH to your VPS:**

```bash
ssh root@148.135.136.95
```

**Then run:**

```bash
# Create directory
mkdir -p /var/www/bideshgomon

# Extract files
unzip /tmp/bideshgomon.zip -d /var/www/bideshgomon

# Copy setup script
cp /tmp/vps-setup.sh /var/www/bideshgomon/
chmod +x /var/www/bideshgomon/vps-setup.sh

# Clean up
rm /tmp/bideshgomon.zip
rm /tmp/vps-setup.sh
```

---

### Step 3: Run One-Click Setup

```bash
cd /var/www/bideshgomon
./vps-setup.sh
```

**The script will ask you:**
1. MySQL root password (press Enter if not set)
2. New database password (create a strong one)
3. Your domain (press Enter to use IP)

**Then it automatically:**
- ✅ Installs PHP 8.2
- ✅ Installs MySQL
- ✅ Installs Node.js 22
- ✅ Installs Composer
- ✅ Installs Nginx
- ✅ Creates database
- ✅ Installs dependencies
- ✅ Builds frontend
- ✅ Runs migrations
- ✅ Seeds demo data
- ✅ Configures Nginx
- ✅ Starts everything

**Time:** 5-10 minutes (mostly automatic)

---

## ✅ After Installation

### Visit Your Site
Open browser: **http://148.135.136.95**

### Login with Demo Account
- Email: `demo@bideshgomon.com`
- Password: `password123`

---

## 🔄 For Future Updates

Use the quick deploy script:

```bash
ssh root@148.135.136.95
cd /var/www/bideshgomon
./deploy.sh
```

This will:
- Pull latest changes
- Update dependencies
- Build frontend
- Run migrations
- Clear cache
- Restart services

---

## 🆘 If Something Goes Wrong

### Check Logs
```bash
# Laravel logs
tail -f /var/www/bideshgomon/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### Restart Everything
```bash
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl restart mysql
```

### Clear All Cache
```bash
cd /var/www/bideshgomon
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Re-run Setup
```bash
cd /var/www/bideshgomon
./vps-setup.sh
```

---

## 🔐 Setup SSL (After Domain)

Once you have a domain pointed to your IP:

```bash
# Install Certbot
apt install -y certbot python3-certbot-nginx

# Get SSL certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal is already setup!
```

---

## 📋 What Each Script Does

### vps-setup.sh
- Complete server setup from scratch
- Installs all software
- Configures everything
- Deploys application
- Creates demo account

### deploy.sh
- Quick updates only
- Updates code
- Rebuilds assets
- Runs migrations
- Clears cache

---

## 💡 Pro Tips

### 1. Keep Scripts Updated
After any changes, upload new scripts:
```bash
scp vps-setup.sh root@148.135.136.95:/var/www/bideshgomon/
scp deploy.sh root@148.135.136.95:/var/www/bideshgomon/
```

### 2. Automate with Git
Setup Git hooks for auto-deployment:
```bash
cd /var/www/bideshgomon
git init
git remote add origin https://github.com/bideshgomon/bideshgomon-api.git
```

Then just:
```bash
git pull && ./deploy.sh
```

### 3. Schedule Backups
```bash
crontab -e
```

Add:
```
0 2 * * * /var/www/bideshgomon/backup.sh
```

---

## 🎉 That's It!

You now have:
- ✅ Fully working Laravel application
- ✅ One-click deployment
- ✅ Easy updates
- ✅ Demo account ready
- ✅ Production optimized

**Total time: Less than 15 minutes!**
