#!/bin/bash

# BideshGomon Development Server Startup Script
# This script ensures clean startup of both Laravel and Vite servers

set -e

PROJECT_DIR="/Users/sbmac/projects/bideshgomon-api"
PHP_PORT=8000
VITE_PORT=5173

echo "🚀 Starting BideshGomon Development Servers..."
echo ""

# Change to project directory
cd "$PROJECT_DIR"

# Function to check if port is in use
check_port() {
    lsof -ti:$1 >/dev/null 2>&1
}

# Function to kill process on port
kill_port() {
    if check_port $1; then
        echo "⚠️  Port $1 is in use, stopping process..."
        lsof -ti:$1 | xargs kill -9 2>/dev/null || true
        sleep 1
    fi
}

# Stop any existing servers AGGRESSIVELY
echo "🧹 Cleaning up existing processes..."
killall -9 php 2>/dev/null || true
killall -9 node 2>/dev/null || true
sleep 2

# Triple check ports are free (kill any remaining processes)
kill_port $PHP_PORT
kill_port $VITE_PORT
# Wait for ports to be fully released
sleep 1
# Final check and kill if still occupied
kill_port $PHP_PORT
kill_port $VITE_PORT

# Clear Laravel caches
echo "🗑️  Clearing Laravel caches..."
php artisan config:clear >/dev/null 2>&1 || true
php artisan route:clear >/dev/null 2>&1 || true
php artisan view:clear >/dev/null 2>&1 || true

# Start PHP server
echo "🐘 Starting Laravel server on port $PHP_PORT..."
php artisan serve --host=127.0.0.1 --port=$PHP_PORT > storage/logs/laravel-server.log 2>&1 &
PHP_PID=$!
sleep 2

# Check if PHP server started
if ! check_port $PHP_PORT; then
    echo "❌ Failed to start Laravel server"
    exit 1
fi
echo "✅ Laravel server running (PID: $PHP_PID) - http://127.0.0.1:$PHP_PORT"

# Start Vite dev server
echo "⚡ Starting Vite dev server on port $VITE_PORT..."
npm run dev > storage/logs/vite-server.log 2>&1 &
VITE_PID=$!
sleep 3

# Check if Vite server started
if ! check_port $VITE_PORT; then
    echo "❌ Failed to start Vite server"
    echo "📋 Check storage/logs/vite-server.log for errors"
    exit 1
fi
echo "✅ Vite dev server running (PID: $VITE_PID) - http://localhost:$VITE_PORT"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🎉 Development servers are ready!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📱 Application: http://127.0.0.1:$PHP_PORT"
echo "⚡ Vite HMR:    http://localhost:$VITE_PORT"
echo ""
echo "🛑 To stop servers, run: ./stop-dev.sh"
echo "📊 Server logs:"
echo "   - Laravel: storage/logs/laravel-server.log"
echo "   - Vite:    storage/logs/vite-server.log"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Save PIDs for stop script
echo "$PHP_PID" > storage/logs/php.pid
echo "$VITE_PID" > storage/logs/vite.pid

# Keep script running
wait
