# El Naranjo Conecta — Backend (Laravel API)

API REST en Laravel + MySQL, organizada por dominio (no por tipo de archivo)
para que cada módulo del MVP se pueda tocar sin pisar los demás.

## Estructura

```
app/
  Domain/
    Usuarios/       Roles, autenticación (AuthController con Sanctum)
    Categorias/      Taxonomía compartida por Servicios/Comercios/Productores
    Servicios/       Prestadores de servicios (oficios)
    Comercios/       Directorio de comercios
    Productores/     Productores locales
    Empleos/         Busco trabajo / Busco trabajador
    Reclamos/        Servicios públicos y reclamos (con flujo de estados)
    Instituciones/   Directorio de instituciones (solo admin)
    Avisos/          Comunicación comunitaria (solo admin)
  Models/User.php    Modelo de autenticación (vive en app/Models por convención de Laravel)
```

Cada módulo sigue el mismo patrón interno:

```
Domain/<Modulo>/
  Models/<Modelo>.php
  Http/Controllers/<Modelo>Controller.php
  Http/Requests/<Modelo>Request.php   (validación)
  Http/Resources/<Modelo>Resource.php (forma de la respuesta JSON)
```

Para agregar un módulo nuevo: creá una migración, un modelo, un controller,
un resource y (si hace falta) un form request siguiendo ese mismo patrón, y
sumá las rutas en `routes/api.php`.

## Requisitos

- PHP >= 8.2, Composer
- MySQL >= 8

## Puesta en marcha

```bash
composer install
cp .env.example .env
php artisan key:generate

# configurar DB_* en .env (usuario/clave de tu MySQL local)
php artisan migrate --seed   # crea las tablas + categorías base + un admin

php artisan serve            # http://localhost:8000
```

El seeder crea un usuario admin de prueba:
`admin@elnaranjoconecta.local` / `cambiar-esta-clave` — **cambiar la clave
antes de ir a producción** (o borrar el seeder de admin y crear el usuario
a mano).

## Autenticación

API stateless con **Laravel Sanctum** (tokens, no cookies) para que el
front (SPA/PWA) pueda loguearse simple:

- `POST /api/auth/register` `{ nombre, email, password, rol? }`
- `POST /api/auth/login` `{ email, password }` → `{ user, token }`
- `POST /api/auth/logout` (requiere `Authorization: Bearer <token>`)
- `GET /api/auth/me`

El resto de los endpoints (`/api/servicios`, `/api/comercios`, etc.) son
públicos para `GET` (listar/ver) y requieren token para crear/editar/borrar.
Instituciones y Avisos los administra solo el rol `admin`.

## Rutas principales

| Método | Ruta | Descripción |
| --- | --- | --- |
| GET | `/api/servicios?lat=&lng=&q=&categoria_id=` | Directorio de prestadores, con "buscar cerca de mí" |
| GET/POST/PUT/DELETE | `/api/comercios` | Directorio de comercios |
| GET/POST/PUT/DELETE | `/api/productores` | Productores locales |
| GET/POST/PUT/DELETE | `/api/empleos?tipo=busco_trabajo\|busco_trabajador` | Busco trabajo / Busco trabajador |
| GET/POST/PUT/DELETE | `/api/reclamos` | Reclamos del vecino |
| PATCH | `/api/reclamos/{id}/estado` | Cambiar estado (solo admin) |
| GET/POST/PUT/DELETE | `/api/instituciones` | Directorio de instituciones (solo admin escribe) |
| GET/POST/PUT/DELETE | `/api/avisos` | Comunicados/eventos (solo admin escribe) |
| GET/POST/PUT/DELETE | `/api/categorias?modulo=` | Categorías por módulo |

## Notas de privacidad (ver el documento del proyecto)

Los módulos que geolocalizan a una persona (Servicios, Empleos) guardan
`lat`/`lng` como **ubicación aproximada**, nunca el domicilio exacto — esa
decisión la toma el usuario al publicar, en el front.

## Tests

```bash
php artisan test
```

Usan SQLite en memoria (configurado en `phpunit.xml`), no tocan tu base MySQL.
