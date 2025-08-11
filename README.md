# CRUD Products Dashboard

Полноценное CRUD-приложение для управления товарами с аутентификацией пользователей.

## 🚀 Технологии

### Backend
- **Laravel 10** - PHP фреймворк
- **MySQL** - база данных
- **Laravel Sanctum** - аутентификация API
- **Laravel Resource** - API ресурсы
- **Eloquent ORM** - работа с базой данных

### Frontend
- **Vue.js 3** - прогрессивный JavaScript фреймворк
- **Vue Router** - маршрутизация
- **Pinia** - управление состоянием
- **Axios** - HTTP клиент
- **Tailwind CSS** - CSS фреймворк для стилизации
- **Vite** - сборщик модулей

## 📁 Архитектура проекта

```
crud-products-dashboard/
├── backend/                 # Laravel приложение
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/ # Контроллеры (только передача данных)
│   │   │   └── Middleware/  # Middleware для аутентификации
│   │   ├── Models/          # Eloquent модели
│   │   ├── Services/        # Бизнес-логика
│   │   └── Resources/       # API ресурсы
│   ├── database/
│   │   ├── migrations/      # Миграции БД
│   │   └── seeders/         # Сидеры данных
│   ├── routes/
│   │   └── api.php          # API маршруты
│   └── config/
│       └── sanctum.php      # Конфигурация Sanctum
├── frontend/                # Vue.js приложение
│   ├── src/
│   │   ├── components/      # Vue компоненты
│   │   ├── views/           # Страницы приложения
│   │   ├── stores/          # Pinia stores
│   │   ├── services/        # API сервисы
│   │   └── router/          # Конфигурация маршрутов
│   ├── public/              # Статические файлы
│   └── package.json         # Зависимости
└── README.md               # Документация
```

## 🏗️ Архитектурные принципы

### Backend (Laravel)
- **MVC Pattern** - разделение логики, представления и данных
- **Service Layer** - бизнес-логика в сервисах
- **Repository Pattern** - абстракция работы с БД
- **Resource Classes** - форматирование API ответов
- **Middleware** - обработка аутентификации
- **API Resources** - структурированные ответы API

### Frontend (Vue.js)
- **Composition API** - современный подход к компонентам
- **Pinia Stores** - централизованное управление состоянием
- **Service Layer** - абстракция API вызовов
- **Route Guards** - защита маршрутов
- **Responsive Design** - адаптивный дизайн с Tailwind

## 📋 Функциональность

### Аутентификация
- ✅ Регистрация пользователей (имя, email, пароль)
- ✅ Вход в систему (email, пароль)
- ✅ JWT токены для API аутентификации
- ✅ Защищенные маршруты

### Управление товарами
- ✅ CRUD операции для товаров
- ✅ Поля товара: название, изображение, артикул, цена, количество
- ✅ Загрузка изображений
- ✅ Валидация данных

### Dashboard
- ✅ Отображение 3 последних товаров
- ✅ Статистика товаров
- ✅ Быстрый доступ к функциям

### Адаптивность
- ✅ Мобильная версия
- ✅ Планшетная версия
- ✅ Десктопная версия

## 🛠️ Установка и запуск

### Требования
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+
- Git

### Backend установка

```bash
# Клонирование репозитория
git clone <repository-url>
cd crud-products-dashboard/backend

# Установка зависимостей
composer install

# Копирование конфигурации
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate

# Настройка базы данных в .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_products
DB_USERNAME=root
DB_PASSWORD=

# Запуск миграций
php artisan migrate

# Запуск сидеров (опционально)
php artisan db:seed

# Запуск сервера
php artisan serve
```

### Frontend установка

```bash
cd frontend

# Установка зависимостей
npm install

# Запуск в режиме разработки
npm run dev

# Сборка для продакшена
npm run build
```

## 🔧 Конфигурация

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

### Аутентификация
- `POST /api/auth/register` - Регистрация
- `POST /api/auth/login` - Вход
- `POST /api/auth/logout` - Выход
- `GET /api/auth/user` - Получение данных пользователя

### Товары
- `GET /api/products` - Список товаров
- `POST /api/products` - Создание товара
- `GET /api/products/{id}` - Получение товара
- `PUT /api/products/{id}` - Обновление товара
- `DELETE /api/products/{id}` - Удаление товара
- `GET /api/products/latest` - Последние товары

## 🎨 UI/UX Особенности

- **Современный дизайн** с использованием Tailwind CSS
- **Адаптивная верстка** для всех устройств
- **Интуитивный интерфейс** с понятной навигацией
- **Быстрая загрузка** благодаря оптимизации
- **Валидация форм** в реальном времени
- **Уведомления** о результатах операций

## 🔒 Безопасность

- **CSRF защита** для всех форм
- **Валидация данных** на backend и frontend
- **JWT токены** для аутентификации
- **Хеширование паролей** с использованием bcrypt
- **Middleware защита** для API endpoints
- **CORS настройки** для безопасного взаимодействия

## 🧪 Тестирование

```bash
# Backend тесты
php artisan test

# Frontend тесты
npm run test
```

## 📦 Развертывание

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

## 🤝 Вклад в проект

1. Fork репозитория
2. Создайте feature branch (`git checkout -b feature/amazing-feature`)
3. Commit изменения (`git commit -m 'Add amazing feature'`)
4. Push в branch (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

## 📄 Лицензия

Этот проект лицензирован под MIT License - см. файл [LICENSE](LICENSE) для деталей.

## 👨‍💻 Автор

Разработано с использованием лучших практик современной веб-разработки.
