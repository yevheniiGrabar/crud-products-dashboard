# Setup Instructions

## Prerequisites

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/PostgreSQL

## Backend Setup

### 1. Install Dependencies
```bash
cd backend
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration
Update `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Migration and Seeding
```bash
php artisan migrate:fresh --seed
```

### 5. Storage Link (REQUIRED for file uploads)
```bash
php artisan storage:link
```

**Important:** This command creates a symbolic link from `public/storage` to `storage/app/public`. 
Without this link:
- ✅ File uploads will work (files are saved)
- ❌ Files won't be accessible via web browser (403 Forbidden)
- ❌ Image URLs in API responses won't work

### 6. Start Development Server
**IMPORTANT:** Make sure you're in the `backend` directory when running this command:
```bash
cd backend  # ← You must be in this directory
php artisan serve --host=0.0.0.0 --port=8000
```

The server will be available at: http://localhost:8000

## Frontend Setup

### 1. Install Dependencies
```bash
cd frontend
npm install
```

### 2. Environment Configuration
```bash
cp env.example .env
```

Update `.env` file if needed:
```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="CRUD Products Dashboard"
VITE_APP_VERSION="1.0.0"
```

### 3. Start Development Server
```bash
npm run dev
```

The frontend will be available at: http://localhost:5173

## Troubleshooting

### "public folder does not exist" Error

If you get an error about the public folder not existing:

1. **Make sure you're in the correct directory:**
   ```bash
   cd backend  # ← Must be in backend directory
   pwd  # Should show: /path/to/project/backend
   ```

2. **Check if public folder exists:**
   ```bash
   ls -la public/
   ```

3. **If public folder is missing, it might be a git issue:**
   ```bash
   git checkout HEAD -- public/
   ```

4. **Verify Laravel installation:**
   ```bash
   php artisan --version
   ```

### Common Issues

1. **Permission Issues:**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

2. **Composer Autoload Issues:**
   ```bash
   composer dump-autoload
   ```

3. **Cache Issues:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

## API Testing

### Test Registration
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Test Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

## Project Structure

```
crud-products-dashboard/
├── backend/                 # Laravel API
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/             # ← This folder must exist
│   ├── routes/
│   └── artisan            # ← Run commands from here
└── frontend/              # Vue.js SPA
    ├── src/
    ├── public/
    └── package.json
```

## Development Workflow

1. Start backend server: `cd backend && php artisan serve`
2. Start frontend server: `cd frontend && npm run dev`
3. Open http://localhost:5173 in browser
4. Register/login and test the application
