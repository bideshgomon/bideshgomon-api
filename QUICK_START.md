# 🚀 Quick Commands Reference

## Daily Commands (USE THESE!)

```bash
# Start servers (every morning)
./start-dev.sh

# Stop servers (end of day)
./stop-dev.sh

# Check if servers are running
./check-servers.sh
```

## Emergency Fix (When page won't load)

```bash
./stop-dev.sh && sleep 2 && ./start-dev.sh
```

That's it! These 3 scripts solve 99% of server issues.

---

## URLs
- **Application:** http://127.0.0.1:8000
- **Vite HMR:** http://localhost:5173

## Logs
```bash
tail -f storage/logs/laravel-server.log
tail -f storage/logs/vite-server.log
```

## Nuclear Option (If scripts fail)
```bash
killall php node && sleep 2 && ./start-dev.sh
```
