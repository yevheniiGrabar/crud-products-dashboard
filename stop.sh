#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

echo "=========================================="
echo "  Stopping CRUD Products Dashboard"
echo "=========================================="
echo ""

# Stop servers using saved PIDs
if [ -f ".server_pids" ]; then
    print_status "Stopping servers using saved PIDs..."
    PIDS=$(cat .server_pids)
    kill $PIDS 2>/dev/null
    rm .server_pids
    print_success "Servers stopped using saved PIDs"
else
    print_status "No saved PIDs found, searching for processes..."
fi

# Kill any remaining processes on our ports
print_status "Checking for processes on ports 8000 and 5173..."

# Kill backend process
BACKEND_PID=$(lsof -ti:8000 2>/dev/null)
if [ ! -z "$BACKEND_PID" ]; then
    print_status "Killing backend process (PID: $BACKEND_PID)"
    kill -9 $BACKEND_PID 2>/dev/null
    print_success "Backend process stopped"
else
    print_status "No backend process found on port 8000"
fi

# Kill frontend process
FRONTEND_PID=$(lsof -ti:5173 2>/dev/null)
if [ ! -z "$FRONTEND_PID" ]; then
    print_status "Killing frontend process (PID: $FRONTEND_PID)"
    kill -9 $FRONTEND_PID 2>/dev/null
    print_success "Frontend process stopped"
else
    print_status "No frontend process found on port 5173"
fi

echo ""
print_success "All servers stopped successfully!"
echo ""
