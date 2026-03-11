# Despliegue a hosting (Laravel) en **Plesk** — paso a paso seguro

Esta guía está optimizada para servidores administrados con **Plesk** (no cPanel), evitando errores típicos de despliegue en producción.

---

## 0) Antes de tocar el servidor (checklist obligatorio)

1. Ten el proyecto funcionando local.
2. Asegúrate de tener `main` actualizado en GitHub.
3. Confirma en Plesk:
   - Dominio/subdominio creado.
   - Versión de PHP (ideal 8.2+).
   - Acceso a **File Manager** y/o **SSH**.
   - Acceso a base de datos MySQL.

---

## 1) Congelar versión de despliegue (en tu laptop)

```bash
git checkout main
git pull origin main
```

Si hay cambios pendientes:

```bash
git add .
git commit -m "chore: release para plesk"
git push origin main
```

---

## 2) Crear base de datos en Plesk

En Plesk:

1. Ve a **Websites & Domains → Databases**.
2. Crea la base de datos.
3. Crea usuario y contraseña de BD.
4. Guarda estos datos:
   - `DB_HOST` (frecuente: `localhost`)
   - `DB_PORT` (`3306`)
   - `DB_DATABASE`
   - `DB_USERNAME`
   - `DB_PASSWORD`

---

## 3) Subir proyecto al servidor (Plesk)

Tienes 2 opciones:

### Opción A: con SSH (recomendado)

```bash
cd /var/www/vhosts/TU_DOMINIO/
git clone TU_REPO.git app
cd app
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

### Opción B: sin SSH (File Manager de Plesk)

1. Comprime el proyecto en ZIP localmente.
2. Sube el ZIP por **Plesk File Manager** a una carpeta fuera de `httpdocs` (por ejemplo `app`).
3. Extrae el ZIP.
4. Verifica estructura Laravel (`app`, `bootstrap`, `config`, `public`, `resources`, `routes`, `storage`, `vendor`).

> Si no puedes correr Composer en servidor, sube también `vendor/` generado en local.

---

## 4) Configurar `.env` de producción

Edita `.env` en servidor:

```env
APP_NAME=Inventario
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://TU_DOMINIO

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=TU_DB
DB_USERNAME=TU_USER
DB_PASSWORD=TU_PASSWORD
```

Luego:

```bash
php artisan key:generate
```

---

## 5) Configuración web en Plesk (clave)

En Plesk, para el dominio/subdominio:

1. Ve a **Websites & Domains → Hosting Settings**.
2. Ajusta **Document root** para que apunte a la carpeta `public` de Laravel.
   - Ejemplo: `httpdocs` apuntando a `/app/public` (según cómo lo subiste).
3. Guarda cambios.

> Laravel debe servir desde `public/`, nunca desde la raíz del proyecto.

### Validación rápida de tu captura (como la enviaste)

Si en **Hosting Settings** te aparece algo como:

```text
jrmovilidad.fycconsultores.com/public
```

entonces el **Document root está bien configurado** para Laravel.

Lo que sigue en ese caso es:

1. Confirmar que dentro de esa carpeta exista `build/manifest.json`.
2. Si no existe, compilar en local (`npm run build`) y subir `public/build/`.
3. Limpiar cachés de Laravel (`php artisan optimize:clear`).

> Nota: el error que mostraste (`ViteManifestNotFoundException`) normalmente no es por Document Root cuando ya apunta a `/public`, sino porque falta `public/build/manifest.json`.

---

## 6) Si no puedes apuntar Document Root a `public/`

Como alternativa:

1. Deja proyecto completo en una carpeta privada (ej: `/var/www/vhosts/.../app`).
2. Copia el contenido de `app/public/` a `httpdocs/`.
3. Ajusta `httpdocs/index.php`:

```php
require __DIR__.'/../app/vendor/autoload.php';
$app = require_once __DIR__.'/../app/bootstrap/app.php';
```

---

## 7) Comandos Laravel en producción (Plesk SSH o terminal)

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si no tienes terminal/SSH, usa extensión de terminal en Plesk o solicita ejecución al soporte.

### 7.1) Poblar base de datos (usuarios, roles, catálogos)

Si ya abre el login pero no te deja entrar, normalmente la BD está vacía o sin usuarios iniciales.

Ejecuta (en servidor):

```bash
php artisan migrate:status
php artisan migrate --force
php artisan db:seed --force
```

> Si tu proyecto usa seeders por migración, también puedes usar:

```bash
php artisan migrate --seed --force
```

### 7.2) Crear usuario administrador manual (si no existe seeder)

Opción rápida con Tinker:

```bash
php artisan tinker
```

Luego pega (ajusta email/clave):

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::updateOrCreate(
    ['email' => 'admin@inventariojr.com'],
    [
        'name' => 'Administrador',
        'password' => Hash::make('Cambiar123!'),
    ]
);
```

Para salir de tinker:

```php
exit
```

### 7.3) Verificación rápida de usuarios en BD

```bash
php artisan tinker --execute="App\\Models\\User::count();"
```

Si responde `0`, no hay usuarios creados todavía.

---

## 8) Permisos (evitar error 500)

Asegura escritura en:

- `storage/`
- `bootstrap/cache/`

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 9) Frontend (Vite)

En local:

```bash
npm install
npm run build
```

Sube al servidor la carpeta:

- `public/build/`

---

## 10) Error exacto en tu caso: `ViteManifestNotFoundException`

Si ves en Plesk/Laravel:

```text
Vite manifest not found at .../public/build/manifest.json
```

haz esto en orden:

1. Compila local:

```bash
npm install
npm run build
```

2. Verifica archivo:

```bash
# Windows PowerShell
dir public\build\manifest.json

# Linux/macOS
ls -lah public/build/manifest.json
```

3. Sube `public/build/` completo al servidor.
4. En Plesk File Manager confirma la ruta correcta según Document Root:
   - si Document Root = `.../app/public` → debe existir `.../app/public/build/manifest.json`
   - si usas `httpdocs` con copia de `public` → debe existir `.../httpdocs/build/manifest.json`
5. Limpia cachés:

```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

6. Refresca navegador en incógnito.

---


## 11) Redirigir dominio raíz (`/`) directo a `/login`

Si al entrar a `https://jrmovilidad.fycconsultores.com` ves la pantalla de bienvenida de Laravel,
debes cambiar la ruta raíz en `routes/web.php`.

### Opción recomendada (en código Laravel)

En `routes/web.php`, deja la ruta `/` así:

```php
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
```

Si ya existe una ruta `/` con `return view('welcome')`, reemplázala por el `Route::redirect`.

Luego ejecuta en servidor:

```bash
php artisan optimize:clear
php artisan route:cache
```

### Opción temporal (solo servidor web)

También podrías redirigir desde configuración web, pero lo correcto es hacerlo en `routes/web.php`
para que quede versionado en tu proyecto.

---

## 12) Ajuste funcional (corrección): vendedor ve TODOS los reportes igual que admin

Perfecto, dejamos el comportamiento así:

- `admin` ✅ ve todos los reportes.
- `vendedor` ✅ ve todos los reportes (igual que admin).

### Paso a paso (exacto) — qué cambiar

#### Paso 1: editar rutas
Archivo: `routes/web.php`

Busca el bloque de reportes y **reemplázalo** por este:

```php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::middleware(['auth', 'role:admin,vendedor'])->group(function () {
    Route::get('/reportes', [ReportController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/ventas', [ReportController::class, 'ventas'])->name('reportes.ventas');
    Route::get('/reportes/ventas/exportar', [ReportController::class, 'exportVentas'])->name('reportes.ventas.exportar');
});
```

#### Paso 2: revisar controlador de reportes
Archivo: `app/Http/Controllers/ReportController.php`

Si tienes filtros tipo:

- `where('user_id', auth()->id())`
- filtros por rol vendedor para ver solo "sus" ventas

**elimínalos** en los métodos de reportes (`index`, `ventas`, `exportVentas`) para que admin y vendedor vean el mismo universo de datos.

#### Paso 3: limpiar caché de rutas/config
En servidor:

```bash
php artisan optimize:clear
php artisan route:cache
```

#### Paso 4: validar permisos por rol

1. Login con admin: debe ver todos los reportes.
2. Login con vendedor: debe ver exactamente los mismos reportes.
3. Login con rol no autorizado: debe recibir `403` o redirección.

> Nota: esta corrección deja a vendedor con el mismo alcance de reportes que admin por decisión funcional.

---

## 13) Verificación post-despliegue

1. Probar `/login`.
2. Probar flujo principal de negocio.
3. Revisar log Laravel:
   - `storage/logs/laravel.log`
4. Si falla:
   - `.env` correcto
   - permisos
   - document root correcto
   - `public/build/manifest.json` presente

---

## 14) Seguridad y operación

1. `APP_DEBUG=false` en producción.
2. SSL activo (Let's Encrypt en Plesk).
3. Backups automáticos (Plesk Backup Manager).
4. Si usas scheduler Laravel, agrega tarea cron en Plesk:

```bash
* * * * * php /var/www/vhosts/TU_DOMINIO/app/artisan schedule:run >> /dev/null 2>&1
```

---

## 15) Rollback rápido

Antes de desplegar: backup de archivos y BD.

Si algo falla:
1. Restaurar backup de archivos.
2. Restaurar BD.
3. `php artisan optimize:clear`.

---

## 16) Checklist final "OK producción"

- [ ] `main` al día.
- [ ] `.env` de producción correcto.
- [ ] Migraciones aplicadas.
- [ ] Seeders ejecutados (`db:seed --force`).
- [ ] Usuario administrador creado/verificado.
- [ ] `storage:link` creado.
- [ ] Permisos correctos.
- [ ] Document Root correcto en Plesk.
- [ ] Ruta `/` redirige a `/login`.
- [ ] Admin y vendedor visualizan todos los reportes (mismo alcance).
- [ ] `public/build/manifest.json` existe.
- [ ] SSL activo.
- [ ] Backups y cron configurados.
