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
- Crear restaurantes
- Autocompletado
- Obtener datos geográficos

**Métodos**:

```php
class RestaurantService
{
    /**
     * Busca restaurantes por término
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchRestaurants(string $query, int $limit = 10): Collection;

    /**
     * Autocompletado de restaurantes
     *
     * @param string $query
     * @return array
     */
    public function autocomplete(string $query): array;

    /**
     * Crea un nuevo restaurante
     *
     * @param array $data
     * @return Restaurant
     */
    public function createRestaurant(array $data): Restaurant;

    /**
     * Obtiene coordenadas de una dirección
     *
     * @param string $address
     * @return array ['lat' => float, 'lng' => float]
     */
    public function geocodeAddress(string $address): array;

    /**
     * Obtiene restaurantes en un área
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius Km
     * @return Collection
     */
    public function getRestaurantsNearby(float $lat, float $lng, float $radius): Collection;
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
