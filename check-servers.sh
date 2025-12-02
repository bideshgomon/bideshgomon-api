#!/bin/bash

# BideshGomon Server Status Check Script

set -e

PROJECT_DIR="/Users/sbmac/projects/bideshgomon-api"
PHP_PORT=8000
VITE_PORT=5173

echo "📊 BideshGomon Server Status Check"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

cd "$PROJECT_DIR"

# Check Laravel Server
if lsof -ti:$PHP_PORT >/dev/null 2>&1; then
    PHP_PID=$(lsof -ti:$PHP_PORT)
    echo "✅ Laravel Server: RUNNING (PID: $PHP_PID)"
    echo "   URL: http://127.0.0.1:$PHP_PORT"
    
    # Test if actually responding
    if curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:$PHP_PORT >/dev/null 2>&1; then
        echo "   Status: Responding to requests ✓"
    else
        echo "   Status: Port open but not responding ⚠️"
    fi
else
    echo "❌ Laravel Server: NOT RUNNING"
fi

echo ""

# Check Vite Server
if lsof -ti:$VITE_PORT >/dev/null 2>&1; then
    VITE_PID=$(lsof -ti:$VITE_PORT)
    echo "✅ Vite Dev Server: RUNNING (PID: $VITE_PID)"
    echo "   URL: http://localhost:$VITE_PORT"
else
    echo "❌ Vite Dev Server: NOT RUNNING"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check for orphaned processes
ORPHANED_PHP=$(ps aux | grep -E "php.*artisan.*serve" | grep -v grep | wc -l)
ORPHANED_NODE=$(ps aux | grep -E "node.*vite" | grep -v grep | wc -l)

if [ $ORPHANED_PHP -gt 1 ] || [ $ORPHANED_NODE -gt 1 ]; then
    echo "⚠️  Warning: Multiple server processes detected"
    echo "   PHP processes: $ORPHANED_PHP"
    echo "   Node processes: $ORPHANED_NODE"
    echo ""
    echo "   Run './stop-dev.sh' to clean up"
    echo ""
fi

# Show recent logs
echo "📋 Recent Laravel Logs (last 5 lines):"
if [ -f storage/logs/laravel-server.log ]; then
    tail -5 storage/logs/laravel-server.log 2>/dev/null || echo "   No logs available"
else
    echo "   No log file found"
fi

echo ""
echo "📋 Recent Vite Logs (last 5 lines):"
if [ -f storage/logs/vite-server.log ]; then
    tail -5 storage/logs/vite-server.log 2>/dev/null || echo "   No logs available"
else
    echo "   No log file found"
fi

echo ""
