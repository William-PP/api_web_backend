# Gestión Deportiva — Monorepo

Monorepo que contiene el backend (API Laravel), el frontend (React) y la base de datos
de la aplicación de gestión deportiva del club.

## Estructura

```
.
├── backend/     API REST — Laravel 12 (Sanctum, DomPDF, Open-Meteo)
├── frontend/    SPA — React 19 + TypeScript + Vite + Tailwind
└── database/    Dump SQL y notas de la base de datos
```

Cada carpeta puede desplegarse por separado:

| Componente | Tecnología | Despliegue |
|---|---|---|
| `backend/` | PHP 8.4 / Laravel 12 | servidor PHP-FPM o `php artisan serve` |
| `frontend/` | Node 22 / Vite | sitio estático (`npm run build` → `dist/`) |
| `database/` | MySQL 8.4 | instancia MySQL independiente |

## Desarrollo local

### 1. Backend

```bash
cd backend
composer install
cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve             # http://localhost:8000
```

Requiere PHP **8.4.1+** (las dependencias Symfony 8.x no funcionan con 8.3) y MySQL 8.
El PHP de Laragon que trae por defecto suele ser 8.3; agregá PHP 8.4 con
**Tools → Quick add → PHP 8.4** o usá un PHP 8.4 del sistema.

API: `http://localhost:8000/api`
Colección de Postman: `backend/postman/GestionDeportiva.postman_collection.json`

### 2. Frontend

```bash
cd frontend
npm install
npm run dev                   # http://localhost:5173
```

El archivo `frontend/.env` apunta a `VITE_API_URL=http://localhost:8000/api`.

### 3. Base de datos

Ver `database/README.md`. El esquema fuente son las migraciones en
`backend/database/migrations`.

## Comandos útiles

```bash
# Backend
cd backend && php artisan test          # 37 tests (SQLite en memoria)
cd backend && php artisan route:list    # todos los endpoints

# Frontend
cd frontend && npm run build            # type-check + build
cd frontend && npm run lint
```

## Notas

- El frontend fue clonado desde `William-PP/api_web_frontend` y su `.git` fue
  eliminado: todo el código vive ahora en este único repositorio.
- No subir `.env` ni `node_modules/` ni `vendor/`.
- Usuario de prueba del seeder: `admin@club.test` / `password` (cambiar en producción).
