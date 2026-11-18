# Despliegue desde la PC con Cloudflare Tunnel

Todo el código sigue corriendo en tu máquina (MySQL de Laragon incluido). Cloudflare
solo expone un puerto a internet. **Sin cuenta, sin tarjeta y sin pagar.**

## 1. Instalar cloudflared (una sola vez)

```powershell
winget install --id Cloudflare.cloudflared
```

Cerrá y abrí una terminal nueva para que se actualice el PATH.

## 2. Levantar los 3 servicios (3 terminales)

Abrí **tres terminales de PowerShell** y ejecutá un comando en cada una.

### Terminal 1 — Backend (PHP 8.4)

```powershell
cd D:\Documents\Proyectos\apiweb\backend\public
& "C:\Users\William\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" -S 127.0.0.1:8000 -t . router.php
```

> ⚠️ **Tres detalles críticos** (si no, el front falla):
> 1. Tenés que estar en **`backend\public`**, no en `backend`. El `index.php`
>    está en `public/`. Si estás en `backend/`, el servidor devuelve HTML de error
>    en vez de JSON (y el front muestra `data is undefined`).
> 2. Usá **`router.php`**, no `index.php`. El router sirve las fotos de `/storage`
>    como imágenes; con `index.php` Laravel devuelve HTML y las fotos no se ven.
> 3. Usá **PHP 8.4** con la ruta completa de arriba. El `php` del PATH es el 8.3
>    de Laragon y no sirve para estas dependencias. Para comprobarlo: `php -v`.
>
> Alternativa: desde `backend\` podés correr
> `php artisan serve --host=127.0.0.1 --port=8000`
> (si da `Failed to listen`, quedate con el `php -S` de arriba).

### Terminal 2 — Frontend

```powershell
cd D:\Documents\Proyectos\apiweb\frontend
npm run build
npm run preview -- --host 127.0.0.1 --port 4173
```

> La primera vez hay que correr `npm run build`. En las siguientes, alcanza con
> `npm run preview`. Para desarrollo diario podés usar `npm run dev` en vez de
> `build` + `preview`: el proxy funciona igual.

### Terminal 3 — Túnel

```powershell
cloudflared tunnel --url http://localhost:4173
```

## 3. ¿Dónde veo la URL pública?

**En la Terminal 3**, la de `cloudflared`. Al arrancar imprime un bloque así:

```
+--------------------------------------------------------------------------------------------+
|  Your quick Tunnel has been created! Visit it at (it may take some time to be reachable):  |
|  https://abc-xyz-123.trycloudflare.com                                                      |
+--------------------------------------------------------------------------------------------+
```

Ese `https://....trycloudflare.com` es el link para compartir. Copialo y pegalo en
el navegador. Las líneas que siguen son solo logs de conexión; no las necesitás.

> ⚠️ La URL **cambia cada vez que reiniciás cloudflared**. Anotá la nueva cuando
> levantes el túnel. Si algún día la URL da 502, significa que el frontend
> (Terminal 2) no está corriendo.

No necesitás configurar CORS ni dominios: `/api` y `/storage` se reenvían al
backend desde `frontend/vite.config.ts`, así que el navegador ve todo como
**mismo origen**.

## 4. Verificación rápida

Abrí la URL del túnel y probá:

1. El listado público "Nuestros deportistas".
2. Login con `admin@club.test` / `password`.
3. Subir una foto en Perfil y recargar: la imagen debe seguir viéndose.

## 5. Cómo detenerlo todo

### Si tenés las terminales abiertas

Andá a cada una y apretá **Ctrl+C**:

| Terminal | Qué detiene |
|---|---|
| Terminal 1 (`php -S ...`) | Backend `:8000` |
| Terminal 2 (`npm run preview ...`) | Frontend `:4173` |
| Terminal 3 (`cloudflared ...`) | Túnel |

### Si ya cerraste las terminales

Pegá esto en **una sola PowerShell**:

```powershell
# Backend (:8000) y frontend (:4173)
Get-NetTCPConnection -LocalPort 8000,4173 -State Listen -ErrorAction SilentlyContinue |
  ForEach-Object { Stop-Process -Id $_.OwningProcess -Force }

# Túnel
Get-Process cloudflared -ErrorAction SilentlyContinue | Stop-Process -Force
```

Para confirmar que no queda nada:

```powershell
Get-NetTCPConnection -LocalPort 8000,4173 -State Listen -ErrorAction SilentlyContinue
Get-Process cloudflared -ErrorAction SilentlyContinue
```

Si no imprimen nada, están todos detenidos. ✅

> No mates MySQL (`:3306`) si querés seguir usando la BD. Se apaga solo si cerrás
> Laragon. Si el backend da 500, es porque MySQL está apagado: prendelo desde
> Laragon.

## Notas

- La URL del túnel cambia en cada reinicio (túnel rápido gratuito). Para una URL
  fija hace falta una cuenta gratis de Cloudflare + un túnel nombrado.
- Los túneles rápidos no tienen garantía de uptime: sirven para una demo, no para
  producción.
- Para exponer solo la red de tu casa/facultad, no necesitás cloudflared:
  `php -S 0.0.0.0:8000 -t . index.php` y `npm run dev -- --host 0.0.0.0`.
- Las credenciales del seeder son `admin@club.test` / `password`.
