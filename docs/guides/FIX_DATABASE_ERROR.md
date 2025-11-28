# 🔧 FIX DATABASE CONNECTION ERROR

## Error Details
```
SQLSTATE[HY000] [1045] Access denied for user 'bidesh_gomon_user'@'localhost'
```

## Problem
The database username in your `.env` file doesn't match what was created in MySQL.

---

## 🚀 QUICK FIX (Run on VPS)

### Step 1: Connect to VPS
```bash
ssh root@148.135.136.95
```

### Step 2: Fix Database User

#### Option A: Create the Correct User
```bash
# Login to MySQL
sudo mysql

# Run these commands:
```

```sql
-- Drop old user if exists
DROP USER IF EXISTS 'bidesh_gomon_user'@'localhost';

-- Create new user with correct name
CREATE USER 'bidesh_gomon_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';

-- Grant all privileges
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bidesh_gomon_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify user exists
SELECT User, Host FROM mysql.user WHERE User = 'bidesh_gomon_user';

-- Exit
EXIT;
```

#### Option B: Update .env File to Match Existing User
```bash
# Check what users exist
sudo mysql -e "SELECT User, Host FROM mysql.user WHERE User LIKE 'bidesh%';"

# Edit .env file
cd /var/www/bideshgomon
nano .env
```

Update these lines in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon
DB_USERNAME=bidesh_gomon_user
DB_PASSWORD=YourActualPassword
```

---

## 📋 COMPLETE SOLUTION (Recommended)

### 1. Check Current Database Status
```bash
# Login to MySQL
sudo mysql

# Check existing databases
SHOW DATABASES;

# Check existing users
SELECT User, Host FROM mysql.user;

# Exit
EXIT;
```

### 2. Create Fresh Database Setup
```bash
sudo mysql
```

```sql
-- Drop existing database if needed (WARNING: This deletes all data!)
DROP DATABASE IF EXISTS bideshgomon;

-- Create database
CREATE DATABASE bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Drop old users
DROP USER IF EXISTS 'bideshgomon_user'@'localhost';
DROP USER IF EXISTS 'bidesh_gomon_user'@'localhost';

-- Create new user (choose a strong password!)
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY 'Str0ng_P@ssw0rd_2024!';

-- Grant privileges
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'bideshgomon_user'@'localhost';

-- Exit
EXIT;
```

### 3. Update .env File
```bash
cd /var/www/bideshgomon
nano .env
```

**Update these lines exactly:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon
DB_USERNAME=bideshgomon_user
DB_PASSWORD=Str0ng_P@ssw0rd_2024!
```

**Important:** Make sure there are NO spaces around the `=` sign!

Save: `Ctrl+X`, then `Y`, then `Enter`

### 4. Clear Laravel Cache
```bash
cd /var/www/bideshgomon

# Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
```

### 5. Test Database Connection
```bash
# Test connection
php artisan tinker
```

In tinker, type:
```php
DB::connection()->getPdo();
```

If successful, you'll see connection info. Type `exit` to quit tinker.

### 6. Run Migrations
```bash
php artisan migrate:fresh --force
```

### 7. Seed Demo Data
```bash
php artisan db:seed --class=DemoUserSeeder
```

### 8. Restart Services
```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## 🔍 VERIFY FIX

### Test Database Connection
```bash
cd /var/www/bideshgomon

# Method 1: Using artisan
php artisan db:show

# Method 2: Using tinker
php artisan tinker --execute="echo DB::connection()->getDatabaseName();"

# Method 3: Direct MySQL connection
mysql -u bideshgomon_user -p bideshgomon
# Enter password when prompted
# If successful, type: EXIT;
```

### Test Website
1. Open browser: **http://148.135.136.95**
2. You should see the login page without errors
3. Try logging in with demo account:
   - Email: `demo@bideshgomon.com`
   - Password: `password123`

---

## ❌ COMMON MISTAKES TO AVOID

### 1. Spaces in .env File
❌ Wrong:
```env
DB_USERNAME = bideshgomon_user
DB_PASSWORD = password
```

✅ Correct:
```env
DB_USERNAME=bideshgomon_user
DB_PASSWORD=password
```

### 2. Wrong Database Name
Make sure database name matches in:
- MySQL: `bideshgomon`
- .env: `DB_DATABASE=bideshgomon`

### 3. Password with Special Characters
If password has special characters, wrap in quotes:
```env
DB_PASSWORD="P@ssw0rd!#2024"
```

### 4. Wrong Host
✅ Use: `127.0.0.1` or `localhost`
❌ Not: `148.135.136.95`

---

## 🆘 STILL NOT WORKING?

### Check MySQL Service
```bash
sudo systemctl status mysql

# If not running:
sudo systemctl start mysql
sudo systemctl enable mysql
```

### Check MySQL Error Logs
```bash
sudo tail -f /var/log/mysql/error.log
```

### Check Laravel Logs
```bash
tail -f /var/www/bideshgomon/storage/logs/laravel.log
```

### Test MySQL is Running
```bash
sudo netstat -tuln | grep 3306
```

Should show:
```
tcp        0      0 127.0.0.1:3306          0.0.0.0:*               LISTEN
```

### Verify User Can Connect
```bash
mysql -u bideshgomon_user -p -h 127.0.0.1
# Enter password
# If it connects, user is correct
```

### Reset Everything
If nothing works, start fresh:
```bash
# Backup first!
mysqldump -u root -p bideshgomon > /tmp/backup.sql

# Drop and recreate
sudo mysql
DROP DATABASE bideshgomon;
DROP USER 'bideshgomon_user'@'localhost';

CREATE DATABASE bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY 'NewPassword123!';
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Update .env
cd /var/www/bideshgomon
nano .env
# Update DB_PASSWORD=NewPassword123!

# Clear cache and migrate
php artisan config:clear
php artisan migrate:fresh --force
php artisan db:seed --class=DemoUserSeeder
```

---

## ✅ SUCCESS CHECKLIST

After fixing, verify:

- [ ] `php artisan tinker --execute="DB::connection()->getPdo();"` - No error
- [ ] `php artisan db:show` - Shows database info
- [ ] `mysql -u bideshgomon_user -p bideshgomon` - Can connect
- [ ] Website loads: http://148.135.136.95
- [ ] Can login with demo account
- [ ] No errors in: `tail /var/www/bideshgomon/storage/logs/laravel.log`

---

## 📝 RECOMMENDED DATABASE CREDENTIALS

Use these for security:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bideshgomon
DB_USERNAME=bideshgomon_user
DB_PASSWORD=Bg2024_Secure_Pass!
```

**Remember to:**
1. Use a strong password
2. Never commit .env to Git
3. Keep database password secure
4. Use different passwords for production vs development

---

## 🔐 SECURITY NOTE

After fixing, secure your MySQL:

```bash
# Change root password
sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'VeryStrongRootPassword!';
FLUSH PRIVILEGES;
EXIT;

# Disable remote root login (if not already)
sudo mysql
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
FLUSH PRIVILEGES;
EXIT;
```

---

## 🎯 NEXT STEPS

Once fixed:

1. ✅ Test all profile sections work
2. ✅ Test demo login
3. ✅ Verify data saving to database
4. ✅ Setup SSL certificate (Let's Encrypt)
5. ✅ Point domain to server
6. ✅ Setup automated backups

Need help? Let me know which step failed!
