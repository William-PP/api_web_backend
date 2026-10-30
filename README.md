# Gestión Deportiva API

Backend REST para administrar deportistas y modalidades deportivas de un club. Construido con **Laravel 12**, arquitectura en 3 capas (**Domain / DataAccess / Api**), autenticación con **Sanctum**, subida de fotos, dashboard, clima y reporte PDF.

> Documentación detallada de la implementación: [`DOCUMENTACION.md`](DOCUMENTACION.md)

---

## Stack

| Tecnología | Uso |
|---|---|
| PHP >= 8.2 (probado en 8.4.27) | Lenguaje |
| Laravel 12 | Framework |
| MySQL 8 | Base de datos |
| Laravel Sanctum | Tokens Bearer |
| barryvdh/laravel-dompdf | Reporte PDF |
| Open-Meteo | Clima (sin API key) |
| PHPUnit | Tests |

---

## Requisitos

### Windows (Laragon)

1. Instalar **Laragon Full** con PHP 8.2+ y MySQL.
2. Verificar que `php.ini` tenga habilitadas estas extensiones:
   `fileinfo`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `zip`, `gd`.
3. Iniciar **MySQL** y **Apache/Nginx** desde Laragon.
4. Abrir la terminal de Laragon (ya trae PHP y Composer en el PATH).
5. Crear la base de datos con HeidiSQL/phpMyAdmin o:
   ```sql
   CREATE DATABASE gestion_deportiva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
6. Si `php artisan storage:link` falla, abrir Laragon como administrador o activar el **Modo desarrollador** de Windows (la creación de symlinks lo requiere).

### Linux (Debian/Ubuntu)

```bash
sudo apt update
sudo apt install php php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd composer mysql-server
```

Verificar extensiones:

```bash
php -m | grep -E 'fileinfo|pdo_mysql|mbstring|openssl|tokenizer|xml|curl|zip|gd'
```

Crear la base de datos:

```bash
sudo mysql -e "CREATE DATABASE gestion_deportiva CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

## Instalación

```bash
git clone https://github.com/William-PP/api_web.git
cd api_web

composer install

cp .env.example .env
# Windows PowerShell:
# Copy-Item .env.example .env

php artisan key:generate
```

Editar `.env` con los datos de tu entorno:

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_deportiva
DB_USERNAME=root
DB_PASSWORD=
```

Crear el esquema y datos de prueba:

```bash
php artisan migrate --seed
php artisan storage:link
```

Levantar el servidor:

```bash
php artisan serve
```

API disponible en `http://localhost:8000/api`.

### Credenciales del seeder

| Campo | Valor |
|---|---|
| Email | `admin@club.test` |
| Contraseña | `password` |

> Cambia esta contraseña en cuanto pruebes el login.

---

## Variables de entorno importantes

| Variable | Descripción |
|---|---|
| `APP_KEY` | Clave de cifrado (secreta, nunca commitear) |
| `APP_URL` | URL del backend |
| `APP_DEBUG` | `true` en local, `false` en producción |
| `FRONTEND_URL` | Origen permitido por CORS y base del enlace de reseteo |
| `DB_*` | Conexión MySQL |
| `MAIL_*` | Correo para recuperación de contraseña |
| `SANCTUM_TOKEN_PREFIX` | Prefijo opcional de tokens |

**Nunca subas `.env` al repositorio.** Solo `.env.example` debe versionarse.

---

## Endpoints principales

### Públicos

| Método | Ruta |
|---|---|
| POST | `/api/register` |
| POST | `/api/login` |
| POST | `/api/forgot-password` |
| POST | `/api/reset-password` |
| GET | `/api/public/deportistas` |
| GET | `/api/public/deportistas/{id}` |

### Privados (`auth:sanctum`)

| Método | Ruta |
|---|---|
| POST | `/api/logout` |
| GET | `/api/me` |
| GET / PUT | `/api/perfil` |
| POST | `/api/perfil/foto` |
| GET / POST | `/api/deportistas` |
| GET / PUT / DELETE | `/api/deportistas/{id}` |
| GET / POST | `/api/modalidades` |
| GET / PUT / DELETE | `/api/modalidades/{id}` |
| GET | `/api/dashboard/resumen` |
| GET | `/api/dashboard/clima?ciudad=Madrid` |
| GET | `/api/reportes/deportistas.pdf` |

Autenticación por token:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@club.test","password":"password"}'

curl http://localhost:8000/api/deportistas \
  -H "Authorization: Bearer <TOKEN>"
```

> Para actualizar un deportista con foto usa `POST` + `_method=PUT` (multipart), porque PHP no parsea `multipart/form-data` en `PUT` directamente.

---

## Tests

```bash
php artisan test
```

Los tests usan **SQLite en memoria** (`phpunit.xml`), por lo que no modifican tu base MySQL.

Resultado esperado:

```
Tests: 37 passed (125 assertions)
```

---

## Estructura

```
app/
├── Api/           Controladores, requests, resources y rutas
├── Domain/        Entidades, DTOs, excepciones, interfaces y reglas de negocio
└── DataAccess/    Modelos Eloquent, mappers, repositorios y adaptadores externos
```

Regla de dependencias:

```
DataAccess ──> Domain <── Api
```

---

## Comandos útiles

```bash
php artisan route:list              # Ver todas las rutas
php artisan migrate:fresh --seed    # Recrear BD y datos de prueba
php artisan storage:link            # Enlazar storage/app/public con public/storage
php artisan optimize:clear          # Limpiar cachés
php artisan config:cache            # Producción
php artisan route:cache             # Producción
php artisan view:cache              # Producción
```

Si el puerto 8000 está ocupado:

```bash
php artisan serve --port=8001
```

---

## Notas para Windows y Linux

- El repositorio incluye `.gitattributes` para evitar problemas de saltos de línea entre sistemas.
- En Windows, los permisos de `storage/` y `bootstrap/cache/` los gestiona Laragon. Si hay errores de escritura, ejecutar la terminal como administrador.
- En Linux, si hay errores de permisos:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R $USER:www-data storage bootstrap/cache
  ```
- El clima usa Open-Meteo (servicio gratuito, sin API key). Requiere conexión a internet.
- El enlace de recuperación de contraseña apunta a `FRONTEND_URL/reset-password?token=...&email=...`.

---

## Seguridad

- `.env` está en `.gitignore`; no se versiona.
- `.env.example` no contiene secretos.
- No hay credenciales hardcodeadas en el código.
- En producción: `APP_DEBUG=false`, HTTPS, usuario MySQL con permisos mínimos y contraseña de admin cambiada.
- Pendiente recomendado: rate limiting en login/registro/recuperación y expiración de tokens.

---

## Documentación

- [`DOCUMENTACION.md`](DOCUMENTACION.md): stack, arquitectura, funcionalidades, endpoints, reglas de negocio, seguridad, tests y pendientes.
- `documentacion-gestion-deportiva - Diseño de aplicaciones web.md` (fuera del repo): diseño original.
