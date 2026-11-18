# Base de datos

Esta carpeta separa la base de datos del resto del monorepo para poder desplegarla
de forma independiente del backend y del frontend.

## Archivos

- `gestion_deportiva.sql` — dump completo (estructura + datos) de MySQL 8.4,
  generado desde la instancia local de Laragon.

## Restaurar en un MySQL nuevo

```bash
mysql -u root -p < database/gestion_deportiva.sql
```

El dump incluye `CREATE DATABASE` / `USE`, así que no hace falta crear la BD antes.

Si solo querés el esquema sin datos, podés generarlo con:

```bash
mysqldump -u root --no-data --databases gestion_deportiva > database/gestion_deportiva.sql
```

## Fuente de verdad

Para desarrollo, la fuente de verdad del esquema son las migraciones de Laravel
en `backend/database/migrations` y los datos de prueba en `backend/database/seeders`.

En un despliegue limpio preferí:

```bash
cd backend
php artisan migrate --seed
```

El dump de esta carpeta es útil para mover el estado actual a otro entorno o para
hacer backup.
