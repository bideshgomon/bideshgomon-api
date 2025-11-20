# Daily Deployment Strategy (CI/CD Pipeline)

## 🎯 Goal: Deploy to Production Daily with Zero Downtime

> **Reality**: Continuous deployment increases quality, reduces bugs, and keeps features flowing  
> **Strategy**: Automated testing → staging deployment → production deployment  
> **Key Principle**: Every commit to `main` branch should be production-ready

---

## 🏗️ Deployment Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    GitHub Repository                     │
│                    (bideshgomon-api)                    │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ git push
                 ▼
┌─────────────────────────────────────────────────────────┐
│              GitHub Actions (CI/CD)                      │
│  ┌──────────────────────────────────────────────────┐  │
│  │ 1. Run Tests (PHPUnit + Pest)                    │  │
│  │ 2. Run Static Analysis (PHPStan)                 │  │
│  │ 3. Check Code Style (Pint)                       │  │
│  │ 4. Build Assets (Vite)                           │  │
│  │ 5. Run Security Audit                            │  │
│  └──────────────────────────────────────────────────┘  │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ If all tests pass
                 ▼
┌─────────────────────────────────────────────────────────┐
│              Deploy to Staging Server                    │
│              (staging.bideshgomon.com)                   │
│  ┌──────────────────────────────────────────────────┐  │
│  │ - Run migrations                                  │  │
│  │ - Clear cache                                     │  │
│  │ - Run smoke tests                                 │  │
│  └──────────────────────────────────────────────────┘  │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ Manual approval or auto after 10 mins
                 ▼
┌─────────────────────────────────────────────────────────┐
│         Deploy to Production (Zero Downtime)             │
│              (bideshgomon.com)                           │
│  ┌──────────────────────────────────────────────────┐  │
│  │ 1. Put app in maintenance mode (specific routes) │  │
│  │ 2. Pull latest code                              │  │
│  │ 3. Run migrations (with backup)                  │  │
│  │ 4. Clear cache                                    │  │
│  │ 5. Restart queue workers                         │  │
│  │ 6. Warm up cache                                 │  │
│  │ 7. Remove maintenance mode                       │  │
│  │ 8. Health check                                  │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 📦 GitHub Actions Workflow

**File**: `.github/workflows/deploy.yml`

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]
  workflow_dispatch: # Manual trigger

jobs:
  tests:
    name: Run Tests
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: testing
          MYSQL_ROOT_PASSWORD: password
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s
      
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, xml, bcmath, pdo_mysql, redis
          coverage: none
      
      - name: Install Composer dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader
      
      - name: Copy .env
        run: cp .env.ci .env
      
      - name: Generate app key
        run: php artisan key:generate
      
      - name: Run migrations
        run: php artisan migrate --force
      
      - name: Run PHPUnit tests
        run: php artisan test --parallel
      
      - name: Run PHPStan
        run: ./vendor/bin/phpstan analyse --memory-limit=2G
      
      - name: Run Pint (code style)
        run: ./vendor/bin/pint --test
  
  build-assets:
    name: Build Frontend Assets
    runs-on: ubuntu-latest
    needs: tests
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
      
      - name: Install dependencies
        run: npm ci
      
      - name: Build production assets
        run: npm run build
      
      - name: Upload build artifacts
        uses: actions/upload-artifact@v3
        with:
          name: build-assets
          path: public/build
  
  deploy-staging:
    name: Deploy to Staging
    runs-on: ubuntu-latest
    needs: [tests, build-assets]
    environment: staging
    
    steps:
      - name: Download build artifacts
        uses: actions/download-artifact@v3
        with:
          name: build-assets
          path: public/build
      
      - name: Deploy to staging server
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.STAGING_HOST }}
          username: ${{ secrets.STAGING_USER }}
          key: ${{ secrets.STAGING_SSH_KEY }}
          script: |
            cd /var/www/staging.bideshgomon.com
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            php artisan cache:clear
      
      - name: Run smoke tests
        run: |
          curl -f https://staging.bideshgomon.com/health || exit 1
          curl -f https://staging.bideshgomon.com/api/health || exit 1
  
  deploy-production:
    name: Deploy to Production (Zero Downtime)
    runs-on: ubuntu-latest
    needs: deploy-staging
    environment: production
    
    steps:
      - name: Download build artifacts
        uses: actions/download-artifact@v3
        with:
          name: build-assets
          path: public/build
      
      - name: Deploy to production
        uses: appleboy/ssh-action@v1.0.0
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            # Run deployment script
            bash /var/www/bideshgomon.com/deploy.sh
      
      - name: Health check
        run: |
          sleep 5
          curl -f https://bideshgomon.com/health || exit 1
      
      - name: Notify team
        if: always()
        uses: slackapi/slack-github-action@v1.24.0
        with:
          payload: |
            {
              "text": "Production deployment ${{ job.status }}",
              "blocks": [
                {
                  "type": "section",
                  "text": {
                    "type": "mrkdwn",
                    "text": "🚀 *Production Deployment*\n*Status:* ${{ job.status }}\n*Commit:* ${{ github.sha }}\n*Author:* ${{ github.actor }}"
                  }
                }
              ]
            }
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

---

## 🚀 Zero-Downtime Deployment Script

**File**: `deploy.sh` (on production server)

```bash
#!/bin/bash

# Exit on any error
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Starting Zero-Downtime Deployment${NC}"
echo -e "${GREEN}========================================${NC}"

# Configuration
APP_DIR="/var/www/bideshgomon.com"
BACKUP_DIR="/var/backups/bideshgomon"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

cd $APP_DIR

# 1. Create database backup
echo -e "\n${YELLOW}[1/10] Creating database backup...${NC}"
php artisan backup:database --path=$BACKUP_DIR/db_$TIMESTAMP.sql

# 2. Enable maintenance mode (except for admins)
echo -e "\n${YELLOW}[2/10] Enabling maintenance mode...${NC}"
php artisan down --refresh=10 --secret="admin-secret-token" --render="errors::503"

# 3. Pull latest code
echo -e "\n${YELLOW}[3/10] Pulling latest code...${NC}"
git fetch origin main
git reset --hard origin/main

# 4. Install/Update composer dependencies
echo -e "\n${YELLOW}[4/10] Installing dependencies...${NC}"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 5. Run database migrations
echo -e "\n${YELLOW}[5/10] Running migrations...${NC}"
php artisan migrate --force

# 6. Clear all caches
echo -e "\n${YELLOW}[6/10] Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 7. Optimize for production
echo -e "\n${YELLOW}[7/10] Optimizing application...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Restart queue workers
echo -e "\n${YELLOW}[8/10] Restarting queue workers...${NC}"
php artisan queue:restart

# 9. Warm up cache
echo -e "\n${YELLOW}[9/10] Warming up cache...${NC}"
php artisan cache:warmup

# 10. Disable maintenance mode
echo -e "\n${YELLOW}[10/10] Disabling maintenance mode...${NC}"
php artisan up

# Health check
echo -e "\n${YELLOW}Running health check...${NC}"
HEALTH_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://bideshgomon.com/health)

if [ $HEALTH_STATUS -eq 200 ]; then
    echo -e "${GREEN}✅ Deployment successful! Health check passed.${NC}"
else
    echo -e "${RED}❌ Health check failed! Rolling back...${NC}"
    git reset --hard HEAD@{1}
    php artisan migrate:rollback --force
    php artisan up
    exit 1
fi

echo -e "\n${GREEN}========================================${NC}"
echo -e "${GREEN}Deployment completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
```

---

## 🔄 Rollback Strategy

**File**: `rollback.sh`

```bash
#!/bin/bash

set -e

echo "⚠️  Rolling back to previous deployment..."

APP_DIR="/var/www/bideshgomon.com"
cd $APP_DIR

# Enable maintenance mode
php artisan down

# Revert to previous commit
git reset --hard HEAD@{1}

# Rollback migrations
php artisan migrate:rollback --step=1 --force

# Reinstall dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Restart queue workers
php artisan queue:restart

# Disable maintenance mode
php artisan up

echo "✅ Rollback completed!"
```

---

## 📋 Pre-Deployment Checklist

```bash
# Run this locally before pushing to main
php artisan test              # All tests pass
./vendor/bin/phpstan analyse  # No static analysis errors
./vendor/bin/pint --test      # Code style check
npm run build                 # Frontend builds successfully
php artisan migrate:status    # Check migration status
```

---

## 🔐 Environment Variables (Production)

**File**: `.env.production` (on server)

```env
APP_NAME="Bideshgomon"
APP_ENV=production
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_DEBUG=false
APP_URL=https://bideshgomon.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon_prod
DB_USERNAME=bideshgomon_user
DB_PASSWORD=XXXXXXXXXXXXXXXXXXXXX

# Redis (Cache + Queues)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=XXXXXXXXXXXXXXXXXXXXX
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=redis

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=XXXXXXXXXXXXXXXXXXXXX
MAIL_FROM_ADDRESS=noreply@bideshgomon.com

# AWS S3 (File Storage)
AWS_ACCESS_KEY_ID=XXXXXXXXXXXXXXXXXXXXX
AWS_SECRET_ACCESS_KEY=XXXXXXXXXXXXXXXXXXXXX
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=bideshgomon-production
AWS_USE_PATH_STYLE_ENDPOINT=false

# Monitoring
SENTRY_LARAVEL_DSN=https://xxxxx@sentry.io/xxxxx
```

---

## 📊 Deployment Monitoring

### Health Check Endpoint

**File**: `routes/web.php`

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version'),
        'database' => DB::connection()->getDatabaseName(),
        'cache' => Cache::has('health_check_' . now()->format('YmdH')),
    ]);
});
```

### Laravel Pulse (Real-time Monitoring)

```bash
composer require laravel/pulse
php artisan pulse:install
php artisan migrate
```

Access at: `https://bideshgomon.com/pulse`

---

## 🔔 Notifications

### Deployment Success/Failure

```php
// In AppServiceProvider or custom service
Event::listen(DeploymentCompleted::class, function ($event) {
    // Send Slack notification
    Notification::route('slack', config('services.slack.webhook'))
        ->notify(new DeploymentNotification($event));
    
    // Log to monitoring service
    Log::channel('deployments')->info('Deployment completed', [
        'commit' => $event->commit,
        'branch' => $event->branch,
        'duration' => $event->duration,
    ]);
});
```

---

## 🎯 Daily Deployment Schedule

```yaml
# Recommended deployment times for Bangladesh
┌─────────────────────────────────────────────────┐
│ Time (Bangladesh)  │ Why?                       │
├─────────────────────────────────────────────────┤
│ 11:00 AM - 12:00 PM│ Before lunch, low traffic  │
│ 3:00 PM - 4:00 PM  │ After lunch, before peak   │
│ 9:00 PM - 10:00 PM │ Off-peak hours             │
└─────────────────────────────────────────────────┘
```

### Automatic Daily Deployment (Optional)

```yaml
# .github/workflows/daily-deploy.yml
name: Daily Deployment

on:
  schedule:
    - cron: '0 6 * * *' # 12:00 PM Bangladesh time (UTC+6)
  workflow_dispatch:

jobs:
  deploy:
    # Same as deploy.yml
```

---

## 🛡️ Security Best Practices

### 1. Secrets Management

```bash
# Never commit these files
.env
.env.production
deploy.sh (with passwords)

# Use GitHub Secrets for CI/CD
STAGING_HOST
STAGING_USER
STAGING_SSH_KEY
PROD_HOST
PROD_USER
PROD_SSH_KEY
SLACK_WEBHOOK
```

### 2. Database Backups (Before Every Deploy)

```bash
# Automated in deploy.sh
php artisan backup:database

# Retention: Keep last 30 days
find /var/backups/bideshgomon -type f -mtime +30 -delete
```

### 3. SSL Certificate Auto-Renewal

```bash
# Let's Encrypt with Certbot (auto-renew every 90 days)
certbot renew --quiet
```

---

## 📱 Mobile App Updates (If Applicable)

If you build a native app later:

```bash
# Automatic updates via CodePush (React Native)
appcenter codepush release-react -a YourOrg/YourApp-Android -d Production

# Force update check on app launch
if (newVersion > currentVersion) {
  showUpdateDialog()
}
```

---

## ✅ Daily Deployment Checklist

```markdown
### Before Push
- [ ] All tests passing locally
- [ ] Code style check passed
- [ ] Database migrations tested
- [ ] .env.example updated with new variables
- [ ] README updated (if needed)

### After Deployment
- [ ] Health check endpoint returns 200
- [ ] Frontend loads without errors
- [ ] Login/registration works
- [ ] Critical user flows tested
- [ ] Queue workers running
- [ ] Error monitoring dashboard checked
- [ ] Team notified in Slack

### Weekly Checks
- [ ] Review error logs
- [ ] Check database backup integrity
- [ ] Monitor disk space usage
- [ ] Review performance metrics
- [ ] Update dependencies (security patches)
```

---

## 🚨 Emergency Rollback

```bash
# SSH to production server
ssh production@bideshgomon.com

# Run rollback script
cd /var/www/bideshgomon.com
bash rollback.sh

# Or manual rollback
git reset --hard HEAD@{1}
php artisan migrate:rollback --force
php artisan cache:clear
php artisan up
```

---

## 📈 Continuous Improvement

### Week 1-2
- Setup basic CI/CD pipeline
- Manual deployment to staging
- Manual approval for production

### Week 3-4
- Automated staging deployments
- Smoke tests after staging
- Semi-automated production (with approval)

### Month 2+
- Fully automated daily deployments
- Feature flags for gradual rollouts
- A/B testing infrastructure
- Blue-green deployments (zero downtime guaranteed)

---

**Summary**: Push to `main` → GitHub Actions runs tests → Auto-deploy to staging → Manual/Auto deploy to production → Zero downtime → Team notified → Monitor for issues → Rollback if needed!
