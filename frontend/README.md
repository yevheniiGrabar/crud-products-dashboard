# Frontend - CRUD Products Dashboard

This is the Vue 3 frontend application for the CRUD Products Dashboard.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Environment Setup

1. Copy the environment example file:
```sh
cp env.example .env
```

2. Update the `.env` file with your backend API URL:
```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME="CRUD Products Dashboard"
VITE_APP_VERSION="1.0.0"
```

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Compile and Minify for Production

```sh
npm run build
```

## Features

- User authentication (login/register)
- Product management (CRUD operations)
- Dashboard with statistics
- Responsive design with Tailwind CSS
- Vue 3 Composition API
- Pinia state management
