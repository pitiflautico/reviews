# 🏛️ ARQUITECTURA DEL PROYECTO
## Decisiones Arquitectónicas y Patrones de Diseño

---

## 🎯 PROPÓSITO

Este documento explica:
- Decisiones arquitectónicas fundamentales
- Patrones de diseño utilizados
- Principios de organización
- Razones detrás de cada decisión

---

## 📐 PRINCIPIOS FUNDAMENTALES

### 1. Separación de Responsabilidades (SoC)

**Principio**: Cada componente tiene UNA responsabilidad clara.

**Implementación**:
```
Controller  → Recibir request, delegar, devolver response
Service     → Lógica de negocio
Model       → Acceso a datos, relaciones
Policy      → Autorización
FormRequest → Validación
Resource    → Transformación de datos
View        → Presentación
```

**Ejemplo incorrecto**:
```php
// ❌ Controller con lógica de negocio
class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // Validación en controller
        $validated = $request->validate([...]);

        // Lógica de negocio en controller
        $review = Review::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                // Subir archivos en controller
                $path = $photo->store('reviews');
                ReviewMedia::create([...]);
            }
        }

        // Cálculos en controller
        $avgRating = Review::where('restaurant_id', $review->restaurant_id)
            ->avg('rating');

        // Notificaciones en controller
        $members = $review->network->members;
        foreach ($members as $member) {
            Mail::to($member)->send(...);
        }

        return redirect()->back();
    }
}
```

**Ejemplo correcto**:
```php
// ✅ Controller limpio
class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function store(StoreReviewRequest $request, Network $network)
    {
        $review = $this->reviewService->createReview(
            $network,
            $request->validated(),
            auth()->user()
        );

        return redirect()
            ->route('reviews.show', $review)
            ->with('success', 'Reseña creada correctamente');
    }
}

// ✅ Service con lógica
class ReviewService
{
    public function __construct(
        private MediaService $mediaService,
        private NotificationService $notificationService
    ) {}

    public function createReview(Network $network, array $data, User $user): Review
    {
        DB::beginTransaction();
        try {
            $review = Review::create([
                'network_id' => $network->id,
                'user_id' => $user->id,
                'restaurant_id' => $data['restaurant_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'date_of_visit' => $data['date_of_visit'],
            ]);

            if (isset($data['photos'])) {
                $this->mediaService->attachPhotos($review, $data['photos']);
            }

            if (isset($data['ticket'])) {
                $this->mediaService->attachTicket($review, $data['ticket']);
            }

            $this->notificationService->notifyNewReview($review);

            DB::commit();
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

---

### 2. DRY (Don't Repeat Yourself)

**Principio**: No duplicar código, usar componentes reutilizables.

**Implementación**:
- Blade Components para UI repetitiva
- Services para lógica común
- Traits para comportamiento compartido
- Helpers para funciones globales

**Ejemplo**:
```php
// ❌ Repetir código
// En NetworkController
public function show(Network $network)
{
    $stats = [
        'total_reviews' => $network->reviews()->count(),
        'total_members' => $network->members()->count(),
        'avg_rating' => $network->reviews()->avg('rating'),
    ];
    return view('network.show', compact('network', 'stats'));
}

// En DashboardController
public function index()
{
    $networks = auth()->user()->networks;
    foreach ($networks as $network) {
        $network->stats = [
            'total_reviews' => $network->reviews()->count(),
            'total_members' => $network->members()->count(),
            'avg_rating' => $network->reviews()->avg('rating'),
        ];
    }
    return view('dashboard', compact('networks'));
}

// ✅ Usar Service
class NetworkService
{
    public function getNetworkStats(Network $network): array
    {
        return [
            'total_reviews' => $network->reviews()->count(),
            'total_members' => $network->members()->count(),
            'avg_rating' => round($network->reviews()->avg('rating'), 1),
            'total_restaurants' => $network->reviews()->distinct('restaurant_id')->count(),
        ];
    }
}

// En Controllers
public function show(Network $network, NetworkService $service)
{
    $stats = $service->getNetworkStats($network);
    return view('network.show', compact('network', 'stats'));
}
```

---

### 3. SOLID Principles

#### S - Single Responsibility
Cada clase tiene una sola razón para cambiar.

```php
// ✅ Correcto
class ReviewService { } // Solo lógica de reseñas
class MediaService { }   // Solo manejo de medios
class EmailService { }   // Solo envío de emails
```

#### O - Open/Closed
Abierto a extensión, cerrado a modificación.

```php
// ✅ Usar interfaces y dependency injection
interface NotificationChannel
{
    public function send(User $user, string $message): void;
}

class EmailChannel implements NotificationChannel { }
class SmsChannel implements NotificationChannel { }
class PushChannel implements NotificationChannel { }

class NotificationService
{
    public function __construct(
        private array $channels = []
    ) {}

    public function addChannel(NotificationChannel $channel)
    {
        $this->channels[] = $channel;
    }

    public function notify(User $user, string $message)
    {
        foreach ($this->channels as $channel) {
            $channel->send($user, $message);
        }
    }
}
```

#### L - Liskov Substitution
Las subclases deben ser sustituibles por sus clases base.

#### I - Interface Segregation
Interfaces específicas mejor que una general.

#### D - Dependency Inversion
Depender de abstracciones, no de implementaciones.

```php
// ✅ Dependency Injection
class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
        private RestaurantService $restaurantService
    ) {}
}
```

---

## 🏗️ ARQUITECTURA EN CAPAS

```
┌─────────────────────────────────────────┐
│         PRESENTATION LAYER              │
│  (Views, Blade Components, Resources)   │
└─────────────────────────────────────────┘
                  ↕
┌─────────────────────────────────────────┐
│         APPLICATION LAYER               │
│  (Controllers, Requests, Middleware)    │
└─────────────────────────────────────────┘
                  ↕
┌─────────────────────────────────────────┐
│         BUSINESS LOGIC LAYER            │
│         (Services, Policies)            │
└─────────────────────────────────────────┘
                  ↕
┌─────────────────────────────────────────┐
│         DATA ACCESS LAYER               │
│         (Models, Repositories)          │
└─────────────────────────────────────────┘
                  ↕
┌─────────────────────────────────────────┐
│         DATABASE                        │
└─────────────────────────────────────────┘
```

**Reglas**:
- Una capa solo puede acceder a la capa inmediatamente inferior
- No saltar capas (Controller NO debe acceder directamente a Model)
- Las capas superiores no conocen detalles de las inferiores

---

## 🎨 PATRÓN: SISTEMA DE SKINS

### Problema
Necesitamos cambiar la apariencia visual sin modificar la lógica.

### Solución
Sistema de skins basado en directorios.

### Implementación

**1. Configuración**
```php
// config/app.php
'skin' => env('APP_SKIN', 'listox'),

// .env
APP_SKIN=listox
```

**2. Helper Global**
```php
// app/helpers.php
if (!function_exists('skin')) {
    function skin(string $path): string
    {
        $skin = config('app.skin', 'listox');
        return "skins.{$skin}.{$path}";
    }
}

// composer.json
"autoload": {
    "files": [
        "app/helpers.php"
    ]
}
```

**3. Uso en Vistas**
```blade
{{-- Layouts --}}
@extends(skin('layouts.app'))

{{-- Components --}}
@include(skin('components.card'))

{{-- Assets --}}
<link rel="stylesheet" href="/assets/{{ config('app.skin') }}/css/app.css">
```

**4. Estructura**
```
resources/views/skins/
├── listox/     ← Skin completo
├── dark/       ← Skin completo
└── pastel/     ← Skin completo

public/assets/
├── listox/     ← Assets del skin
├── dark/
└── pastel/
```

### Beneficios
- Cambio de tema en tiempo de ejecución
- Temas completamente independientes
- Facilita A/B testing
- White-label ready

---

## 🔐 PATRÓN: PRIVACIDAD POR REDES

### Problema
Los datos de una red NO deben ser visibles para miembros de otras redes.

### Solución
Scopes automáticos + Policies + Middleware

### Implementación

**1. Scopes en Modelos**
```php
// app/Models/Review.php
class Review extends Model
{
    protected static function booted()
    {
        // Scope global: solo reviews de redes del usuario
        static::addGlobalScope('network', function (Builder $builder) {
            if (auth()->check()) {
                $networkIds = auth()->user()->networks()->pluck('networks.id');
                $builder->whereIn('network_id', $networkIds);
            }
        });
    }
}
```

**2. Policies**
```php
// app/Policies/ReviewPolicy.php
class ReviewPolicy
{
    public function view(User $user, Review $review): bool
    {
        return $user->networks()
            ->where('networks.id', $review->network_id)
            ->exists();
    }

    public function update(User $user, Review $review): bool
    {
        return $review->user_id === $user->id;
    }
}
```

**3. Middleware**
```php
// app/Http/Middleware/EnsureUserBelongsToNetwork.php
class EnsureUserBelongsToNetwork
{
    public function handle(Request $request, Closure $next)
    {
        $network = $request->route('network');

        if (!$request->user()->networks()->where('networks.id', $network->id)->exists()) {
            abort(403, 'No perteneces a esta red');
        }

        return $next($request);
    }
}
```

**4. Rutas**
```php
Route::middleware(['auth', 'belongs-to-network'])
    ->prefix('networks/{network}')
    ->group(function () {
        Route::resource('reviews', ReviewController::class);
    });
```

### Beneficios
- Privacidad garantizada a nivel de código
- No depende de validaciones manuales
- Imposible acceder a datos de otras redes

---

## 🔄 PATRÓN: SERVICE LAYER

### Problema
Controllers con demasiada lógica, difíciles de testear y mantener.

### Solución
Capa de servicios que encapsula lógica de negocio.

### Cuándo usar Services

**✅ Crear Service cuando**:
- La lógica involucra múltiples modelos
- Hay cálculos complejos
- Se envían notificaciones
- Se manejan archivos
- Hay transacciones de base de datos
- La lógica se reutiliza en varios lugares

**❌ NO crear Service cuando**:
- Es un simple CRUD
- No hay lógica compleja
- Solo se consulta un modelo

### Estructura de un Service

```php
namespace App\Services;

use App\Models\Network;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NetworkService
{
    /**
     * Crea una nueva red y asigna al usuario como owner
     */
    public function createNetwork(array $data, User $user): Network
    {
        DB::beginTransaction();
        try {
            // Crear red
            $network = Network::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            // Subir logo si existe
            if (isset($data['logo'])) {
                $network->addMedia($data['logo'])
                    ->toMediaCollection('logo');
            }

            // Asignar usuario como owner
            $network->members()->attach($user->id, [
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            DB::commit();
            return $network->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Invita a un usuario a la red
     */
    public function inviteMember(Network $network, string $email, string $role = 'member'): Invitation
    {
        // Lógica de invitación
    }

    /**
     * Obtiene estadísticas de la red
     */
    public function getNetworkStats(Network $network): array
    {
        return [
            'total_reviews' => $network->reviews()->count(),
            'total_members' => $network->members()->count(),
            'avg_rating' => round($network->reviews()->avg('rating'), 1),
            'total_restaurants' => $network->reviews()
                ->distinct('restaurant_id')
                ->count(),
            'recent_activity' => $this->getRecentActivity($network),
        ];
    }

    /**
     * Método privado helper
     */
    private function getRecentActivity(Network $network): Collection
    {
        // Lógica privada
    }
}
```

### Inyección de Servicios

```php
class NetworkController extends Controller
{
    public function __construct(
        private NetworkService $networkService
    ) {}

    public function store(StoreNetworkRequest $request)
    {
        $network = $this->networkService->createNetwork(
            $request->validated(),
            auth()->user()
        );

        return redirect()
            ->route('networks.show', $network)
            ->with('success', 'Red creada correctamente');
    }
}
```

---

## 🧩 PATRÓN: BLADE COMPONENTS

### Problema
Vistas con código HTML repetido y poco mantenible.

### Solución
Componentes Blade reutilizables con lógica PHP opcional.

### Tipos de Componentes

#### 1. Componentes Anónimos (solo vista)

**Ubicación**: `resources/views/components/`

```blade
{{-- resources/views/components/alert.blade.php --}}
@props(['type' => 'info'])

<div {{ $attributes->merge(['class' => "alert alert-{$type}"]) }}>
    {{ $slot }}
</div>

{{-- Uso --}}
<x-alert type="success">
    Reseña creada correctamente
</x-alert>
```

#### 2. Componentes con Clase (con lógica)

**Clase**: `app/View/Components/`
**Vista**: `resources/views/components/`

```php
// app/View/Components/ReviewCard.php
namespace App\View\Components;

use App\Models\Review;
use Illuminate\View\Component;

class ReviewCard extends Component
{
    public function __construct(
        public Review $review
    ) {}

    public function averageRating(): float
    {
        return round($this->review->rating, 1);
    }

    public function render()
    {
        return view('components.review-card');
    }
}
```

```blade
{{-- resources/views/components/review-card.blade.php --}}
<div class="review-card">
    <h3>{{ $review->restaurant->name }}</h3>
    <div class="rating">
        <x-rating-stars :rating="$averageRating()" />
    </div>
    <p>{{ $review->comment }}</p>
</div>

{{-- Uso --}}
<x-review-card :review="$review" />
```

---

## 📊 PATRÓN: EAGER LOADING

### Problema
Queries N+1 causan problemas de performance.

### Solución
Eager Loading en relaciones.

```php
// ❌ N+1 Problem
$reviews = Review::all();
foreach ($reviews as $review) {
    echo $review->user->name;        // Query
    echo $review->restaurant->name;  // Query
}
// Total: 1 + N + N queries

// ✅ Eager Loading
$reviews = Review::with(['user', 'restaurant'])->get();
foreach ($reviews as $review) {
    echo $review->user->name;
    echo $review->restaurant->name;
}
// Total: 3 queries

// ✅ Relaciones anidadas
$networks = Network::with([
    'members',
    'reviews.user',
    'reviews.restaurant',
    'reviews.media'
])->get();
```

### Cargar relaciones condicionalmente

```php
$reviews = Review::query()
    ->with(['user', 'restaurant'])
    ->when($includeComments, fn($q) => $q->with('comments'))
    ->get();
```

---

## 🔑 PATRÓN: POLÍTICAS DE AUTORIZACIÓN

### Problema
Verificar permisos manualmente en cada controller.

### Solución
Policies centralizadas.

### Implementación

```php
// app/Policies/NetworkPolicy.php
class NetworkPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Cualquiera puede ver lista de sus redes
    }

    public function view(User $user, Network $network): bool
    {
        return $user->networks()->where('networks.id', $network->id)->exists();
    }

    public function create(User $user): bool
    {
        return true; // Cualquiera puede crear redes
    }

    public function update(User $user, Network $network): bool
    {
        return $user->networks()
            ->wherePivot('role', 'owner')
            ->where('networks.id', $network->id)
            ->exists();
    }

    public function delete(User $user, Network $network): bool
    {
        return $this->update($user, $network);
    }

    public function inviteMembers(User $user, Network $network): bool
    {
        return $user->networks()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->where('networks.id', $network->id)
            ->exists();
    }
}
```

### Uso en Controllers

```php
class NetworkController extends Controller
{
    public function update(Request $request, Network $network)
    {
        $this->authorize('update', $network);

        // Si llega aquí, tiene permiso
        $network->update($request->validated());

        return redirect()->back();
    }
}
```

### Uso en Blade

```blade
@can('update', $network)
    <a href="{{ route('networks.edit', $network) }}">Editar</a>
@endcan

@can('delete', $network)
    <form method="POST" action="{{ route('networks.destroy', $network) }}">
        @csrf
        @method('DELETE')
        <button>Eliminar</button>
    </form>
@endcan
```

---

## 📝 PATRÓN: FORM REQUESTS

### Problema
Validaciones dispersas en controllers.

### Solución
FormRequests dedicados.

```php
// app/Http/Requests/StoreReviewRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificar que el usuario pertenece a la red
        return $this->user()
            ->networks()
            ->where('networks.id', $this->route('network')->id)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'date_of_visit' => 'required|date|before_or_equal:today',
            'meal_type' => 'nullable|in:breakfast,lunch,dinner',
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:5120',
            'ticket' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'La calificación es obligatoria',
            'rating.min' => 'La calificación mínima es 1',
            'rating.max' => 'La calificación máxima es 5',
            'photos.max' => 'Máximo 10 fotos por reseña',
            'photos.*.max' => 'Cada foto debe pesar máximo 5MB',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Preparar datos antes de validar
        $this->merge([
            'date_of_visit' => $this->date_of_visit ?? now()->toDateString(),
        ]);
    }
}
```

---

## 🗄️ PATRÓN: REPOSITORY (Opcional)

Para proyectos que crezcan mucho, considerar patrón Repository.

```php
// app/Repositories/ReviewRepository.php
interface ReviewRepositoryInterface
{
    public function findByNetwork(Network $network): Collection;
    public function findWithFilters(array $filters): Collection;
}

class ReviewRepository implements ReviewRepositoryInterface
{
    public function findByNetwork(Network $network): Collection
    {
        return Review::where('network_id', $network->id)
            ->with(['user', 'restaurant', 'media'])
            ->latest()
            ->get();
    }

    public function findWithFilters(array $filters): Collection
    {
        return Review::query()
            ->when($filters['rating'] ?? null, fn($q, $rating) =>
                $q->where('rating', '>=', $rating)
            )
            ->when($filters['cuisine'] ?? null, fn($q, $cuisine) =>
                $q->whereHas('restaurant', fn($q) =>
                    $q->where('cuisine_type', $cuisine)
                )
            )
            ->get();
    }
}
```

---

## 🎯 DECISIONES ARQUITECTÓNICAS

### ¿Por qué Laravel 11?
- Framework maduro y estable
- Ecosistema rico (Spatie, etc.)
- Documentación excelente
- Comunidad activa
- Facilita seguir buenas prácticas

### ¿Por qué Blade sobre Vue/React?
- Menor complejidad
- SSR nativo
- Mejor SEO
- Más rápido de desarrollar
- Sistema de skins más simple
- Livewire para interactividad donde se necesite

### ¿Por qué Services sobre Repositories?
- Más simple para el tamaño del proyecto
- Eloquent ya es un buen ORM
- Repositories agregan complejidad innecesaria en proyectos medianos

### ¿Por qué Spatie MediaLibrary?
- Manejo robusto de archivos
- Conversiones automáticas
- Colecciones de medios
- Bien mantenido

### ¿Por qué sistema de Skins personalizado?
- Laravel no tiene soporte nativo multi-tema
- Paquetes de terceros agregan dependencias
- Solución custom es simple y efectiva
- Control total sobre implementación

---

## ⚠️ ANTI-PATRONES A EVITAR

### ❌ Fat Controllers
```php
// NO
class ReviewController
{
    public function store(Request $request)
    {
        // 200 líneas de código
    }
}
```

### ❌ Dios Models
```php
// NO
class User extends Model
{
    public function createReview() { }
    public function inviteToNetwork() { }
    public function calculateStats() { }
    public function sendEmail() { }
    // 50 métodos más
}
```

### ❌ Lógica en Vistas
```blade
<!-- NO -->
@php
    $avgRating = $reviews->avg('rating');
    $filtered = $reviews->filter(function($r) {
        return $r->rating >= 4;
    });
@endphp
```

### ❌ Hardcoding
```php
// NO
if ($user->email === 'admin@example.com') { }
if ($network->id === 1) { }
```

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
