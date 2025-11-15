# ⚙️ SERVICIOS (SERVICES)
## Lógica de Negocio Centralizada

---

## 🎯 PROPÓSITO

Este documento describe:
- Todos los servicios disponibles
- Métodos públicos de cada servicio
- Parámetros y retornos
- Ejemplos de uso

---

## 📋 LISTA DE SERVICIOS

### 1. NetworkService

**Ubicación**: `app/Services/NetworkService.php`

**Responsabilidades**:
- Crear redes
- Gestionar miembros
- Invitar usuarios
- Obtener estadísticas

**Métodos**:

```php
class NetworkService
{
    /**
     * Crea una nueva red
     *
     * @param array $data Datos de la red
     * @param User $owner Usuario creador
     * @return Network
     */
    public function createNetwork(array $data, User $owner): Network;

    /**
     * Invita a un miembro a la red
     *
     * @param Network $network Red destino
     * @param string $email Email del invitado
     * @param string $role Rol (admin|member)
     * @param User $sender Usuario que invita
     * @return Invitation
     */
    public function inviteMember(
        Network $network,
        string $email,
        string $role,
        User $sender
    ): Invitation;

    /**
     * Acepta una invitación
     *
     * @param string $token Token de invitación
     * @param User $user Usuario que acepta
     * @return Membership
     */
    public function acceptInvitation(string $token, User $user): Membership;

    /**
     * Remueve un miembro de la red
     *
     * @param Network $network
     * @param User $member Usuario a remover
     * @return bool
     */
    public function removeMember(Network $network, User $member): bool;

    /**
     * Obtiene estadísticas de la red
     *
     * @param Network $network
     * @return array
     */
    public function getNetworkStats(Network $network): array;

    /**
     * Obtiene actividad reciente de la red
     *
     * @param Network $network
     * @param int $limit
     * @return Collection
     */
    public function getRecentActivity(Network $network, int $limit = 10): Collection;
}
```

**Ejemplo de uso**:

```php
// En NetworkController
public function store(StoreNetworkRequest $request, NetworkService $service)
{
    $network = $service->createNetwork(
        $request->validated(),
        auth()->user()
    );

    return redirect()->route('networks.show', $network);
}

public function invite(InviteMemberRequest $request, Network $network, NetworkService $service)
{
    $invitation = $service->inviteMember(
        $network,
        $request->email,
        $request->role,
        auth()->user()
    );

    return back()->with('success', 'Invitación enviada');
}
```

---

### 2. ReviewService

**Ubicación**: `app/Services/ReviewService.php`

**Responsabilidades**:
- Crear reseñas
- Adjuntar medios (fotos, tickets)
- Actualizar reseñas
- Eliminar reseñas

**Métodos**:

```php
class ReviewService
{
    /**
     * Crea una nueva reseña
     *
     * @param Network $network
     * @param array $data Datos de la reseña
     * @param User $user Usuario autor
     * @return Review
     */
    public function createReview(Network $network, array $data, User $user): Review;

    /**
     * Actualiza una reseña existente
     *
     * @param Review $review
     * @param array $data
     * @return Review
     */
    public function updateReview(Review $review, array $data): Review;

    /**
     * Adjunta fotos a la reseña
     *
     * @param Review $review
     * @param array $photos Array de UploadedFile
     * @return void
     */
    public function attachPhotos(Review $review, array $photos): void;

    /**
     * Adjunta ticket a la reseña
     *
     * @param Review $review
     * @param UploadedFile $ticket
     * @return void
     */
    public function attachTicket(Review $review, UploadedFile $ticket): void;

    /**
     * Elimina una reseña
     *
     * @param Review $review
     * @return bool
     */
    public function deleteReview(Review $review): bool;

    /**
     * Obtiene reseñas filtradas
     *
     * @param Network $network
     * @param array $filters
     * @return Collection
     */
    public function getFilteredReviews(Network $network, array $filters): Collection;
}
```

**Ejemplo de uso**:

```php
// En ReviewController
public function store(
    StoreReviewRequest $request,
    Network $network,
    ReviewService $service
) {
    $review = $service->createReview(
        $network,
        $request->validated(),
        auth()->user()
    );

    return redirect()
        ->route('reviews.show', $review)
        ->with('success', 'Reseña creada correctamente');
}
```

---

### 3. RestaurantService

**Ubicación**: `app/Services/RestaurantService.php`

**Responsabilidades**:
- Buscar restaurantes
- Crear restaurantes con detección de duplicados
- Autocompletado
- Obtener datos geográficos

**Dependencias**:
- `RestaurantNormalizationService`
- `DuplicateDetectionService`

**Métodos**:

```php
class RestaurantService
{
    public function __construct(
        private RestaurantNormalizationService $normalization,
        private DuplicateDetectionService $duplicateDetection
    ) {}

    /**
     * Busca restaurantes por término
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchRestaurants(string $query, int $limit = 10): Collection;

    /**
     * Autocompletado de restaurantes (usa campos normalizados)
     *
     * @param string $query
     * @return array
     */
    public function autocomplete(string $query): array;

    /**
     * Crea un nuevo restaurante con detección de duplicados
     *
     * IMPORTANTE: Puede retornar Restaurant O array con candidatos duplicados
     *
     * @param array $data
     * @return Restaurant|array Si hay duplicados: ['duplicates_found' => true, 'candidates' => ...]
     */
    public function createRestaurant(array $data): Restaurant|array;

    /**
     * Fuerza la creación sin verificar duplicados
     * (después de que el usuario confirme que no es duplicado)
     *
     * @param array $data
     * @return Restaurant
     */
    public function forceCreateRestaurant(array $data): Restaurant;

    /**
     * Obtiene coordenadas de una dirección (geocoding)
     *
     * @param string $address
     * @return array ['lat' => float, 'lng' => float]
     */
    public function geocodeAddress(string $address): array;

    /**
     * Obtiene restaurantes en un área (usa Haversine)
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius Km
     * @return Collection
     */
    public function getRestaurantsNearby(float $lat, float $lng, float $radius): Collection;
}
```

**Ejemplo de uso con duplicados**:

```php
// En RestaurantController
public function store(StoreRestaurantRequest $request, RestaurantService $service)
{
    $result = $service->createRestaurant($request->validated());

    // Verificar si encontró duplicados
    if (is_array($result) && $result['duplicates_found']) {
        return view('restaurants.duplicate-check', [
            'candidates' => $result['candidates'],
            'originalData' => $result['original_data']
        ]);
    }

    // No hay duplicados, restaurante creado
    return redirect()
        ->route('restaurants.show', $result)
        ->with('success', 'Restaurante creado');
}

// Si el usuario confirma que NO es duplicado
public function confirmCreate(Request $request, RestaurantService $service)
{
    $restaurant = $service->forceCreateRestaurant($request->all());

    return redirect()
        ->route('restaurants.show', $restaurant)
        ->with('success', 'Restaurante creado');
}
```

---

### 4. MapService

**Ubicación**: `app/Services/MapService.php`

**Responsabilidades**:
- Generar datos para mapas
- Filtrar marcadores
- Agrupar marcadores

**Métodos**:

```php
class MapService
{
    /**
     * Obtiene datos de mapa para una red
     *
     * @param Network $network
     * @return array
     */
    public function getNetworkMapData(Network $network): array;

    /**
     * Filtra restaurantes por bounds del mapa
     *
     * @param float $minLat
     * @param float $maxLat
     * @param float $minLng
     * @param float $maxLng
     * @return Collection
     */
    public function filterByBounds(
        float $minLat,
        float $maxLat,
        float $minLng,
        float $maxLng
    ): Collection;

    /**
     * Agrupa marcadores cercanos
     *
     * @param Collection $restaurants
     * @param int $zoomLevel
     * @return array
     */
    public function clusterMarkers(Collection $restaurants, int $zoomLevel): array;
}
```

---

### 5. MediaService

**Ubicación**: `app/Services/MediaService.php`

**Responsabilidades**:
- Subir archivos
- Generar thumbnails
- Validar archivos
- Eliminar archivos

**Métodos**:

```php
class MediaService
{
    /**
     * Adjunta medios a un modelo
     *
     * @param Model $model
     * @param array $files
     * @param string $collection
     * @return void
     */
    public function attachMedia(Model $model, array $files, string $collection): void;

    /**
     * Valida archivo antes de subir
     *
     * @param UploadedFile $file
     * @param array $rules
     * @return bool
     */
    public function validateFile(UploadedFile $file, array $rules): bool;

    /**
     * Elimina medios de un modelo
     *
     * @param Model $model
     * @param string $collection
     * @return void
     */
    public function clearMediaCollection(Model $model, string $collection): void;
}
```

---

### 6. NotificationService

**Ubicación**: `app/Services/NotificationService.php`

**Responsabilidades**:
- Enviar notificaciones
- Notificar nuevos reviews
- Notificar invitaciones
- Notificar comentarios

**Métodos**:

```php
class NotificationService
{
    /**
     * Notifica nueva reseña a miembros
     *
     * @param Review $review
     * @return void
     */
    public function notifyNewReview(Review $review): void;

    /**
     * Notifica nueva invitación
     *
     * @param Invitation $invitation
     * @return void
     */
    public function notifyInvitation(Invitation $invitation): void;

    /**
     * Notifica nuevo comentario
     *
     * @param ReviewComment $comment
     * @return void
     */
    public function notifyNewComment(ReviewComment $comment): void;

    /**
     * Notifica miembros de la red
     *
     * @param Network $network
     * @param string $message
     * @return void
     */
    public function notifyNetworkMembers(Network $network, string $message): void;
}
```

---

### 7. VisitWishService

**Ubicación**: `app/Services/VisitWishService.php`

**Responsabilidades**:
- Añadir a "por visitar"
- Convertir a reseña
- Gestionar lista

**Métodos**:

```php
class VisitWishService
{
    /**
     * Añade restaurante a lista de deseos
     *
     * @param Network $network
     * @param Restaurant $restaurant
     * @param User $user
     * @param array $data
     * @return VisitWish
     */
    public function addToWishlist(
        Network $network,
        Restaurant $restaurant,
        User $user,
        array $data = []
    ): VisitWish;

    /**
     * Convierte wish en reseña
     *
     * @param VisitWish $wish
     * @param array $reviewData
     * @return Review
     */
    public function convertToReview(VisitWish $wish, array $reviewData): Review;

    /**
     * Obtiene wishes del usuario
     *
     * @param User $user
     * @param Network|null $network
     * @return Collection
     */
    public function getUserWishes(User $user, ?Network $network = null): Collection;

    /**
     * Elimina de wishlist
     *
     * @param VisitWish $wish
     * @return bool
     */
    public function removeFromWishlist(VisitWish $wish): bool;
}
```

---

### 8. RestaurantNormalizationService

**Ubicación**: `app/Services/RestaurantNormalizationService.php`

**Responsabilidades**:
- Normalizar nombres de restaurantes
- Normalizar direcciones
- Eliminar acentos y caracteres especiales
- Preparar datos para búsquedas y comparaciones

**Métodos**:

```php
class RestaurantNormalizationService
{
    /**
     * Normaliza el nombre de un restaurante
     *
     * Proceso:
     * 1. Convierte a minúsculas
     * 2. Elimina acentos (á → a, ñ → n)
     * 3. Elimina caracteres especiales
     * 4. Normaliza espacios múltiples a uno
     * 5. Trim
     *
     * @param string $name
     * @return string
     *
     * @example "El Bullí  Restaurant" → "el bulli restaurant"
     */
    public function normalizeName(string $name): string;

    /**
     * Normaliza la dirección
     *
     * Proceso:
     * 1. Convierte a minúsculas
     * 2. Normaliza espacios
     * 3. Trim
     *
     * @param string $address
     * @return string
     *
     * @example "  Calle Mayor, 123  " → "calle mayor 123"
     */
    public function normalizeAddress(string $address): string;

    /**
     * Elimina acentos de un string
     *
     * @param string $string
     * @return string
     */
    private function removeAccents(string $string): string;
}
```

**Ejemplo de uso**:

```php
$service = app(RestaurantNormalizationService::class);

$normalized = $service->normalizeName("El Bullí");
// "el bulli"

$normalizedAddress = $service->normalizeAddress("  Av. Diagonal, 123  ");
// "av diagonal 123"
```

**Integración con Observer**:

```php
// app/Observers/RestaurantObserver.php
class RestaurantObserver
{
    public function __construct(
        private RestaurantNormalizationService $normalizationService
    ) {}

    public function saving(Restaurant $restaurant): void
    {
        // Normalización automática al guardar
        if ($restaurant->isDirty('name')) {
            $restaurant->name_normalized =
                $this->normalizationService->normalizeName($restaurant->name);
        }

        if ($restaurant->isDirty('address')) {
            $restaurant->address_normalized =
                $this->normalizationService->normalizeAddress($restaurant->address);
        }
    }
}
```

---

### 9. DuplicateDetectionService

**Ubicación**: `app/Services/DuplicateDetectionService.php`

**Responsabilidades**:
- Detectar restaurantes duplicados
- Calcular scores de similitud
- Usar múltiples métodos de detección
- Registrar candidatos en BD

**Métodos**:

```php
class DuplicateDetectionService
{
    /**
     * Encuentra restaurantes similares
     *
     * Usa 3 métodos:
     * 1. Nombre normalizado exacto (score 100)
     * 2. Levenshtein distance (score basado en distancia)
     * 3. Proximidad geográfica + nombre similar
     *
     * @param array $data Debe incluir name_normalized y opcionalmente lat/lng
     * @return Collection Items con ['restaurant' => Restaurant, 'score' => int, 'method' => string]
     */
    public function findSimilarRestaurants(array $data): Collection;

    /**
     * Calcula similitud entre dos nombres
     *
     * @param string $name1
     * @param string $name2
     * @return int Score 0-100
     */
    public function calculateNameSimilarity(string $name1, string $name2): int;

    /**
     * Encuentra restaurantes cercanos usando Haversine
     *
     * @param float $lat
     * @param float $lng
     * @param float $radiusKm
     * @return Collection
     */
    private function findNearbyRestaurants(float $lat, float $lng, float $radiusKm): Collection;

    /**
     * Registra candidato de duplicado en BD
     *
     * @param Restaurant $restaurantA
     * @param Restaurant $restaurantB
     * @param int $score
     * @param string $method
     * @return DuplicateRestaurantCandidate
     */
    public function registerCandidate(
        Restaurant $restaurantA,
        Restaurant $restaurantB,
        int $score,
        string $method
    ): DuplicateRestaurantCandidate;
}
```

**Ejemplo de uso**:

```php
$service = app(DuplicateDetectionService::class);

$data = [
    'name' => 'El Bulli',
    'name_normalized' => 'el bulli',
    'latitude' => 42.2486,
    'longitude' => 3.2311
];

$duplicates = $service->findSimilarRestaurants($data);

foreach ($duplicates as $item) {
    echo "{$item['restaurant']->name}: {$item['score']}% ({$item['method']})\n";
}

// Output:
// El Bullí: 100% (exact_name)
// El Bulli Restaurant: 90% (similar_name)
// El Buli: 85% (similar_name)
```

**Thresholds recomendados**:
- **Score >= 95%**: Casi seguro duplicado
- **Score 80-94%**: Muy probable duplicado, revisar
- **Score < 80%**: Probablemente no es duplicado

---

### 10. RestaurantMergeService

**Ubicación**: `app/Services/RestaurantMergeService.php`

**Responsabilidades**:
- Fusionar restaurantes duplicados
- Migrar reviews y visit_wishes
- Mantener trazabilidad
- Solo accesible para Super Admin

**Métodos**:

```php
class RestaurantMergeService
{
    /**
     * Fusiona dos restaurantes
     *
     * Proceso:
     * 1. Migrar todas las reviews de $remove a $keep
     * 2. Migrar todos los visit_wishes de $remove a $keep
     * 3. Actualizar candidatos de duplicados
     * 4. Soft delete del restaurante removido
     *
     * IMPORTANTE: Usa transacción, todo o nada
     *
     * @param Restaurant $keep Restaurante que se mantiene
     * @param Restaurant $remove Restaurante que se elimina
     * @return void
     * @throws \Exception Si falla alguna operación
     */
    public function mergeRestaurants(Restaurant $keep, Restaurant $remove): void;

    /**
     * Valida que la fusión es segura
     *
     * @param Restaurant $keep
     * @param Restaurant $remove
     * @return bool
     */
    public function canMerge(Restaurant $keep, Restaurant $remove): bool;

    /**
     * Obtiene preview de la fusión
     *
     * @param Restaurant $keep
     * @param Restaurant $remove
     * @return array Estadísticas de lo que se migrará
     */
    public function getMergePreview(Restaurant $keep, Restaurant $remove): array;

    /**
     * Revierte una fusión (si es posible)
     *
     * @param Restaurant $restaurant Restaurante que fue eliminado
     * @return Restaurant
     */
    public function revertMerge(Restaurant $restaurant): Restaurant;
}
```

**Ejemplo de uso (Controller de Super Admin)**:

```php
// app/Http/Controllers/Admin/RestaurantMergeController.php
class RestaurantMergeController extends Controller
{
    public function preview(
        Restaurant $keep,
        Restaurant $remove,
        RestaurantMergeService $service
    ) {
        $preview = $service->getMergePreview($keep, $remove);

        return view('admin.restaurants.merge-preview', [
            'keep' => $keep,
            'remove' => $remove,
            'preview' => $preview
        ]);
    }

    public function merge(
        Restaurant $keep,
        Restaurant $remove,
        RestaurantMergeService $service
    ) {
        // Verificar que el usuario es super admin
        $this->authorize('merge-restaurants');

        try {
            $service->mergeRestaurants($keep, $remove);

            return redirect()
                ->route('admin.restaurants.index')
                ->with('success', 'Restaurantes fusionados correctamente');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al fusionar: ' . $e->getMessage());
        }
    }
}
```

**Preview response example**:

```php
[
    'reviews_to_migrate' => 15,
    'visit_wishes_to_migrate' => 3,
    'total_reviews_after' => 42, // $keep->reviews->count() + 15
    'networks_affected' => [1, 3, 5, 8],
    'safe_to_merge' => true
]
```

---

## 🔧 INYECCIÓN DE DEPENDENCIAS

Todos los servicios se inyectan en controllers:

```php
class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
        private RestaurantService $restaurantService,
        private NotificationService $notificationService
    ) {}

    public function store(StoreReviewRequest $request, Network $network)
    {
        $review = $this->reviewService->createReview(
            $network,
            $request->validated(),
            auth()->user()
        );

        $this->notificationService->notifyNewReview($review);

        return redirect()->route('reviews.show', $review);
    }
}
```

---

## 📝 PATRÓN GENERAL DE SERVICIOS

Todos los servicios siguen este patrón:

```php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class ExampleService
{
    // Inyección de dependencias
    public function __construct(
        private OtherService $otherService
    ) {}

    /**
     * Método público documentado
     *
     * @param Type $param
     * @return Type
     * @throws Exception
     */
    public function publicMethod($param): Type
    {
        // Usar transacciones para operaciones críticas
        DB::beginTransaction();
        try {
            // Lógica de negocio
            $result = $this->processData($param);

            // Más operaciones
            $this->otherService->doSomething($result);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            // Log del error
            logger()->error('Error in publicMethod', [
                'param' => $param,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Método privado helper
     */
    private function processData($data): Type
    {
        // Lógica auxiliar
    }
}
```

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
