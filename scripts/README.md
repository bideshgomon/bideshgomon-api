# Scripts Directory

This directory contains utility scripts for testing, maintenance, and administrative tasks.

## 🧪 Test Scripts

### Service Testing
- `test-agency-assignments.php` - Test agency assignment functionality
- `test-all-services.php` - Test all service modules
- `test-multiple-services.php` - Test multi-service workflow
- `test-multi-service.php` - Alternative multi-service test
- `test-service-types.php` - Verify service type configurations

### Authentication & Roles
- `test-login.php` - Test authentication system
- `test-role.php` - Test role-based access control
- `test-role-relationship.php` - Verify role relationships
- `debug-role.php` - Debug role assignment issues

## 👤 User Management

- `create-admin-user.php` - Create new admin user
- `reset-admin.php` - Reset admin credentials
- `list-users.php` - List all users in system

## 🔍 Verification & Analysis

- `verify-improvements.php` - Verify implemented improvements
- `check-missing-admin-links.php` - Check for missing admin navigation
- `check-service-types.php` - Validate service configurations

## 🧹 Cleanup & Maintenance

- `cleanup-codebase.php` - Clean up unused code
- `cleanup-execute.php` - Execute cleanup operations
- `cleanup-report.txt` - Cleanup operation report

## 📧 Communication

- `send-test-email.php` - Test email delivery

## 📄 Document Processing

- `reprocess-scans.php` - Reprocess document scans

## 📚 Reference

- `composer-original.txt` - Original composer configuration backup

---

## Usage Guidelines

### Running Scripts

```powershell
# From project root
php scripts/script-name.php
```

### Common Commands

```powershell
# Create admin user
php scripts/create-admin-user.php

# Test all services
php scripts/test-all-services.php

# List users
php scripts/list-users.php

# Test authentication
php scripts/test-login.php
```

### Safety Notes

⚠️ **Important:**
- Always backup database before running cleanup scripts
- Test scripts on development environment first
- Review script output carefully
- Some scripts may modify database data

---

**Last Updated:** November 28, 2025
