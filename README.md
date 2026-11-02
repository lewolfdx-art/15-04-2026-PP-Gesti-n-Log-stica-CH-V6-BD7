# Concretera Mantaro — Sistema de gestión y web

Sistema de gestión para una empresa de concreto premezclado, con su web pública.

- **Panel de administración** (`/admin`): contratos y cobranza (adelantos, abonos, saldo), cotizaciones,
  programación de vaciados con control de cruces de mixers y bombas, vehículos, personal y pagos
  (sueldos y comisiones), ingresos, gastos, cuentas por pagar, Libro de Reclamaciones y contenido de la web.
- **Panel de campo** (`/operador`): los asesores registran cotizaciones desde la obra (ubicación, fotos,
  adelanto) y los operadores ven sus vaciados con el mapa.
- **Web pública**: Inicio con "Cotiza tu obra", Nosotros, Productos, Sostenibilidad, Experiencia,
  Concretips, Contacto y Libro de Reclamaciones (Ley 29571).

**Tecnología:** Laravel 12, Filament 5, Livewire 4, PHP 8.3, MySQL 8, Vite.

## Instalación

```bash
composer install
npm install && npm run build
cp .env.example .env          # completar DB_*, APP_URL, MAIL_*, EMPRESA_RUC
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan make:superadmin   # crea el primer administrador (contraseña en SUPER_ADMIN_PASSWORD)
```

Las migraciones funcionan en una base nueva tanto en MySQL como en SQLite.

## Pruebas

```bash
php artisan test
```

Más de 160 comprobaciones del sistema: cobranza y pagos, adelantos y fechas separadas, comisiones,
cruces de vehículos, permisos por rol, web pública y formularios, vista previa por ngrok, widgets del
Escritorio y respaldos.

Las pruebas usan una base **SQLite en memoria** (ver `phpunit.xml`) y nunca tocan la base MySQL.
`tests/TestCase.php` tiene un candado: si la configuración apunta a otra base (por ejemplo, después de un
`php artisan config:cache`), las pruebas se detienen antes de tocar nada. En ese caso, ejecuta
`php artisan config:clear` y vuelve a correrlas.

## Ajustes en `.env`

| Variable | Para qué |
|---|---|
| `EMPRESA_RAZON_SOCIAL`, `EMPRESA_RUC`, `EMPRESA_DIRECCION` | Datos del proveedor en el Libro de Reclamaciones |
| `MYSQLDUMP_PATH` | Ruta de `mysqldump` para los respaldos de la base |
| `NOTIFICACIONES_CADA` | Cada cuánto el panel revisa notificaciones nuevas (`10s` por defecto) |
| `VISTA_PREVIA_HOSTS` | Dominios extra (además de ngrok) que solo muestran la web pública |
