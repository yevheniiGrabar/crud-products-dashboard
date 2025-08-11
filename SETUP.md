# Project Setup Instructions

## Requirements

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+
- Git

## Quick Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd crud-products-dashboard
   ```

2. **Install dependencies and configure the project**
   ```bash
   npm run setup
   ```

3. **Configure the database**
   
   Create a MySQL database named `crud_products` and configure the connection in the `backend/.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crud_products
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Start the project**
   ```bash
   npm run dev
   ```

   This will start:
   - Backend on http://localhost:8000
   - Frontend on http://localhost:3000

## Manual Setup

### Backend (Laravel)

1. **Navigate to the backend directory**
   ```bash
   cd backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Copy configuration file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Configure database in .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crud_products
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Create symbolic link for storage**
   ```bash
   php artisan storage:link
   ```

8. **Start server**
   ```bash
   php artisan serve
   ```

### Frontend (Vue.js)

1. **Navigate to the frontend directory**
   ```bash
   cd frontend
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Create .env file**
   ```bash
   echo "VITE_API_URL=http://localhost:8000/api" > .env
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

## Test Data

After running migrations and seeders, the following will be created in the database:

- Test user:
  - Email: test@example.com
  - Password: password

- 5 test products (iPhone, MacBook, iPad, AirPods, Apple Watch)

## Project Structure

```
crud-products-dashboard/
├── backend/                 # Laravel application
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API controllers
│   │   ├── Models/                # Eloquent models
│   │   ├── Services/              # Business logic
│   │   └── Resources/             # API resources
│   ├── database/
│   │   ├── migrations/            # Database migrations
│   │   └── seeders/               # Data seeders
│   └── routes/api.php             # API routes
├── frontend/                # Vue.js application
│   ├── src/
│   │   ├── components/            # Vue components
│   │   ├── views/                 # Application pages
│   │   ├── stores/                # Pinia stores
│   │   └── router/                # Routing
│   └── package.json
└── README.md
```

## API Endpoints

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
- `GET /api/products/stats` - Statistics

## Functionality

- ✅ User registration and login
- ✅ CRUD operations for products
- ✅ Image upload
- ✅ Dashboard with statistics
- ✅ Responsive design
- ✅ Protected routes
- ✅ Data validation
- ✅ Pagination

## Technologies

### Backend
- Laravel 10
- Laravel Sanctum (authentication)
- MySQL
- Eloquent ORM

### Frontend
- Vue.js 3
- Vue Router
- Pinia (state management)
- Tailwind CSS
- Axios (HTTP client)
