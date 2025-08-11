# Инструкции по настройке проекта

## Требования

- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 8.0+
- Git

## Быстрая настройка

1. **Клонируйте репозиторий**
   ```bash
   git clone <repository-url>
   cd crud-products-dashboard
   ```

2. **Установите зависимости и настройте проект**
   ```bash
   npm run setup
   ```

3. **Настройте базу данных**
   
   Создайте базу данных MySQL с именем `crud_products` и настройте подключение в файле `backend/.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crud_products
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Запустите проект**
   ```bash
   npm run dev
   ```

   Это запустит:
   - Backend на http://localhost:8000
   - Frontend на http://localhost:3000

## Ручная настройка

### Backend (Laravel)

1. **Перейдите в директорию backend**
   ```bash
   cd backend
   ```

2. **Установите зависимости**
   ```bash
   composer install
   ```

3. **Скопируйте файл конфигурации**
   ```bash
   cp .env.example .env
   ```

4. **Сгенерируйте ключ приложения**
   ```bash
   php artisan key:generate
   ```

5. **Настройте базу данных в .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crud_products
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Запустите миграции и сидеры**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Создайте символическую ссылку для storage**
   ```bash
   php artisan storage:link
   ```

8. **Запустите сервер**
   ```bash
   php artisan serve
   ```

### Frontend (Vue.js)

1. **Перейдите в директорию frontend**
   ```bash
   cd frontend
   ```

2. **Установите зависимости**
   ```bash
   npm install
   ```

3. **Создайте файл .env**
   ```bash
   echo "VITE_API_URL=http://localhost:8000/api" > .env
   ```

4. **Запустите сервер разработки**
   ```bash
   npm run dev
   ```

## Тестовые данные

После запуска миграций и сидеров в базе данных будут созданы:

- Тестовый пользователь:
  - Email: test@example.com
  - Пароль: password

- 5 тестовых товаров (iPhone, MacBook, iPad, AirPods, Apple Watch)

## Структура проекта

```
crud-products-dashboard/
├── backend/                 # Laravel приложение
│   ├── app/
│   │   ├── Http/Controllers/Api/  # API контроллеры
│   │   ├── Models/                # Eloquent модели
│   │   ├── Services/              # Бизнес-логика
│   │   └── Resources/             # API ресурсы
│   ├── database/
│   │   ├── migrations/            # Миграции БД
│   │   └── seeders/               # Сидеры данных
│   └── routes/api.php             # API маршруты
├── frontend/                # Vue.js приложение
│   ├── src/
│   │   ├── components/            # Vue компоненты
│   │   ├── views/                 # Страницы приложения
│   │   ├── stores/                # Pinia stores
│   │   └── router/                # Маршрутизация
│   └── package.json
└── README.md
```

## API Endpoints

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
- `GET /api/products/stats` - Статистика

## Функциональность

- ✅ Регистрация и вход пользователей
- ✅ CRUD операции для товаров
- ✅ Загрузка изображений
- ✅ Панель управления с статистикой
- ✅ Адаптивный дизайн
- ✅ Защищенные маршруты
- ✅ Валидация данных
- ✅ Пагинация

## Технологии

### Backend
- Laravel 10
- Laravel Sanctum (аутентификация)
- MySQL
- Eloquent ORM

### Frontend
- Vue.js 3
- Vue Router
- Pinia (управление состоянием)
- Tailwind CSS
- Axios (HTTP клиент)
