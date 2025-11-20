# Bidesh Gomon SaaS - CI/CD Deployment Guide

## 🚀 Automated Deployment Setup

This guide will help you set up automated daily deployments to your live server using GitHub Actions.

## Prerequisites

1. **GitHub Repository**: Your code should be in a GitHub repository
2. **Production Server**: Ubuntu/Debian server with:
   - PHP 8.2+
   - MySQL 8.0+
   - Nginx/Apache
   - Composer
   - Node.js 20+
   - SSH access

## 🔧 Server Setup

### 1. Create Deployment Structure

```bash
# On your production server
cd /var/www
mkdir -p bideshgomon-saas/{releases,shared/storage,shared/storage/app,shared/storage/logs,shared/storage/framework}
cd bideshgomon-saas
```

### 2. Setup Environment File

```bash
# Copy your .env file to shared directory
nano shared/.env
```

Configure your production environment variables:
```env
APP_NAME="Bidesh Gomon"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon_saas
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# ... other environment variables
```

### 3. Set Permissions

```bash
chown -R www-data:www-data /var/www/bideshgomon-saas
chmod -R 775 /var/www/bideshgomon-saas/shared/storage
```

### 4. Configure Web Server

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/bideshgomon-saas/current/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔐 GitHub Secrets Configuration

Go to your GitHub repository → Settings → Secrets and variables → Actions → New repository secret

Add the following secrets:

1. **SERVER_HOST**: `your-server-ip-or-domain`
2. **SERVER_USER**: `your-ssh-username`
3. **SERVER_PORT**: `22` (or your SSH port)
4. **SSH_PRIVATE_KEY**: Your private SSH key (generate with `ssh-keygen`)
5. **DEPLOY_PATH**: `/var/www/bideshgomon-saas`
6. **SLACK_WEBHOOK**: (Optional) For deployment notifications

### Generate SSH Key

On your local machine:
```bash
ssh-keygen -t ed25519 -C "github-actions-deploy"
```

Copy the **private key** to GitHub secrets:
```bash
cat ~/.ssh/id_ed25519
```

Copy the **public key** to your server:
```bash
ssh-copy-id -i ~/.ssh/id_ed25519.pub user@your-server
```

## 🎯 Deployment Workflow

The GitHub Actions workflow (`.github/workflows/deploy.yml`) will:

1. **Trigger** on every push to `main` branch
2. **Build** the application:
   - Install PHP dependencies (production mode)
   - Install NPM dependencies
   - Compile assets with Vite
3. **Deploy** to server:
   - Create timestamped release directory
   - Extract deployment package
   - Link shared files (storage, .env)
   - Run Laravel optimization commands
   - Run database migrations
   - Update symlink to new release
   - Reload PHP-FPM
4. **Cleanup** old releases (keeps last 5)
5. **Notify** via Slack (optional)

## 📦 Manual Deployment Commands

If you need to deploy manually:

```bash
# On your local machine
git push origin main

# Or trigger manually from GitHub Actions tab
```

## 🔄 Rollback to Previous Version

If something goes wrong:

```bash
# SSH into your server
ssh user@your-server

# Go to deployment directory
cd /var/www/bideshgomon-saas

# Check available releases
ls -l releases/

# Rollback to previous release
ln -sfn releases/PREVIOUS_RELEASE_TIMESTAMP current

# Reload PHP-FPM
sudo systemctl reload php8.2-fpm
```

## 📊 Monitoring Deployments

1. **GitHub Actions Tab**: View deployment logs and status
2. **Server Logs**: 
   ```bash
   tail -f /var/www/bideshgomon-saas/current/storage/logs/laravel.log
   ```
3. **Nginx Logs**:
   ```bash
   tail -f /var/log/nginx/error.log
   ```

## 🛡️ Security Best Practices

1. **Never commit .env file** to repository
2. **Use environment variables** for sensitive data
3. **Enable SSL/TLS** with Let's Encrypt:
   ```bash
   sudo certbot --nginx -d yourdomain.com
   ```
4. **Regular backups** of database and storage:
   ```bash
   # Add to crontab
   0 2 * * * mysqldump -u user -p'password' bideshgomon_saas > /backups/db_$(date +\%Y\%m\%d).sql
   ```

## 🚨 Troubleshooting

### Deployment Failed
- Check GitHub Actions logs
- Verify SSH connection: `ssh user@your-server`
- Check server disk space: `df -h`
- Verify PHP-FPM status: `sudo systemctl status php8.2-fpm`

### 500 Error After Deployment
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Clear cache: `php artisan cache:clear`
- Check permissions: `chmod -R 775 storage bootstrap/cache`

### Assets Not Loading
- Rebuild assets: `npm run build`
- Clear browser cache
- Check Nginx configuration

## 📞 Support

For deployment issues, check:
- Laravel logs: `/var/www/bideshgomon-saas/current/storage/logs/laravel.log`
- Nginx logs: `/var/log/nginx/error.log`
- PHP-FPM logs: `/var/log/php8.2-fpm.log`

## 🎉 Success!

Once setup is complete, every push to the `main` branch will automatically deploy to your production server!

**Test Deployment:**
```bash
git add .
git commit -m "Test deployment"
git push origin main
```

Watch the deployment in the GitHub Actions tab! 🚀
