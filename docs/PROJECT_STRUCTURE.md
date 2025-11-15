# 📁 ESTRUCTURA DEL PROYECTO
## Mapa Completo de Ubicaciones y Organización

---

## 🎯 PROPÓSITO DE ESTE DOCUMENTO

Este documento es tu **mapa de navegación**. Aquí encontrarás:
- Dónde está cada tipo de archivo
- Qué contiene cada directorio
- Nomenclatura y convenciones
- Ejemplos de rutas reales

**Úsalo cuando necesites**:
- Ubicar un archivo específico
- Saber dónde crear nuevos archivos
- Entender la organización del proyecto

---

## 🌳 ÁRBOL GENERAL

```
reviews/                           ← Raíz del proyecto
│
├── app/                           ← Código de aplicación
│   ├── Console/                   ← Comandos Artisan
│   ├── Exceptions/                ← Manejadores de excepciones
│   ├── Http/                      ← Capa HTTP
│   │   ├── Controllers/           ← Controllers (lógica mínima)
│   │   ├── Middleware/            ← Middlewares personalizados
│   │   ├── Requests/              ← FormRequests (validaciones)
│   │   └── Resources/             ← API Resources
│   ├── Models/                    ← Eloquent Models
│   ├── Policies/                  ← Policies de autorización
│   ├── Providers/                 ← Service Providers
│   ├── Services/                  ← Servicios (lógica de negocio)
│   └── View/
│       └── Components/            ← Componentes Blade
│
├── bootstrap/                     ← Archivos de arranque Laravel
│
├── config/                        ← Archivos de configuración
│   ├── app.php                    ← Config principal (incluye skin)
│   ├── auth.php                   ← Autenticación
│   ├── database.php               ← Conexiones DB
│   └── ...
│
├── database/                      ← Todo relacionado con BD
│   ├── factories/                 ← Model Factories
│   ├── migrations/                ← Migraciones
│   └── seeders/                   ← Seeders
│
├── docs/                          ← DOCUMENTACIÓN COMPLETA
│   ├── AI_HANDOFF.md              ← Guía para IAs (LEER PRIMERO)
│   ├── PROJECT_STRUCTURE.md       ← Este archivo
│   ├── DEVELOPMENT_GUIDE.md       ← Guía de desarrollo
│   ├── architecture.md            ← Decisiones arquitectónicas
│   ├── database.md                ← Esquemas y relaciones
│   ├── services.md                ← Servicios disponibles
│   ├── flows.md                   ← Flujos de usuario
│   ├── skins.md                   ← Sistema de temas
│   ├── endpoints.md               ← Rutas y APIs
│   └── phases/                    ← Documentación por fase
│       ├── phase-0-fundamentos.md
│       ├── phase-1-auth.md
│       └── ...
│
├── public/                        ← Archivos públicos
│   ├── index.php                  ← Entry point
│   └── assets/                    ← Assets estáticos por skin
│       ├── listox/                ← Tema Listox
│       │   ├── css/
│       │   ├── js/
│       │   └── images/
│       ├── dark/                  ← Tema Dark
│       └── pastel/                ← Tema Pastel
│
├── resources/                     ← Recursos de frontend
│   ├── css/                       ← CSS global
│   ├── js/                        ← JavaScript
│   └── views/                     ← Vistas Blade
│       ├── skins/                 ← Vistas organizadas por skin
│       │   ├── listox/            ← Skin Listox (default)
│       │   │   ├── layouts/       ← Layouts del skin
│       │   │   ├── components/    ← Componentes del skin
│       │   │   ├── pages/         ← Páginas completas
│       │   │   └── partials/      ← Parciales reutilizables
│       │   ├── dark/              ← Skin Dark
│       │   └── pastel/            ← Skin Pastel
│       ├── components/            ← Componentes compartidos
│       └── vendor/                ← Vistas de vendors
│
├── routes/                        ← Definición de rutas
│   ├── web.php                    ← Rutas web
│   ├── api.php                    ← Rutas API
│   ├── console.php                ← Comandos CLI
│   └── channels.php               ← Broadcasting
│
├── storage/                       ← Almacenamiento privado
│   ├── app/                       ← Archivos de aplicación
│   │   ├── public/                ← Archivos públicos (symlink)
│   │   └── private/               ← Archivos privados
│   ├── framework/                 ← Cache, sesiones, vistas
│   └── logs/                      ← Logs de Laravel
│
├── tests/                         ← Tests automatizados
│   ├── Feature/                   ← Tests de features
│   │   ├── Auth/
│   │   ├── Network/
│   │   ├── Review/
│   │   └── ...
│   └── Unit/                      ← Tests unitarios
│       ├── Services/
│       └── Models/
│
├── .env                           ← Variables de entorno
├── .env.example                   ← Ejemplo de variables
├── composer.json                  ← Dependencias PHP
├── package.json                   ← Dependencias NPM
├── phpunit.xml                    ← Config de testing
└── README.md                      ← README principal
```

---

## 📂 DETALLES POR DIRECTORIO

### `/app/` - Código de Aplicación

#### `/app/Models/` - Modelos Eloquent

**Propósito**: Representar entidades de la base de datos

**Convención de nombres**:
- Singular, PascalCase
- Nombre de la tabla en plural

**Archivos esperados**:
```
app/Models/
├── User.php                       ← Usuario del sistema
├── Network.php                    ← Red privada
├── Membership.php                 ← Relación User-Network
├── Restaurant.php                 ← Restaurante
├── Review.php                     ← Reseña
├── ReviewMedia.php                ← Medios de reseña (Spatie)
├── ReviewComment.php              ← Comentarios en reseña
├── VisitWish.php                  ← "Por visitar"
└── Invitation.php                 ← Invitaciones a redes
```

**Ejemplo de ruta**: `app/Models/Network.php`

---

#### `/app/Services/` - Servicios (Lógica de Negocio)

**Propósito**: Contener toda la lógica de negocio compleja

**Convención de nombres**:
- `NombreService.php`
- Métodos descriptivos y verbos

**Archivos esperados**:
```
app/Services/
├── NetworkService.php             ← Lógica de redes
│   ├── createNetwork()
│   ├── inviteMember()
│   ├── removeMember()
│   └── getNetworkStats()
│
├── ReviewService.php              ← Lógica de reseñas
│   ├── createReview()
│   ├── attachMedia()
│   ├── attachTicket()
│   └── updateRating()
│
├── RestaurantService.php          ← Lógica de restaurantes
│   ├── searchRestaurants()
│   ├── createRestaurant()
│   └── autocomplete()
│
├── InvitationService.php          ← Lógica de invitaciones
│   ├── sendInvitation()
│   ├── acceptInvitation()
│   └── cancelInvitation()
│
└── MapService.php                 ← Lógica de mapas
    ├── getNetworkMapData()
    ├── filterByBounds()
    └── clusterMarkers()
```

**Ejemplo de uso**:
```php
// En Controller
public function store(StoreReviewRequest $request, ReviewService $reviewService)
{
    $review = $reviewService->createReview(
        $request->validated(),
        auth()->user()
    );

    return redirect()->route('reviews.show', $review);
}
```

---

#### `/app/Http/Controllers/` - Controllers

**Propósito**: Manejar requests HTTP (lógica mínima)

**Convención de nombres**:
- `NombreController.php`
- Métodos RESTful cuando sea posible

**Estructura esperada**:
```
app/Http/Controllers/
├── Auth/                          ← Controllers de autenticación
│   ├── LoginController.php
│   ├── RegisterController.php
│   └── ForgotPasswordController.php
│
├── Network/                       ← Controllers de redes
│   ├── NetworkController.php
│   ├── MemberController.php
│   └── InvitationController.php
│
├── Restaurant/                    ← Controllers de restaurantes
│   ├── RestaurantController.php
│   └── SearchController.php
│
├── Review/                        ← Controllers de reseñas
│   ├── ReviewController.php
│   ├── ReviewCommentController.php
│   └── ReviewMediaController.php
│
├── VisitWishController.php        ← "Por visitar"
├── MapController.php              ← Mapas
└── DashboardController.php        ← Dashboard
```

**Ejemplo de estructura**:
```php
class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index(Network $network)
    public function create(Network $network)
    public function store(StoreReviewRequest $request, Network $network)
    public function show(Review $review)
    public function edit(Review $review)
    public function update(UpdateReviewRequest $request, Review $review)
    public function destroy(Review $review)
}
```

---

#### `/app/Http/Requests/` - Form Requests

**Propósito**: Validaciones de formularios

**Convención de nombres**:
- `AccionNombreRequest.php`
- Ejemplo: `StoreReviewRequest`, `UpdateNetworkRequest`

**Archivos esperados**:
```
app/Http/Requests/
├── Auth/
│   └── RegisterRequest.php
├── Network/
│   ├── StoreNetworkRequest.php
│   ├── UpdateNetworkRequest.php
│   └── InviteMemberRequest.php
├── Review/
│   ├── StoreReviewRequest.php
│   └── UpdateReviewRequest.php
└── Restaurant/
    └── StoreRestaurantRequest.php
```

**Ejemplo**:
```php
class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-review', $this->route('network'));
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'photos.*' => 'nullable|image|max:5120',
            'ticket' => 'nullable|image|max:5120',
        ];
    }
}
```

---

#### `/app/Http/Resources/` - API Resources

**Propósito**: Transformar modelos en respuestas JSON

**Convención de nombres**:
- `NombreResource.php`
- `NombreCollection.php` para colecciones

**Archivos esperados**:
```
app/Http/Resources/
├── NetworkResource.php
├── ReviewResource.php
├── ReviewCollection.php
├── RestaurantResource.php
└── UserResource.php
```

---

#### `/app/Policies/` - Policies

**Propósito**: Autorización y permisos

**Convención de nombres**:
- `NombrePolicy.php`
- Métodos según acciones

**Archivos esperados**:
```
app/Policies/
├── NetworkPolicy.php
│   ├── view()
│   ├── update()
│   ├── delete()
│   └── inviteMembers()
│
├── ReviewPolicy.php
│   ├── view()
│   ├── create()
│   ├── update()
│   └── delete()
│
└── RestaurantPolicy.php
```

---

#### `/app/View/Components/` - Componentes Blade

**Propósito**: Componentes Blade reutilizables con lógica PHP

**Convención de nombres**:
- PascalCase para clases
- kebab-case para uso en Blade

**Estructura esperada**:
```
app/View/Components/
├── Form/
│   ├── Input.php                  ← <x-form.input />
│   ├── Textarea.php               ← <x-form.textarea />
│   ├── Rating.php                 ← <x-form.rating />
│   └── GalleryUpload.php          ← <x-form.gallery-upload />
│
├── Card/
│   ├── Review.php                 ← <x-card.review />
│   ├── Restaurant.php             ← <x-card.restaurant />
│   └── Network.php                ← <x-card.network />
│
└── Map/
    ├── Network.php                ← <x-map.network />
    └── Marker.php                 ← <x-map.marker />
```

---

### `/database/` - Base de Datos

#### `/database/migrations/` - Migraciones

**Convención de nombres**:
- `YYYY_MM_DD_HHMMSS_descripcion.php`
- Laravel las genera automáticamente

**Orden esperado**:
```
database/migrations/
├── 2024_01_01_000000_create_users_table.php
├── 2024_01_01_000001_create_password_reset_tokens_table.php
├── 2024_01_02_000000_create_networks_table.php
├── 2024_01_02_000001_create_memberships_table.php
├── 2024_01_03_000000_create_restaurants_table.php
├── 2024_01_04_000000_create_reviews_table.php
├── 2024_01_05_000000_create_review_comments_table.php
├── 2024_01_06_000000_create_visit_wishes_table.php
└── 2024_01_07_000000_create_invitations_table.php
```

**IMPORTANTE**: Las migraciones deben ejecutarse en orden. Las tablas con foreign keys deben crearse DESPUÉS de sus referencias.

---

#### `/database/seeders/` - Seeders

**Propósito**: Datos de prueba y datos iniciales

**Archivos esperados**:
```
database/seeders/
├── DatabaseSeeder.php             ← Seeder principal
├── UserSeeder.php                 ← Usuarios de prueba
├── NetworkSeeder.php              ← Redes de ejemplo
├── RestaurantSeeder.php           ← Restaurantes
└── ReviewSeeder.php               ← Reseñas de prueba
```

---

### `/resources/views/` - Vistas Blade

#### `/resources/views/skins/` - Sistema de Skins

**Propósito**: Organizar vistas por tema visual

**Estructura POR SKIN**:
```
resources/views/skins/listox/
├── layouts/
│   ├── app.blade.php              ← Layout principal autenticado
│   ├── guest.blade.php            ← Layout para invitados
│   └── dashboard.blade.php        ← Layout del dashboard
│
├── components/
│   ├── button.blade.php           ← <x-button />
│   ├── card.blade.php             ← <x-card />
│   └── form/
│       ├── input.blade.php        ← <x-form.input />
│       ├── textarea.blade.php
│       ├── rating.blade.php
│       └── gallery-upload.blade.php
│
├── pages/
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   └── forgot-password.blade.php
│   │
│   ├── dashboard.blade.php
│   │
│   ├── network/
│   │   ├── index.blade.php        ← Listar redes
│   │   ├── create.blade.php       ← Crear red
│   │   ├── show.blade.php         ← Ver red
│   │   └── edit.blade.php         ← Editar red
│   │
│   ├── review/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── show.blade.php
│   │
│   ├── restaurant/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   │
│   └── map/
│       └── network.blade.php
│
└── partials/
    ├── header.blade.php           ← Header del skin
    ├── footer.blade.php           ← Footer del skin
    ├── nav.blade.php              ← Navegación
    └── sidebar.blade.php          ← Sidebar
```

**Cómo usar skins en código**:
```blade
{{-- En vez de --}}
@extends('layouts.app')

{{-- Usar --}}
@extends(skin('layouts.app'))

{{-- En vez de --}}
@include('components.card')

{{-- Usar --}}
@include(skin('components.card'))
```

---

### `/public/assets/` - Assets Estáticos

**Propósito**: CSS, JS, imágenes organizados por skin

**Estructura**:
```
public/assets/
├── listox/
│   ├── css/
│   │   ├── app.css
│   │   └── components.css
│   ├── js/
│   │   ├── app.js
│   │   └── map.js
│   └── images/
│       ├── logo.png
│       └── icons/
│
├── dark/
│   └── ... (misma estructura)
│
└── pastel/
    └── ... (misma estructura)
```

**En Blade**:
```blade
<link rel="stylesheet" href="/assets/{{ config('app.skin') }}/css/app.css">
<script src="/assets/{{ config('app.skin') }}/js/app.js"></script>
```

---

### `/routes/` - Rutas

#### `/routes/web.php`

**Organización**:
```php
// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Public routes
Route::get('/', HomeController::class)->name('home');

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Networks
    Route::prefix('networks')->name('networks.')->group(function () {
        Route::get('/', [NetworkController::class, 'index'])->name('index');
        Route::get('/create', [NetworkController::class, 'create'])->name('create');
        Route::post('/', [NetworkController::class, 'store'])->name('store');

        Route::prefix('{network}')->group(function () {
            Route::get('/', [NetworkController::class, 'show'])->name('show');
            Route::get('/edit', [NetworkController::class, 'edit'])->name('edit');
            Route::put('/', [NetworkController::class, 'update'])->name('update');
            Route::delete('/', [NetworkController::class, 'destroy'])->name('destroy');

            // Reviews dentro de network
            Route::resource('reviews', ReviewController::class);

            // Map
            Route::get('/map', [MapController::class, 'show'])->name('map');
        });
    });

    // Restaurants (global search)
    Route::resource('restaurants', RestaurantController::class);
    Route::get('/restaurants/search', [RestaurantSearchController::class, 'search'])
        ->name('restaurants.search');
});
```

---

### `/docs/` - Documentación

**Archivos CRÍTICOS**:

```
docs/
├── AI_HANDOFF.md                  ← LEER PRIMERO (guía para IAs)
├── PROJECT_STRUCTURE.md           ← ESTE ARCHIVO (mapa de ubicaciones)
├── DEVELOPMENT_GUIDE.md           ← Guía paso a paso de desarrollo
│
├── architecture.md                ← Decisiones arquitectónicas
├── database.md                    ← Esquemas, relaciones, migraciones
├── services.md                    ← Servicios disponibles y uso
├── flows.md                       ← Flujos de usuario (diagramas)
├── skins.md                       ← Sistema de temas (cómo crear uno)
├── endpoints.md                   ← Lista de rutas y APIs
│
└── phases/                        ← Documentación por fase
    ├── phase-0-fundamentos.md
    ├── phase-1-auth.md
    ├── phase-2-networks.md
    ├── phase-3-restaurants.md
    ├── phase-4-reviews.md
    ├── phase-5-comments.md
    ├── phase-6-visit-wish.md
    ├── phase-7-map.md
    ├── phase-8-filters.md
    ├── phase-9-super-system.md
    ├── phase-10-skins.md
    ├── phase-11-testing.md
    └── phase-12-deploy.md
```

---

## 🔍 CÓMO ENCONTRAR ARCHIVOS

### Por Funcionalidad

| Quiero... | Buscar en... |
|-----------|-------------|
| Modificar lógica de negocio | `/app/Services/` |
| Cambiar validaciones | `/app/Http/Requests/` |
| Ajustar permisos | `/app/Policies/` |
| Modificar vistas | `/resources/views/skins/{skin}/` |
| Cambiar estilos | `/public/assets/{skin}/css/` |
| Ver esquema de BD | `/database/migrations/` |
| Añadir rutas | `/routes/web.php` |
| Crear componente | `/app/View/Components/` y `/resources/views/skins/{skin}/components/` |

### Por Modelo

| Modelo | Archivos relacionados |
|--------|----------------------|
| Network | `app/Models/Network.php`<br>`app/Services/NetworkService.php`<br>`app/Http/Controllers/Network/NetworkController.php`<br>`app/Policies/NetworkPolicy.php`<br>`resources/views/skins/listox/pages/network/` |
| Review | `app/Models/Review.php`<br>`app/Services/ReviewService.php`<br>`app/Http/Controllers/Review/ReviewController.php`<br>`app/Policies/ReviewPolicy.php`<br>`resources/views/skins/listox/pages/review/` |

---

## 📝 CONVENCIONES DE NOMENCLATURA

### Archivos PHP

| Tipo | Convención | Ejemplo |
|------|-----------|---------|
| Modelo | PascalCase singular | `Network.php` |
| Controller | PascalCase + Controller | `NetworkController.php` |
| Service | PascalCase + Service | `NetworkService.php` |
| Request | Accion + Nombre + Request | `StoreReviewRequest.php` |
| Policy | PascalCase + Policy | `NetworkPolicy.php` |
| Migration | snake_case | `create_networks_table.php` |

### Archivos Blade

| Tipo | Convención | Ejemplo |
|------|-----------|---------|
| Layout | kebab-case | `app.blade.php` |
| Página | kebab-case | `create.blade.php` |
| Componente | kebab-case | `rating-stars.blade.php` |
| Parcial | kebab-case prefijo _ | `_header.blade.php` |

### Rutas

| Tipo | Convención | Ejemplo |
|------|-----------|---------|
| URL | kebab-case | `/my-networks` |
| Nombre ruta | dot notation | `networks.reviews.create` |

---

## 🎯 UBICACIONES RÁPIDAS

### Necesito crear...

**Un nuevo modelo**:
```bash
php artisan make:model Network -mfsc
# Crea: Model, Migration, Factory, Seeder, Controller
# Ubicación: app/Models/Network.php
```

**Un servicio**:
```bash
php artisan make:class Services/NetworkService
# Ubicación: app/Services/NetworkService.php
```

**Un componente Blade**:
```bash
php artisan make:component Form/Rating
# Crea: app/View/Components/Form/Rating.php
# Vista: resources/views/components/form/rating.blade.php
```

**Un FormRequest**:
```bash
php artisan make:request StoreReviewRequest
# Ubicación: app/Http/Requests/StoreReviewRequest.php
```

**Una Policy**:
```bash
php artisan make:policy NetworkPolicy --model=Network
# Ubicación: app/Policies/NetworkPolicy.php
```

---

## ⚠️ LUGARES PROHIBIDOS

### NO crear archivos en:

- `/vendor/` - Dependencias de Composer
- `/node_modules/` - Dependencias de NPM
- `/storage/framework/` - Cache de Laravel
- `/bootstrap/cache/` - Cache de arranque

### NO modificar directamente:

- Vistas de vendors (usar `php artisan vendor:publish` primero)
- Archivos de configuración de Laravel sin entender el impacto
- Migraciones ya aplicadas (crear nuevas en su lugar)

---

## 📊 DIAGRAMA DE DEPENDENCIAS

```
Usuario hace request
    ↓
/routes/web.php
    ↓
HTTP Middleware
    ↓
Controller (app/Http/Controllers/)
    ↓
FormRequest (app/Http/Requests/) → valida
    ↓
Service (app/Services/) → lógica
    ↓
Model (app/Models/) → datos
    ↓
Database
    ↑
Policy (app/Policies/) → autoriza
    ↓
Controller recibe resultado
    ↓
Resource (app/Http/Resources/) → transforma (si API)
    O
View (resources/views/skins/{skin}/) → renderiza (si web)
    ↓
Response al usuario
```

---

## 🔧 ARCHIVOS DE CONFIGURACIÓN

### Principales

```
/
├── .env                           ← Variables de entorno (NO versionar)
├── .env.example                   ← Ejemplo de .env (SÍ versionar)
├── composer.json                  ← Dependencias PHP
├── package.json                   ← Dependencias NPM
├── phpunit.xml                    ← Configuración de tests
├── vite.config.js                 ← Build de assets
└── tailwind.config.js             ← Configuración de Tailwind
```

### En `/config/`

```
config/
├── app.php                        ← Config general (INCLUYE skin)
├── auth.php                       ← Autenticación
├── database.php                   ← Base de datos
├── filesystems.php                ← Almacenamiento
├── media-library.php              ← Spatie MediaLibrary
└── permission.php                 ← Spatie Permissions
```

---

## 📦 ASSETS Y PÚBLICOS

### Storage (privado)

```
storage/
├── app/
│   ├── public/                    ← Accesible vía /storage (symlink)
│   │   ├── networks/              ← Logos de redes
│   │   ├── reviews/               ← Fotos de reseñas
│   │   └── tickets/               ← Tickets
│   └── private/                   ← NO accesible públicamente
│       └── backups/
└── logs/
    └── laravel.log                ← Logs principales
```

### Public (accesible)

```
public/
├── index.php                      ← Entry point
├── storage/                       ← Symlink a storage/app/public
└── assets/
    ├── listox/
    ├── dark/
    └── pastel/
```

---

## 🧪 Tests

```
tests/
├── Feature/                       ← Tests de funcionalidad completa
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegisterTest.php
│   ├── Network/
│   │   ├── CreateNetworkTest.php
│   │   ├── InviteMemberTest.php
│   │   └── NetworkDashboardTest.php
│   └── Review/
│       ├── CreateReviewTest.php
│       └── UploadMediaTest.php
│
└── Unit/                          ← Tests unitarios
    ├── Services/
    │   ├── NetworkServiceTest.php
    │   └── ReviewServiceTest.php
    └── Models/
        ├── NetworkTest.php
        └── ReviewTest.php
```

---

## 📌 RESUMEN EJECUTIVO

### Los 5 directorios más importantes:

1. **`/app/Services/`** - Toda la lógica de negocio
2. **`/app/Models/`** - Modelos de datos
3. **`/resources/views/skins/`** - Todas las vistas
4. **`/routes/web.php`** - Definición de rutas
5. **`/docs/`** - Documentación completa

### Los 3 archivos que debes leer:

1. **`/docs/AI_HANDOFF.md`** - Guía para IAs
2. **`/docs/PROJECT_STRUCTURE.md`** - Este archivo
3. **`/docs/DEVELOPMENT_GUIDE.md`** - Guía de desarrollo

### El 1 concepto clave:

**Separación de responsabilidades**:
- Controllers → mínimo
- Services → lógica
- Models → datos
- Views → presentación
- Policies → autorización

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0

---

## ⏭️ PRÓXIMO PASO

**Lee ahora**: `/docs/DEVELOPMENT_GUIDE.md`

Este documento te guiará paso a paso en el desarrollo de cada funcionalidad.
