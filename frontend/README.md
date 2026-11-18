# Gestión Deportiva - Frontend

Frontend de la aplicación de gestión deportiva del club. Consume la API Laravel del repositorio [`api_web`](https://github.com/William-PP/api_web.git).

## Stack

- React 19 + TypeScript
- Vite
- Tailwind CSS v4
- React Router
- Axios
- TanStack Query
- TanStack Table
- React Hook Form
- Zod
- Lucide React
- Recharts

## Requisitos

- Node.js 20 o superior
- npm 9 o superior
- Backend Laravel en ejecución

## Instalación

```bash
git clone https://github.com/William-PP/api_web_frontend.git
cd api_web_frontend
npm install
```

Crea el archivo `.env` a partir de `.env.example`:

```bash
cp .env.example .env
```

En Windows puedes crear el archivo manualmente o usar:

```bat
copy .env.example .env
```

## Variables de entorno

```env
VITE_API_URL=http://localhost:8000/api
```

- `VITE_API_URL`: URL base de la API Laravel.
- El archivo `.env` está ignorado por Git.
- `.env.example` sirve como plantilla.

## Scripts

```bash
npm run dev      # Servidor de desarrollo en http://localhost:5173
npm run build    # Verificación de TypeScript y build de producción
npm run preview  # Vista previa del build
npm run lint     # Análisis con ESLint
```

## Estructura

```text
src/
├── app/                 # Router, providers y layouts globales
│   └── layouts/         # PublicLayout, AppLayout, ProtectedRoute
├── shared/              # Código reutilizable
│   ├── api/             # Cliente Axios y manejo de errores
│   ├── components/      # Button, Input, etc.
│   └── lib/             # Utilidades de formato y fechas
└── features/            # Módulos del negocio
    ├── landing/
    ├── auth/
    ├── dashboard/
    ├── deportistas/
    ├── modalidades/
    ├── perfil/
    └── reportes/
```

Cada feature expone su API pública mediante un archivo `index.ts`.
La descarga del PDF de reportes está en `features/deportistas/api/reportes.api.ts`.

## Estado actual

### Completado

- [x] Proyecto creado con Vite, React y TypeScript.
- [x] Tailwind CSS configurado y dependencias instaladas.
- [x] Estructura de carpetas por features y router con rutas públicas/privadas.
- [x] Layouts `PublicLayout`, `AppLayout` y `ProtectedRoute`.
- [x] Cliente Axios con interceptor del token Bearer y helper de errores.
- [x] Auth: login, registro, logout, validación con `/me`, recuperación y reset.
- [x] `AuthContext` con usuario y token persistidos en `localStorage`.
- [x] Componentes compartidos `Button`, `Input`, `Select`, `Card`, `Modal` y `StateMessage`.
- [x] Landing con listado público de deportistas (`/public/deportistas`).
- [x] Dashboard con resumen, gráfico por modalidad y clima (Open-Meteo vía API).
- [x] Modalidades: listado con búsqueda y CRUD completo.
- [x] Deportistas: listado con búsqueda, CRUD con foto y exportación a PDF.
- [x] Perfil: edición de datos y subida de foto.
- [x] Validaciones con Zod en todos los formularios.
- [x] `npm run lint` y `npm run build` sin errores.
- [x] Probado contra el backend Laravel real (API en `localhost:8000`).

### Pendiente / mejoras futuras

- [ ] Página 404 y manejo global de errores (ErrorBoundary).
- [ ] Confirmación de borrado con un diálogo propio en vez de `window.confirm`.
- [ ] Paginar las tablas de deportistas y modalidades.
- [ ] Dividir el bundle (Recharts pesa ~500 kB) con `React.lazy`.
- [ ] Pruebas de interfaz (Vitest + Testing Library).
- [ ] Mostrar el código de clima con iconos descriptivos.

## Dónde vamos

Todas las features principales ya están conectadas de punta a punta con la API. Los próximos pasos son de pulido: paginación, manejo global de errores, dividir el bundle y agregar pruebas de UI.

## Backend

Repositorio: [`https://github.com/William-PP/api_web.git`](https://github.com/William-PP/api_web.git)

En Windows con Laragon:

1. Crear la base de datos `gestion_deportiva`.
2. Configurar `.env` del backend.
3. Ejecutar `php artisan migrate --seed`.
4. Ejecutar `php artisan storage:link`.
5. Ejecutar `php artisan serve`.

El backend queda disponible en `http://localhost:8000` y la API en `http://localhost:8000/api`.

Usuario administrador del seeder:

```text
admin@club.test
password
```

Cambiar esa contraseña en un entorno real.

## Notas

- La rama de desarrollo es `develop`.
- No se debe subir el archivo `.env`.
- El token se guarda temporalmente en `localStorage`.
- La carpeta `dist/` y `node_modules/` están ignoradas por Git.
