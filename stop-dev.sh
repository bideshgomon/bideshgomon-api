#!/bin/bash

# BideshGomon Development Server Stop Script

set -e

PROJECT_DIR="/Users/sbmac/projects/bideshgomon-api"

echo "🛑 Stopping BideshGomon Development Servers..."
echo ""

cd "$PROJECT_DIR"

# Stop using PIDs if available
if [ -f storage/logs/php.pid ]; then
    PHP_PID=$(cat storage/logs/php.pid)
    if ps -p $PHP_PID > /dev/null 2>&1; then
        echo "🐘 Stopping Laravel server (PID: $PHP_PID)..."
        kill $PHP_PID 2>/dev/null || true
    fi
    rm -f storage/logs/php.pid
fi

if [ -f storage/logs/vite.pid ]; then
    VITE_PID=$(cat storage/logs/vite.pid)
    if ps -p $VITE_PID > /dev/null 2>&1; then
        echo "⚡ Stopping Vite server (PID: $VITE_PID)..."
        kill $VITE_PID 2>/dev/null || true
    fi
    rm -f storage/logs/vite.pid
fi

# Fallback: kill all php and node processes
echo "🧹 Cleaning up any remaining processes..."
killall php 2>/dev/null || true
killall node 2>/dev/null || true

sleep 1

echo ""
echo "✅ All development servers stopped"
echo ""
