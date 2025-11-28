# Scripts Directory

This directory contains utility scripts for testing, maintenance, and administrative tasks.

## 📊 Database Analysis Scripts (NEW)

### analyze-database-relationships.php
**Comprehensive database relationship integrity check**

Checks:
- ✅ Orphaned records across 21+ critical tables
- ✅ Missing indexes on foreign keys
- ✅ Referential integrity
- ✅ Duplicate relationships
- ✅ Cascade delete configuration
- ✅ Model relationship verification

**Usage:**
```bash
php scripts/analyze-database-relationships.php
```

### analyze-advanced-relationships.php
**Deep relationship analysis with advanced checks**

Checks:
- ✅ Missing inverse relationships
- ✅ Circular reference detection
- ✅ Wallet transaction consistency
- ✅ Service quote integrity
- ✅ Referral & reward integrity
- ✅ Profile completeness

**Usage:**
```bash
php scripts/analyze-advanced-relationships.php
```

### run-database-analysis.ps1
**PowerShell runner for comprehensive analysis**

**Usage:**
```powershell
.\scripts\run-database-analysis.ps1
```

### run-database-analysis.bat
**CMD runner for comprehensive analysis**

**Usage:**
```cmd
scripts\run-database-analysis.bat
```

**📊 Reports Generated:**
- `docs/DATABASE_RELATIONSHIP_ANALYSIS.md` - Full detailed report
- `docs/DATABASE_SCAN_EXECUTIVE_SUMMARY.md` - Executive summary

---

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
