# 🚀 BideshGomon Development Server Management

## Quick Start

### Start Servers
```bash
./start-dev.sh
```
This will:
- ✅ Stop any existing PHP/Node processes
- ✅ Clear all Laravel caches
- ✅ Start Laravel server on `http://127.0.0.1:8000`
- ✅ Start Vite dev server on `http://localhost:5173`
- ✅ Log all output to `storage/logs/`

### Stop Servers
```bash
./stop-dev.sh
```
Cleanly stops both Laravel and Vite servers.

### Check Server Status
```bash
./check-servers.sh
```
Shows:
- ✅ Running status of both servers
- 📊 Process IDs (PIDs)
- 📋 Recent logs
- ⚠️ Warnings about orphaned processes

## Common Issues & Solutions

### Issue: "Port already in use"
**Solution:** Run `./stop-dev.sh` then `./start-dev.sh`

### Issue: "White screen / Page not loading"
**Causes:**
1. **Multiple Vite processes running** (most common)
2. Vite crashed but PHP is still running
3. Port conflicts (5173, 5174, 5175 in use)

**Solution:**
```bash
./stop-dev.sh
sleep 2
./start-dev.sh
```

### Issue: "npm run dev" keeps spawning multiple processes
**Root Cause:** Orphaned Node processes from previous sessions

**Permanent Fix:**
Always use `./start-dev.sh` and `./stop-dev.sh` - they handle process cleanup properly.

### Issue: Server logs show errors but terminal output looks fine
**Check logs:**
```bash
tail -f storage/logs/laravel-server.log
tail -f storage/logs/vite-server.log
```

## Manual Process Management

### Kill all PHP and Node processes (Emergency)
```bash
killall php node 2>/dev/null
```

### Check what's running on port 8000
```bash
lsof -ti:8000
```

### Check what's running on port 5173
```bash
lsof -ti:5173
```

### Kill specific port
```bash
lsof -ti:8000 | xargs kill -9
lsof -ti:5173 | xargs kill -9
```

## Development Workflow

### 1. Start Your Day
```bash
cd /Users/sbmac/projects/bideshgomon-api
./start-dev.sh
```
☕ Wait 5 seconds, then open http://127.0.0.1:8000

### 2. During Development
- Keep servers running
- Vite provides Hot Module Replacement (HMR)
- Changes to Vue files reload automatically
- Changes to Laravel files may need page refresh

### 3. If Something Breaks
```bash
./check-servers.sh  # See what's running
./stop-dev.sh       # Stop everything
./start-dev.sh      # Start fresh
```

### 4. End of Day
```bash
./stop-dev.sh
```

## Script Details

### start-dev.sh
- **Purpose:** Clean startup of both servers
- **Features:**
  - Kills orphaned processes
  - Checks port availability
  - Clears Laravel caches
  - Saves PIDs for later management
  - Logs to separate files
- **Output:** Logs to `storage/logs/laravel-server.log` and `storage/logs/vite-server.log`

### stop-dev.sh
- **Purpose:** Gracefully stop both servers
- **Features:**
  - Uses saved PIDs when available
  - Falls back to `killall` if needed
  - Cleans up PID files

### check-servers.sh
- **Purpose:** Verify server status
- **Features:**
  - Shows running status
  - Tests if ports respond
  - Warns about orphaned processes
  - Shows recent logs

## Why Multiple Vite Processes Happen

**Problem:** Each `npm run dev` spawns:
1. Main Vite process
2. esbuild subprocess
3. Hot reload watcher

If not stopped properly, these accumulate across sessions.

**Solution:** Always use `./stop-dev.sh` before closing terminal or restarting.

## Troubleshooting Checklist

- [ ] Run `./check-servers.sh` to see current state
- [ ] Check `storage/logs/laravel-server.log` for PHP errors
- [ ] Check `storage/logs/vite-server.log` for build errors
- [ ] Verify no orphaned processes: `ps aux | grep -E "php|node|vite" | grep -v grep`
- [ ] Hard reset: `./stop-dev.sh && sleep 2 && ./start-dev.sh`
- [ ] Clear browser cache (Cmd+Shift+R)
- [ ] Check `.env` file exists and is correct

## Pro Tips

### Always Start Clean
```bash
./stop-dev.sh && ./start-dev.sh
```

### Monitor Logs in Real-time
```bash
# Terminal 1
./start-dev.sh

# Terminal 2
tail -f storage/logs/laravel-server.log

# Terminal 3
tail -f storage/logs/vite-server.log
```

### Quick Restart (Faster than full stop/start)
```bash
# Restart PHP only
lsof -ti:8000 | xargs kill -9 && php artisan serve > storage/logs/laravel-server.log 2>&1 &

# Restart Vite only
lsof -ti:5173 | xargs kill -9 && npm run dev > storage/logs/vite-server.log 2>&1 &
```

## After Git Pull / Composer/NPM Updates

```bash
./stop-dev.sh
composer install  # If composer.json changed
npm install       # If package.json changed
php artisan migrate  # If new migrations
./start-dev.sh
```

## File Locations

| File | Purpose |
|------|---------|
| `start-dev.sh` | Start servers script |
| `stop-dev.sh` | Stop servers script |
| `check-servers.sh` | Status check script |
| `storage/logs/laravel-server.log` | Laravel output |
| `storage/logs/vite-server.log` | Vite output |
| `storage/logs/php.pid` | Laravel process ID |
| `storage/logs/vite.pid` | Vite process ID |

## Need Help?

1. **Check server status:** `./check-servers.sh`
2. **View logs:** `cat storage/logs/laravel-server.log`
3. **Nuclear option:** `killall php node && ./start-dev.sh`

---

**Last Updated:** December 2, 2025  
**Laravel:** 12.x | **PHP:** 8.4.14 | **Node:** 22.21.0 | **Vite:** 6.x
