# CRUD Products Dashboard

A full-stack CRUD application for product management built with Laravel 10 and Vue.js 3.

## 🏗️ Architecture

### Backend Architecture (Laravel)

The application follows **SOLID principles** and **Repository Pattern**:

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Controllers   │    │     Services    │    │  Repositories   │    │    Resources    │
│                 │    │                 │    │                 │    │                 │
│ • AuthController│    │ • ProductService│    │ • ProductRepo   │    │ • ProductResource│
│ • ProductCtrl   │───▶│ • Business Logic│───▶│ • DB Operations │───▶│ • ProductCollection│
│ • Data Transfer │    │ • Validation    │    │ • Model Calls   │    │ • StatsResource │
└─────────────────┘    └─────────────────┘    └─────────────────┘    └─────────────────┘
```

**Layers:**
- **Controllers**: Handle HTTP requests/responses, data transfer
- **Services**: Business logic, validation, file processing
- **Repositories**: Database operations, model interactions
- **Resources**: API response formatting and transformation
- **Models**: Eloquent models with relationships and casts

**Key Benefits:**
- ✅ **Separation of Concerns**: Each layer has a specific responsibility
- ✅ **Testability**: Easy to mock repositories for unit testing
- ✅ **Maintainability**: Changes in one layer don't affect others
- ✅ **Scalability**: Easy to add new features or modify existing ones
- ✅ **Consistent API**: Resources ensure uniform response format

### Frontend Architecture (Vue.js 3)

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     Views       │    │     Stores      │    │     Services    │
│                 │    │                 │    │                 │
│ • Dashboard     │    │ • Auth Store    │    │ • API Service   │
│ • Products      │───▶│ • Products Store│───▶│ • HTTP Client   │
│ • Components    │    │ • State Mgmt    │    │ • Interceptors  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

**Layers:**
- **Views**: Page components, user interface
- **Stores (Pinia)**: State management, data caching
- **Services**: API communication, HTTP requests
- **Components**: Reusable UI components

## 🚀 Technologies

### Backend
- **Laravel 10** - PHP framework
- **MySQL** - Database
- **Laravel Sanctum** - API authentication
- **Repository Pattern** - Data access layer
- **Service Layer** - Business logic
- **Eloquent ORM** - Database abstraction

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Client-side routing
- **Pinia** - State management
- **Tailwind CSS** - Utility-first CSS framework
- **Axios** - HTTP client
- **Vite** - Build tool

## 📋 Features

### Authentication
- User registration and login
- JWT token-based authentication
- Protected routes
- Automatic token refresh

### Product Management
- **CRUD Operations**: Create, Read, Update, Delete products
- **Image Upload**: Product images with validation
- **Pagination**: Efficient data loading
- **Search & Filter**: Find products quickly
- **Statistics**: Dashboard with product analytics

### User Interface
- **Responsive Design**: Works on all devices
- **Modern UI**: Clean and intuitive interface
- **Real-time Updates**: Instant feedback
- **Form Validation**: Client and server-side validation

## 🛠️ Installation

### Prerequisites
- PHP 8.1+
- Node.js 16+
- MySQL 8.0+
- Composer
- npm

### Quick Setup
```bash
# Clone repository
git clone <repository-url>
cd crud-products-dashboard

# Install dependencies
npm run install:all

# Setup environment
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Configure database in backend/.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_products
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run setup
npm run setup

# Start development servers
npm run dev
```

### Manual Setup

#### Backend Setup
```bash
cd backend

# Install dependencies
composer install

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create storage link
php artisan storage:link

# Start server
php artisan serve
```

#### Frontend Setup
```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

## 📊 API Endpoints

### Authentication
```
POST   /api/auth/register    - Register new user
POST   /api/auth/login       - User login
POST   /api/auth/logout      - User logout
GET    /api/auth/user        - Get current user
```

### Products (Protected)
```
GET    /api/products         - Get all products (paginated)
GET    /api/products/{id}    - Get product by ID
POST   /api/products         - Create new product
PUT    /api/products/{id}    - Update product
DELETE /api/products/{id}    - Delete product
GET    /api/products/latest  - Get latest 3 products
GET    /api/products/stats   - Get product statistics
```

## 🔧 Configuration

### Environment Variables

#### Backend (.env)
```env
APP_NAME="CRUD Products Dashboard"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_products
DB_USERNAME=root
DB_PASSWORD=

CORS_ALLOWED_ORIGINS=http://localhost:5173
```

#### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api
```

## 🧪 Testing

### Backend Tests
```bash
cd backend
php artisan test
```

### Frontend Tests
```bash
cd frontend
npm run test
```

## 📁 Project Structure

```
crud-products-dashboard/
├── backend/                          # Laravel Backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/     # API Controllers
│   │   │   └── Resources/           # API Resources
│   │   │       ├── ProductResource.php
│   │   │       ├── ProductCollection.php
│   │   │       └── StatsResource.php
│   │   ├── Models/                  # Eloquent Models
│   │   ├── Repositories/            # Repository Pattern
│   │   │   ├── ProductRepository.php
│   │   │   └── ProductRepositoryInterface.php
│   │   ├── Services/                # Business Logic
│   │   │   └── ProductService.php
│   │   └── Providers/               # Service Providers
│   ├── database/
│   │   ├── migrations/              # Database Migrations
│   │   └── seeders/                 # Database Seeders
│   ├── routes/
│   │   └── api.php                  # API Routes
│   └── config/                      # Configuration Files
├── frontend/                        # Vue.js Frontend
│   ├── src/
│   │   ├── components/              # Vue Components
│   │   ├── views/                   # Page Components
│   │   ├── stores/                  # Pinia Stores
│   │   ├── services/                # API Services
│   │   └── router/                  # Vue Router
│   ├── public/                      # Static Assets
│   └── package.json
├── README.md                        # Project Documentation
├── SETUP.md                         # Setup Instructions
├── DEPLOYMENT.md                    # Deployment Guide
└── package.json                     # Root Package Configuration
```

## 🔒 Security Features

- **JWT Authentication**: Secure token-based authentication
- **CORS Protection**: Configured for frontend-backend communication
- **Input Validation**: Server-side validation for all inputs
- **File Upload Security**: Image validation and secure storage
- **SQL Injection Protection**: Eloquent ORM with parameter binding
- **XSS Protection**: Laravel's built-in XSS protection

## 🚀 Deployment

### Production Setup
1. Set environment variables for production
2. Run `npm run build` for frontend
3. Configure web server (Apache/Nginx)
4. Set up database and run migrations
5. Configure SSL certificates

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed instructions.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [SETUP.md](SETUP.md) for troubleshooting
2. Review the API documentation above
3. Check the browser console and Laravel logs
4. Create an issue in the repository

---

**Built with ❤️ using Laravel and Vue.js**
