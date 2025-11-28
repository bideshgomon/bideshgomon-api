# Performance Monitoring Guide

## Overview
Laravel Telescope provides comprehensive performance monitoring and debugging capabilities for the BideshGomon platform.

## ✅ What's Installed

### Core Components
- **Laravel Telescope v5.15.1** - Performance monitoring dashboard
- **PerformanceMetrics Middleware** - Request tracking with execution time and memory usage
- **PerformanceReport Command** - CLI tool for analyzing performance data

### Key Features
- 🐌 **Slow Query Detection** - Logs queries taking >50ms
- 🐢 **Request Performance Tracking** - Monitors all HTTP requests
- ⚠️ **Exception Monitoring** - Captures and logs all exceptions
- 📊 **Memory Usage Tracking** - Monitors memory consumption per request
- 📈 **Performance Headers** - Adds `X-Execution-Time` and `X-Memory-Usage` to responses

## 📊 Accessing Telescope Dashboard

Visit: **http://localhost/telescope**

### Dashboard Sections
1. **Requests** - All HTTP requests with timing
2. **Queries** - Database queries with execution time
3. **Exceptions** - All exceptions with stack traces
4. **Models** - Eloquent model operations
5. **Cache** - Cache operations
6. **Mail** - Email previews and content
7. **Notifications** - Notification events
8. **Jobs** - Queue job executions
9. **Logs** - Application logs
10. **Views** - Rendered views

## 🚀 Using Performance Monitoring

### 1. View Performance Report

```bash
# Last 24 hours (default)
php artisan performance:report

# Last 1 hour
php artisan performance:report --hours=1

# Show top 20 results
php artisan performance:report --limit=20
```

**Output Example:**
```
Performance Report - Last 24 hours

🐌 Slowest Database Queries:
  245ms - SELECT * FROM users WHERE status = 'active'...
  180ms - SELECT * FROM service_applications WITH...
  120ms - SELECT COUNT(*) FROM bookings WHERE...

🐢 Slowest HTTP Requests:
  445ms - GET /login
  338ms - GET /admin/impersonations
  314ms - GET /admin/agency-assignments

⚠️  Recent Exceptions:
  No exceptions 🎉
```

### 2. Check Response Headers

Every request includes performance headers:

```bash
# Using curl
curl -I http://localhost/admin/dashboard

# Response includes:
X-Execution-Time: 245.67ms
X-Memory-Usage: 12.34MB
```

### 3. Slow Request Warnings

Requests taking >1 second are automatically logged:

```
[warning] Slow request detected
{
  "url": "http://localhost/admin/reports",
  "method": "GET",
  "execution_time": "1245.67ms",
  "memory_usage": "45.23MB",
  "user_id": 5
}
```

## ⚙️ Configuration

### Enable/Disable Telescope

```env
# .env
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope
```

### Slow Query Threshold

Edit `config/telescope.php`:

```php
'slow' => 50, // Log queries slower than 50ms
```

### Environment Restrictions

Telescope is configured to run in **local environment only** by default. In production, it will only log:
- Reportable exceptions
- Failed requests
- Failed jobs
- Scheduled tasks

To change this, edit `app/Providers/TelescopeServiceProvider.php`.

## 🔍 Finding Performance Issues

### 1. Identify Slow Queries

**Via Telescope:**
1. Go to http://localhost/telescope/queries
2. Sort by "Duration" (descending)
3. Look for queries >100ms

**Via Command:**
```bash
php artisan performance:report --hours=24
```

**Optimization Tips:**
- Add database indexes
- Use eager loading (with/load)
- Cache frequently accessed data
- Paginate large result sets

### 2. Track Memory Leaks

**Check Headers:**
```javascript
// In browser console
fetch('/admin/dashboard')
  .then(res => console.log(res.headers.get('X-Memory-Usage')))
```

**Warning Signs:**
- Memory usage >50MB per request
- Increasing memory over time
- OutOfMemoryException errors

**Solutions:**
- Use chunk() for large datasets
- Clear unused variables
- Use lazy collections
- Implement caching

### 3. Monitor Exception Trends

**Via Telescope:**
1. Go to http://localhost/telescope/exceptions
2. Group by exception type
3. Identify recurring issues

**Via Command:**
```bash
php artisan performance:report --hours=168  # Last week
```

## 📈 Performance Best Practices

### Database Optimization

```php
// ❌ BAD: N+1 Query Problem
$users = User::all();
foreach ($users as $user) {
    echo $user->role->name;  // Queries on every iteration
}

// ✅ GOOD: Eager Loading
$users = User::with('role')->get();
foreach ($users as $user) {
    echo $user->role->name;  // Already loaded
}
```

### Query Caching

```php
// Cache frequently accessed data
$services = Cache::remember('active_services', 3600, function () {
    return Service::where('status', 'active')->get();
});
```

### Response Time Targets
- **Homepage:** <200ms
- **Dashboard Pages:** <500ms
- **Complex Reports:** <1000ms
- **API Endpoints:** <300ms

## 🛠️ Troubleshooting

### Telescope Not Loading

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Republish assets
php artisan telescope:publish

# Check migrations
php artisan migrate:status
```

### Database Table Not Found

```bash
# Run Telescope migrations
php artisan migrate

# Check if telescope_entries table exists
php artisan db:table telescope_entries
```

### Too Much Data in Telescope

```bash
# Prune old entries (keeps last 24 hours)
php artisan telescope:prune

# Prune entries older than 48 hours
php artisan telescope:prune --hours=48

# Add to scheduler (app/Console/Kernel.php)
protected function schedule(Schedule $schedule)
{
    $schedule->command('telescope:prune')->daily();
}
```

## 🔒 Security Notes

1. **Production Access:** Telescope is disabled in production by default
2. **Authentication:** Modify `TelescopeServiceProvider::gate()` to restrict access
3. **Sensitive Data:** Configure `hideSensitiveRequestDetails()` to hide passwords/tokens
4. **Public Access:** Never expose /telescope to public internet without authentication

## 📊 Monitoring Checklist

Daily:
- [ ] Check for new exceptions
- [ ] Review slow requests (>1s)
- [ ] Monitor memory usage trends

Weekly:
- [ ] Run `php artisan performance:report --hours=168`
- [ ] Identify query optimization opportunities
- [ ] Review error patterns

Monthly:
- [ ] Analyze long-term performance trends
- [ ] Update slow query threshold if needed
- [ ] Prune old Telescope data

## 🎯 Quick Reference Commands

```bash
# View performance report
php artisan performance:report

# Access Telescope dashboard
http://localhost/telescope

# Prune old data
php artisan telescope:prune --hours=24

# Clear all Telescope data
php artisan telescope:clear

# List all routes
php artisan route:list --path=telescope

# Check Telescope status
php artisan telescope:status
```

## 📞 Support

For issues or questions:
- Review Laravel Telescope docs: https://laravel.com/docs/telescope
- Check application logs: `storage/logs/laravel.log`
- Use `php artisan performance:report` for quick diagnostics

---

**System Status:** ✅ Active and Monitoring
**Version:** Laravel Telescope 5.15.1
**Last Updated:** November 27, 2025
