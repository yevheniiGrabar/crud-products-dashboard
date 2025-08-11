# CRUD Products Dashboard

A full-featured CRUD application for product management with user authentication.

## 🚀 Technologies

### Backend
- **Laravel 10** - PHP framework
- **MySQL** - database
- **Laravel Sanctum** - API authentication
- **Laravel Resource** - API resources
- **Eloquent ORM** - database operations

### Frontend
- **Vue.js 3** - progressive JavaScript framework
- **Vue Router** - routing
- **Pinia** - state management
- **Axios** - HTTP client
- **Tailwind CSS** - CSS framework for styling
- **Vite** - module bundler

## 📁 Project Architecture

```
crud-products-dashboard/
├── backend/                 # Laravel application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/ # Controllers (data transfer only)
│   │   │   └── Middleware/  # Authentication middleware
│   │   ├── Models/          # Eloquent models
│   │   ├── Services/        # Business logic
│   │   └── Resources/       # API resources
│   ├── database/
│   │   ├── migrations/      # Database migrations
│   │   └── seeders/         # Data seeders
│   ├── routes/
│   │   └── api.php          # API routes
│   └── config/
│       └── sanctum.php      # Sanctum configuration
├── frontend/                # Vue.js application
│   ├── src/
│   │   ├── components/      # Vue components
│   │   ├── views/           # Application pages
│   │   ├── stores/          # Pinia stores
│   │   ├── services/        # API services
│   │   └── router/          # Route configuration
│   ├── public/              # Static files
│   └── package.json         # Dependencies
└── README.md               # Documentation
```

## 🏗️ Architectural Principles

### Backend (Laravel)
- **MVC Pattern** - separation of logic, presentation and data
- **Service Layer** - business logic in services
- **Repository Pattern** - database abstraction
- **Resource Classes** - API response formatting
- **Middleware** - authentication processing
- **API Resources** - structured API responses

### Frontend (Vue.js)
- **Composition API** - modern component approach
- **Pinia Stores** - centralized state management
- **Service Layer** - API call abstraction
- **Route Guards** - route protection
- **Responsive Design** - adaptive design with Tailwind

## 📋 Functionality

### Authentication
- ✅ User registration (name, email, password)
- ✅ System login (email, password)
- ✅ JWT tokens for API authentication
- ✅ Protected routes

### Product Management
- ✅ CRUD operations for products
- ✅ Product fields: name, image, SKU, price, quantity
- ✅ Image upload
- ✅ Data validation

### Dashboard
- ✅ Display of 3 latest products
- ✅ Product statistics
- ✅ Quick access to functions

### Adaptability
- ✅ Mobile version
- ✅ Tablet version
- ✅ Desktop version

## 🛠️ Installation and Setup

### Requirements
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+
- Git

### Backend Installation

```bash
# Clone repository
git clone <repository-url>
cd crud-products-dashboard/backend

# Install dependencies
composer install

# Copy configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_products
DB_USERNAME=root
DB_PASSWORD=

# Run migrations
php artisan migrate

# Run seeders (optional)
php artisan db:seed

# Start server
php artisan serve
```

### Frontend Installation

```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build
```

## 🔧 Configuration

### Backend (.env)
```env
APP_NAME="CRUD Products Dashboard"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_products
DB_USERNAME=root
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME="CRUD Products Dashboard"
```

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - Registration
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `GET /api/auth/user` - Get user data

### Products
- `GET /api/products` - Product list
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/latest` - Latest products

## 🎨 UI/UX Features

- **Modern design** using Tailwind CSS
- **Responsive layout** for all devices
- **Intuitive interface** with clear navigation
- **Fast loading** thanks to optimization
- **Form validation** in real time
- **Notifications** about operation results

## 🔒 Security

- **CSRF protection** for all forms
- **Data validation** on backend and frontend
- **JWT tokens** for authentication
- **Password hashing** using bcrypt
- **Middleware protection** for API endpoints
- **CORS settings** for secure interaction

## 🧪 Testing

```bash
# Backend tests
php artisan test

# Frontend tests
npm run test
```

## 📦 Deployment

### Backend (Production)
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend (Production)
```bash
npm run build
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

Developed using best practices of modern web development.
