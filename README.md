# Products Dashboard

A full-stack CRUD application for product management built with Laravel (backend) and Vue.js (frontend).

## Technologies

### Backend
- **Laravel 11** - PHP framework
- **MySQL** - Database
- **Laravel Sanctum** - API authentication
- **Repository Pattern** - Data access layer
- **Service Layer** - Business logic
- **Laravel Resources** - API response formatting
- **CORS** - Cross-origin resource sharing

### Frontend
- **Vue.js 3** - JavaScript framework
- **Vue Router** - Client-side routing
- **Pinia** - State management
- **Axios** - HTTP client
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Build tool

## Architecture

### Backend Architecture
```
Controllers → Services → Repositories → Resources
     ↓           ↓           ↓           ↓
HTTP Layer → Business Logic → Data Access → Response Formatting
```

- **Controllers**: Handle HTTP requests and responses
- **Services**: Contain business logic and orchestrate operations
- **Repositories**: Handle all database interactions
- **Resources**: Transform data for API responses

### Frontend Architecture
```
Views → Stores → Services → Components
  ↓       ↓        ↓          ↓
Pages → State → API Calls → UI Elements
```

## Features

- ✅ User authentication (register, login, logout)
- ✅ Product CRUD operations
- ✅ Image upload for products
- ✅ Pagination with customizable items per page
- ✅ Product statistics dashboard
- ✅ Responsive design
- ✅ Protected routes
- ✅ Repository pattern implementation
- ✅ Comprehensive test coverage

## Installation

### Prerequisites
- PHP 8.2+
- Node.js 18+
- MySQL 8.0+
- Composer
- npm

### Quick Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd crud-products-dashboard
```

2. **Install dependencies**
```bash
# Install all dependencies (backend + frontend)
npm run install:all

# Or install separately:
# Backend
cd backend && composer install
# Frontend
cd frontend && npm install
```

3. **Environment setup**
```bash
# Backend
cd backend
cp .env.example .env
# Configure database settings in .env

# Frontend
cd frontend
cp .env.example .env
# Configure API URL in .env (default: http://localhost:8000/api)
```

4. **Database setup**
```bash
cd backend
php artisan migrate
php artisan db:seed
```

5. **Storage setup**
```bash
cd backend
php artisan storage:link
```

6. **Start development servers**
```bash
# Start both servers
npm run dev

# Or start separately:
# Backend (from backend directory)
php artisan serve

# Frontend (from frontend directory)
npm run dev
```

### Manual Setup

#### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
# Configure .env with your database settings
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan serve
```

#### Frontend Setup
```bash
cd frontend
npm install
cp .env.example .env
# Configure VITE_API_BASE_URL in .env
npm run dev
```

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/user` - Get current user

### Products
- `GET /api/products` - Get paginated products
- `GET /api/products/{id}` - Get specific product
- `POST /api/products` - Create product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/latest` - Get latest products
- `GET /api/products/stats` - Get product statistics

## Testing

### Run Tests
```bash
# All tests
cd backend && php artisan test

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

### Test Coverage
- **Unit Tests**: 37 tests covering repositories, services, and resources
- **Feature Tests**: 38 tests covering controllers, authentication, and integration
- **Total**: 75 tests with 448 assertions

## Troubleshooting

### Common Issues and Solutions

#### 1. Frontend .env file missing
If you get errors about missing environment variables:
```bash
cd frontend
cp .env.example .env
# Edit .env and set VITE_API_BASE_URL=http://localhost:8000/api
```

#### 2. Database seeder errors (duplicate records)
If you get duplicate key errors during seeding:
```bash
cd backend
php artisan migrate:fresh --seed
```

#### 3. Frontend can't connect to backend (500 error on registration)
This is usually due to missing storage link or CORS issues:

**Check storage link:**
```bash
cd backend
php artisan storage:link
```

**Verify CORS configuration:**
The backend is configured to allow all origins in development. If you're still having issues, check that:
- Backend is running on `http://localhost:8000`
- Frontend is running on a different port (usually `http://localhost:5173`)
- CORS configuration in `backend/config/cors.php` allows your frontend origin

**Check backend logs:**
```bash
cd backend
tail -f storage/logs/laravel.log
```

#### 4. Image upload not working
Ensure storage link is created:
```bash
cd backend
php artisan storage:link
```

#### 5. Authentication issues
Clear browser storage and restart both servers:
```bash
# Backend
cd backend && php artisan config:clear && php artisan cache:clear

# Frontend
cd frontend && npm run dev
```

### Development Tips

1. **Check API responses** using browser dev tools or curl:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

2. **Monitor backend logs** for errors:
```bash
cd backend && tail -f storage/logs/laravel.log
```

3. **Clear caches** if you make configuration changes:
```bash
cd backend && php artisan config:clear && php artisan cache:clear
```

## Project Structure

```
crud-products-dashboard/
├── backend/                 # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/ # API controllers
│   │   │   └── Resources/   # API response transformers
│   │   ├── Models/          # Eloquent models
│   │   ├── Repositories/    # Data access layer
│   │   └── Services/        # Business logic
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   └── seeders/         # Database seeders
│   ├── routes/
│   │   └── api.php          # API routes
│   └── tests/               # Test files
├── frontend/                # Vue.js application
│   ├── src/
│   │   ├── components/      # Vue components
│   │   ├── views/           # Page components
│   │   ├── stores/          # Pinia stores
│   │   ├── services/        # API services
│   │   └── router/          # Vue router
│   └── public/              # Static assets
└── README.md               # This file
```

## Security

- API authentication using Laravel Sanctum
- CORS properly configured
- Input validation on all endpoints
- SQL injection protection via Eloquent ORM
- XSS protection via proper output encoding

## Performance

- Database queries optimized with proper indexing
- API responses cached where appropriate
- Frontend assets optimized with Vite
- Image optimization for product uploads

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
