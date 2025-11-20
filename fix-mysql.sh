#!/bin/bash

# ============================================
# Fix MySQL Migration Issue
# ============================================

set -e

echo "======================================"
echo "  Fixing MySQL Setup..."
echo "======================================"

# Stop databases
echo "Stopping database services..."
systemctl stop mysql 2>/dev/null || true
systemctl stop mariadb 2>/dev/null || true

# Remove frozen state
echo "Removing frozen state..."
rm -f /etc/mysql/FROZEN

# Backup and remove old data
echo "Cleaning old database data..."
if [ -d "/var/lib/mysql" ]; then
    mv /var/lib/mysql /var/lib/mysql.backup.$(date +%s) || true
fi

# Create fresh directory
mkdir -p /var/lib/mysql
chown -R mysql:mysql /var/lib/mysql

# Initialize MySQL
echo "Initializing MySQL..."
mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql

# Start MySQL
echo "Starting MySQL..."
systemctl start mysql
systemctl enable mysql

# Wait for MySQL to be ready
echo "Waiting for MySQL to start..."
sleep 5

# Create database and user
echo "Creating database and user..."
mysql << 'EOF'
CREATE DATABASE IF NOT EXISTS bideshgomon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'bideshgomon_user'@'localhost';
CREATE USER 'bideshgomon_user'@'localhost' IDENTIFIED BY 'Nakib##123@@@';
GRANT ALL PRIVILEGES ON bideshgomon.* TO 'bideshgomon_user'@'localhost';
FLUSH PRIVILEGES;
SELECT 'Database created successfully!' as Status;
EOF

echo ""
echo "✓ MySQL fixed successfully!"
echo ""
echo "Now run: cd /var/www/bideshgomon && ./complete-setup.sh"
