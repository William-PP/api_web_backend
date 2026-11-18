# Documentación de la aplicación — Gestión Deportiva API

> Documento complementario al diseño original (`documentacion-gestion-deportiva - Diseño de aplicaciones web.md`).
> Describe el stack real, lo implementado, lo verificado y lo que queda pendiente.

**Estado:** backend completo y probado.
**Última verificación:** 30/10/2026 — `php artisan test` → **37 tests passed, 125 assertions**.

---

## 1. Información general

Backend API REST para administrar deportistas y modalidades deportivas de un club. No incluye frontend: está pensado para ser consumido por una SPA (React + TypeScript) o por terceros mediante los endpoints públicos.

- Autenticación con tokens (Laravel Sanctum).
- CRUD de modalidades y deportistas.
- Subida de fotos (deportista y perfil).
- Dashboard con totales, datos para gráfico y clima.
- Reporte PDF con DomPDF.
- API pública de solo lectura.
- Arquitectura en 3 capas: **Domain**, **DataAccess** y **Api**.

---

## 2. Stack utilizado

| Tecnología | Versión / uso |
|---|---|
| PHP | 8.4.27 |
| Laravel | 12.70.2 |
| Composer | 2.8.8 |
| MySQL | 8+ (entorno real; base `gestion_deportiva`) |
| SQLite | En memoria, solo para tests (`phpunit.xml`) |
| Laravel Sanctum | Autenticación por tokens Bearer |
| barryvdh/laravel-dompdf | Generación del PDF de deportistas |
| Open-Meteo | API externa de clima (sin API key) |
| PHPUnit | Tests de feature |
| Laragon / Windows | Entorno de desarrollo recomendado por el usuario |

> Node/npm no son necesarios para este backend; pertenecen al frontend.

---

## 3. Arquitectura

```
DataAccess ──> Domain <── Api
```

- **Domain** (`app/Domain`): entidades, DTOs, excepciones, contratos (interfaces) y reglas de negocio. No depende de Laravel.
- **DataAccess** (`app/DataAccess`): modelos Eloquent, mappers, repositorios, adaptadores externos y `DataAccessServiceProvider`.
- **Api** (`app/Api`): controladores, FormRequests, Resources y rutas. Inyecta interfaces de Domain; no conoce implementaciones concretas.

### Estructura resumida

```
app/
├── Api/
│   ├── Controllers/     Auth, Deportista, Modalidad, Dashboard, Perfil, Reporte, PublicDeportista
│   ├── Requests/        DeportistaRequest, ModalidadRequest
│   ├── Resources/       UserResource, ModalidadResource, DeportistaResource
│   └── Support/         FileDataFactory
├── Domain/
│   ├── Entities/        AuditBase, User, Modalidad, Deportista
│   ├── DTOs/            FileData
│   ├── Exceptions/      BusinessException, ConflictException, NotFoundException
│   ├── Interfaces/
│   │   ├── Repositories/     IGenericRepository + 3 concretas
│   │   ├── Services/         8 contratos de servicio
│   │   └── Infrastructure/   token, hash, PDF, clima, storage, reset, usuario actual
│   └── Services/        8 servicios de negocio
└── DataAccess/
    ├── Models/          UserModel, ModalidadModel, DeportistaModel
    ├── Mappers/         3 mappers entidad ↔ modelo
    ├── Repositories/    GenericRepository + 3 concretos
    ├── External/        7 adaptadores (Sanctum, Hash, Storage, DomPDF, Open-Meteo, reset, usuario actual)
    └── Providers/       DataAccessServiceProvider
```

### Inyección de dependencias

`bootstrap/providers.php` registra `DataAccessServiceProvider`, que enlaza:

- Repositorios: `IUserRepository`, `IModalidadRepository`, `IDeportistaRepository`.
- Infraestructura: `ITokenService`, `IPasswordHasher`, `IPdfGenerator`, `IWeatherClient`, `IFileStorage`, `IPasswordResetService`, `ICurrentUserProvider`.
- Servicios: `IAuthService`, `IDeportistaService`, `IModalidadService`, `IEstadisticaService`, `IPerfilService`, `IReporteService`, `IArchivoService`, `IClimaService`.

---

## 4. Funcionalidades implementadas

### 4.1 Autenticación

- Registro con validación y hash de contraseña.
- Login con verificación de credenciales y emisión de token Sanctum.
- Logout con revocación del token actual.
- `GET /me` para obtener el usuario autenticado.
- Recuperación de contraseña:
  - `POST /forgot-password` envía el enlace.
  - `POST /reset-password` cambia la contraseña con token.
  - El enlace apunta a `FRONTEND_URL/reset-password?token=...&email=...` (configurable).

### 4.2 Perfil

- Ver perfil.
- Editar nombre y correo (con validación de correo único).
- Subir/reemplazar foto de perfil; la foto anterior se elimina del almacenamiento.

### 4.3 Modalidades

- Listar, ver, crear, actualizar y eliminar.
- Nombre único (validación de negocio + restricción única en BD).
- No se puede eliminar una modalidad con deportistas asociados (409).
- Listado incluye `deportistas_count`.

### 4.4 Deportistas

- Listar, ver, crear, actualizar y eliminar.
- Asociación obligatoria a una modalidad.
- Asociación al usuario que lo registra.
- Fecha de nacimiento no futura.
- Foto opcional; al actualizar se elimina la anterior si se reemplaza.
- Listado con modalidad y usuario relacionados.

### 4.5 Dashboard

- `GET /dashboard/resumen`: totales de deportistas, modalidades y usuarios + datos para el gráfico (deportistas por modalidad).
- `GET /dashboard/clima?ciudad=...`: consume Open-Meteo (geocodificación + clima actual).

### 4.6 Reportes

- `GET /reportes/deportistas.pdf`: PDF con tabla de deportistas, modalidad y fecha de nacimiento.

### 4.7 API pública

- `GET /public/deportistas`: lista limitada (sin datos del usuario).
- `GET /public/deportistas/{id}`: detalle limitado.
- No requiere autenticación.

### 4.8 Transversal

- Errores JSON consistentes: 404, 409 y 422 según el tipo de excepción de dominio.
- Respuestas JSON forzadas para `api/*`, incluso sin cabecera `Accept`.
- CORS configurado con `FRONTEND_URL`.
- Almacenamiento de fotos en el disco `public`.
- Migraciones, factories y seeder (1 admin, 5 modalidades, 20 deportistas).
- Tests de feature para todos los endpoints.

---

## 5. Endpoints

### Públicos

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/register` | Crear cuenta y devolver token |
| POST | `/api/login` | Iniciar sesión y devolver token |
| POST | `/api/forgot-password` | Enviar enlace de recuperación |
| POST | `/api/reset-password` | Restablecer contraseña |
| GET | `/api/public/deportistas` | Lista pública de deportistas |
| GET | `/api/public/deportistas/{id}` | Detalle público de un deportista |

### Privados (`auth:sanctum`)

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/logout` | Revocar token actual |
| GET | `/api/me` | Usuario autenticado |
| GET | `/api/perfil` | Ver perfil |
| PUT | `/api/perfil` | Editar perfil |
| POST | `/api/perfil/foto` | Subir foto de perfil |
| GET | `/api/deportistas` | Listar deportistas |
| POST | `/api/deportistas` | Crear deportista (multipart si lleva foto) |
| GET | `/api/deportistas/{id}` | Ver deportista |
| PUT | `/api/deportistas/{id}` | Actualizar deportista |
| DELETE | `/api/deportistas/{id}` | Eliminar deportista |
| GET | `/api/modalidades` | Listar modalidades |
| POST | `/api/modalidades` | Crear modalidad |
| GET | `/api/modalidades/{id}` | Ver modalidad |
| PUT | `/api/modalidades/{id}` | Actualizar modalidad |
| DELETE | `/api/modalidades/{id}` | Eliminar modalidad |
| GET | `/api/dashboard/resumen` | Totales y datos del gráfico |
| GET | `/api/dashboard/clima?ciudad=Madrid` | Clima actual |
| GET | `/api/reportes/deportistas.pdf` | Descargar PDF |

> Las respuestas de recursos individuales y colecciones se envuelven en `data` (comportamiento por defecto de Laravel).

---

## 6. Reglas de negocio

1. El correo de usuario es único.
2. El nombre de modalidad es único (sin distinguir mayúsculas/minúsculas en MySQL).
3. No se elimina una modalidad que tenga deportistas.
4. La fecha de nacimiento no puede ser futura.
5. Todo deportista pertenece a una modalidad existente y a un usuario.
6. Las fotos se guardan en `storage/app/public/{deportistas|perfiles}` y se sirven con `storage:link`.
7. Cualquier usuario autenticado puede administrar modalidades y deportistas (aún no hay roles).

---

## 7. Modelo de datos

### `users`
`id`, `name`, `email` (único), `email_verified_at`, `password`, `foto` (nullable), `remember_token`, timestamps.

### `modalidades`
`id`, `nombre` (único, 100), `descripcion` (nullable, 255), timestamps.

### `deportistas`
`id`, `nombre` (100), `apellido` (100), `fecha_nacimiento` (date), `foto` (nullable), `modalidad_id` (FK, restrict), `user_id` (FK, cascade), timestamps.

### Tablas de Laravel
`personal_access_tokens` (Sanctum), `password_reset_tokens`, `cache`, `jobs`, etc.

---

## 8. Variables de entorno y seguridad

### Dónde viven los secretos

Sí: **todas las credenciales y claves viven en `.env`**, nunca en el código.

| Variable | Uso |
|---|---|
| `APP_KEY` | Cifrado de la aplicación (secreto) |
| `DB_PASSWORD` | Contraseña de MySQL |
| `MAIL_PASSWORD` | Contraseña SMTP (si se configura correo real) |
| `REDIS_PASSWORD` | Contraseña de Redis (si se usa) |
| `AWS_SECRET_ACCESS_KEY` | Credenciales AWS (si se usa S3) |
| `FRONTEND_URL` | Origen permitido por CORS y base del enlace de reseteo |
| `SANCTUM_TOKEN_PREFIX` | Prefijo opcional de tokens |

### Verificación realizada

- `.env` está incluido en `.gitignore` (no se sube al repositorio).
- `.env.example` no contiene secretos: `APP_KEY` vacío, contraseñas vacías o `null`.
- No hay credenciales, tokens ni API keys escritas en el código fuente.
- Open-Meteo no requiere API key.
- `config/*` lee los valores con `env(...)`; no hay valores sensibles hardcodeados.
- El frontend recibe solo `foto_url`, nunca rutas internas ni datos del usuario en la API pública.

### Recomendaciones para producción

1. `APP_DEBUG=false` y `APP_ENV=production`.
2. Generar una `APP_KEY` propia en el servidor (`php artisan key:generate`).
3. Usar un usuario MySQL con permisos mínimos (no `root`).
4. Servir siempre por HTTPS.
5. Configurar un mailer real (SMTP) para recuperación de contraseña.
6. Añadir rate limiting a login/registro/recuperación.
7. Rotar/expirar tokens y definir política de refresh.
8. Nunca commitear `.env`; solo `.env.example`.

---

## 9. Instalación y ejecución

```bash
# 1. Dependencias
composer install

# 2. Entorno
cp .env.example .env
php artisan key:generate

# 3. Crear la base de datos en MySQL
#    CREATE DATABASE gestion_deportiva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#    Ajustar DB_DATABASE, DB_USERNAME y DB_PASSWORD en .env

# 4. Migrar y sembrar
php artisan migrate --seed

# 5. Enlazar almacenamiento público
php artisan storage:link

# 6. Levantar el servidor
php artisan serve
```

Credenciales del seeder:

- Email: `admin@club.test`
- Contraseña: `password` (cambiar en cuanto se pruebe)

### Prueba manual rápida

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@club.test","password":"password"}'

curl http://localhost:8000/api/deportistas \
  -H "Authorization: Bearer <TOKEN>"
```

> Para actualizar un deportista con foto usa `POST` + `_method=PUT` (multipart), porque PHP no parsea `multipart/form-data` en `PUT` directamente.

---

## 10. Tests

```bash
php artisan test
```

Resultado actual:

```
Tests: 37 passed (125 assertions)
```

- Los tests usan **SQLite en memoria** (`phpunit.xml`), por lo que **no tocan MySQL** ni ejecutan migraciones en la base real.
- `RefreshDatabase` crea el esquema en memoria para cada test.

Archivos:

| Archivo | Cobertura |
|---|---|
| `tests/Feature/AuthTest.php` | Registro, login, me, logout, forgot/reset password |
| `tests/Feature/ModalidadTest.php` | CRUD, duplicados, borrado con deportistas, 404 |
| `tests/Feature/DeportistaTest.php` | CRUD, foto, fecha futura, modalidad inexistente, 404 |
| `tests/Feature/DashboardTest.php` | Totales, gráfico, clima (HTTP fake), ciudad inválida |
| `tests/Feature/PerfilTest.php` | Ver/editar perfil, correo ocupado, foto |
| `tests/Feature/PublicApiTest.php` | Lista/detalle público y 404 |
| `tests/Feature/ReporteTest.php` | Generación de PDF (`%PDF`, content-type) |
| `tests/Feature/AuthorizationTest.php` | Endpoints privados sin token → 401 |
| `tests/Feature/ExampleTest.php` | Test base de Laravel (se mantiene) |
| `tests/Unit/ExampleTest.php` | Test unitario base de Laravel (se mantiene) |

La carpeta `tests/` **es funcional** y se conserva.

---

## 11. Pendientes / próximos pasos

### Backend

- [ ] Ejecutar migraciones y seeder en MySQL (`php artisan migrate --seed`).
- [ ] Configurar correo real para recuperación de contraseña.
- [ ] Añadir roles/permisos (admin vs usuario) y políticas de autorización.
- [ ] Paginación, búsqueda y filtros en listados.
- [ ] Rate limiting (`throttle`) en auth y endpoints públicos.
- [ ] Expiración/refresh de tokens Sanctum.
- [ ] Versionado de API (`/api/v1`) y documentación OpenAPI/Swagger.
- [ ] Optimización/redimensionado de imágenes y opción de almacenamiento en la nube.
- [ ] Auditoría de cambios (quién creó/editó/eliminó).
- [ ] Tests unitarios adicionales para servicios y casos borde.
- [ ] CI/CD y despliegue (Laragon/Windows o servidor Linux).
- [ ] Respaldos de base de datos y almacenamiento.

### Frontend

- [ ] SPA en React + TypeScript (login, dashboard, tablas, CRUD, perfil, reporte).
- [ ] Cliente HTTP con inyección del token en un solo lugar.
- [ ] Gráfico con Recharts y consumo de `/dashboard/resumen`.
- [ ] Descarga del PDF como blob.

---

## 12. Notas conocidas

- Las migraciones fueron creadas pero **no ejecutadas** en MySQL todavía.
- El SQLite autogenerado por el instalador fue eliminado; el entorno apunta a MySQL.
- `php artisan storage:link` es obligatorio para que `foto_url` funcione.
- El endpoint de clima depende de Open-Meteo; si la ciudad no existe devuelve 422.
- La API pública no expone correo ni datos del usuario que registró al deportista.
- Los tests requieren la extensión GD solo si se usan imágenes fake con `image()`; se optó por contenido PNG embebido para no depender de GD.
