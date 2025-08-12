#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check if port is available
check_port() {
    local port=$1
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null 2>&1; then
        return 1
    else
        return 0
    fi
}

# Function to wait for port to be available
wait_for_port() {
    local port=$1
    local max_attempts=30
    local attempt=1
    
    print_status "Waiting for port $port to be available..."
    
    while [ $attempt -le $max_attempts ]; do
        if check_port $port; then
            print_success "Port $port is available"
            return 0
        fi
        sleep 1
        attempt=$((attempt + 1))
    done
    
    print_error "Port $port is still in use after $max_attempts seconds"
    return 1
}

# Function to kill process on port
kill_port() {
    local port=$1
    local pid=$(lsof -ti:$port 2>/dev/null)
    if [ ! -z "$pid" ]; then
        print_warning "Killing process on port $port (PID: $pid)"
        kill -9 $pid 2>/dev/null
        sleep 2
    fi
}

# Main setup function
main() {
    echo "=========================================="
    echo "  CRUD Products Dashboard Setup Script"
    echo "=========================================="
    echo ""
    
    # Check prerequisites
    print_status "Checking prerequisites..."
    
    if ! command_exists php; then
        print_error "PHP is not installed. Please install PHP 8.1+"
        exit 1
    fi
    
    if ! command_exists composer; then
        print_error "Composer is not installed. Please install Composer"
        exit 1
    fi
    
    if ! command_exists node; then
        print_error "Node.js is not installed. Please install Node.js 16+"
        exit 1
    fi
    
    if ! command_exists npm; then
        print_error "npm is not installed. Please install npm"
        exit 1
    fi
    
    print_success "All prerequisites are installed"
    echo ""
    
    # Backend setup
    print_status "Setting up backend..."
    
    if [ ! -d "backend" ]; then
        print_error "Backend directory not found. Please run this script from the project root"
        exit 1
    fi
    
    cd backend
    
    # Install PHP dependencies
    print_status "Installing PHP dependencies..."
    if composer install --no-interaction; then
        print_success "PHP dependencies installed"
    else
        print_error "Failed to install PHP dependencies"
        exit 1
    fi
    
    # Copy environment file
    if [ ! -f ".env" ]; then
        print_status "Creating .env file..."
        cp .env.example .env
        print_success ".env file created"
    else
        print_warning ".env file already exists"
    fi
    
    # Generate application key
    print_status "Generating application key..."
    if php artisan key:generate --no-interaction; then
        print_success "Application key generated"
    else
        print_error "Failed to generate application key"
        exit 1
    fi
    
    # Create storage link
    print_status "Creating storage link..."
    if php artisan storage:link --no-interaction; then
        print_success "Storage link created"
    else
        print_warning "Storage link already exists or failed to create"
    fi
    
    # Run migrations and seeders
    print_status "Running database migrations and seeders..."
    if php artisan migrate:fresh --seed --no-interaction; then
        print_success "Database setup completed"
    else
        print_error "Failed to setup database"
        print_warning "Make sure your database is configured in .env file"
        exit 1
    fi
    
    cd ..
    echo ""
    
    # Frontend setup
    print_status "Setting up frontend..."
    
    if [ ! -d "frontend" ]; then
        print_error "Frontend directory not found. Please run this script from the project root"
        exit 1
    fi
    
    cd frontend
    
    # Install Node.js dependencies
    print_status "Installing Node.js dependencies..."
    if npm install; then
        print_success "Node.js dependencies installed"
    else
        print_error "Failed to install Node.js dependencies"
        exit 1
    fi
    
    # Copy environment file
    if [ ! -f ".env" ]; then
        print_status "Creating frontend .env file..."
        if [ -f "env.example" ]; then
            cp env.example .env
            print_success "Frontend .env file created"
        else
            print_warning "env.example not found, creating basic .env"
            echo "VITE_API_BASE_URL=http://localhost:8000/api" > .env
            echo "VITE_APP_NAME=\"CRUD Products Dashboard\"" >> .env
            echo "VITE_APP_VERSION=\"1.0.0\"" >> .env
            print_success "Basic frontend .env file created"
        fi
    else
        print_warning "Frontend .env file already exists"
    fi
    
    cd ..
    echo ""
    
    # Start servers
    print_status "Starting servers..."
    
    # Kill existing processes on ports 8000 and 5173
    kill_port 8000
    kill_port 5173
    
    # Wait for ports to be available
    wait_for_port 8000
    wait_for_port 5173
    
    # Start backend server
    print_status "Starting backend server on port 8000..."
    cd backend
    php artisan serve --host=0.0.0.0 --port=8000 > ../backend.log 2>&1 &
    BACKEND_PID=$!
    cd ..
    
    # Wait for backend to start
    sleep 3
    
    # Check if backend is running
    if curl -s http://localhost:8000 > /dev/null 2>&1; then
        print_success "Backend server started successfully"
    else
        print_error "Failed to start backend server"
        print_status "Check backend.log for details"
        exit 1
    fi
    
    # Start frontend server
    print_status "Starting frontend server on port 5173..."
    cd frontend
    npm run dev > ../frontend.log 2>&1 &
    FRONTEND_PID=$!
    cd ..
    
    # Wait for frontend to start
    sleep 5
    
    # Check if frontend is running
    if curl -s http://localhost:5173 > /dev/null 2>&1; then
        print_success "Frontend server started successfully"
    else
        print_warning "Frontend server might still be starting..."
        print_status "Check frontend.log for details"
    fi
    
    echo ""
    echo "=========================================="
    print_success "Setup completed successfully!"
    echo "=========================================="
    echo ""
    echo "Backend:  http://localhost:8000"
    echo "Frontend: http://localhost:5173"
    echo "API:      http://localhost:8000/api"
    echo ""
    echo "Log files:"
    echo "  Backend:  ./backend.log"
    echo "  Frontend: ./frontend.log"
    echo ""
    echo "To stop servers, run:"
    echo "  kill $BACKEND_PID $FRONTEND_PID"
    echo ""
    echo "Or use Ctrl+C to stop this script"
    echo ""
    
    # Save PIDs to file for easy cleanup
    echo "$BACKEND_PID $FRONTEND_PID" > .server_pids
    
    # Wait for user to stop
    trap 'cleanup' INT TERM
    wait
}

# Cleanup function
cleanup() {
    echo ""
    print_status "Stopping servers..."
    
    if [ -f ".server_pids" ]; then
        PIDS=$(cat .server_pids)
        kill $PIDS 2>/dev/null
        rm .server_pids
    fi
    
    # Kill any remaining processes on our ports
    kill_port 8000
    kill_port 5173
    
    print_success "Servers stopped"
    exit 0
}

# Run main function
main "$@"
