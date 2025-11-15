# 👨‍💻 GUÍA DE DESARROLLO
## Paso a Paso para Desarrollar Cada Funcionalidad

---

## 🎯 PROPÓSITO

Este documento te guía paso a paso en el desarrollo de cada funcionalidad siguiendo las mejores prácticas del proyecto.

---

## 📋 ANTES DE EMPEZAR

### Checklist Inicial

- [ ] He leído `AI_HANDOFF.md`
- [ ] He leído `PROJECT_STRUCTURE.md`
- [ ] Entiendo en qué fase estamos
- [ ] Tengo el entorno configurado
- [ ] Entiendo qué se va a implementar

### Entorno de Desarrollo

```bash
# Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL/MariaDB
- Git

# Instalar Laravel (si es nuevo proyecto)
composer create-project laravel/laravel reviews
cd reviews

# Dependencias base
composer require spatie/laravel-medialibrary
composer require spatie/laravel-permission
composer require laravel/breeze --dev

# Frontend
npm install
```

---

## 🔧 PATRÓN GENERAL: CREAR UNA FUNCIONALIDAD

Para cada nueva funcionalidad sigue estos pasos EN ORDEN:

### 1. MIGRACIÓN (Base de Datos)

```bash
php artisan make:migration create_reviews_table
```

```php
// database/migrations/XXXX_create_reviews_table.php
public function up(): void
{
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('network_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
        $table->tinyInteger('rating');
        $table->text('comment')->nullable();
        $table->date('date_of_visit');
        $table->timestamps();
        $table->softDeletes();

        $table->index('network_id');
        $table->index('rating');
    });
}
```

```bash
php artisan migrate
```

---

### 2. MODELO

```bash
php artisan make:model Review
```

```php
// app/Models/Review.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'network_id',
        'user_id',
        'restaurant_id',
        'rating',
        'comment',
        'date_of_visit',
    ];

    protected $casts = [
        'rating' => 'integer',
        'date_of_visit' => 'date',
    ];

    // Relaciones
    public function network(): BelongsTo
    {
        return $this->belongsTo(Network::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    // Media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);

        $this->addMediaCollection('ticket')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }

    // Scopes
    public function scopeByNetwork($query, Network $network)
    {
        return $query->where('network_id', $network->id);
    }

    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }
}
```

---

### 3. POLICY

```bash
php artisan make:policy ReviewPolicy --model=Review
```

```php
// app/Policies/ReviewPolicy.php
namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        // Usuario debe pertenecer a la red
        return $user->networks()
            ->where('networks.id', $review->network_id)
            ->exists();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Review $review): bool
    {
        // Solo el autor puede editar
        return $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        // Autor o admin de la red
        return $review->user_id === $user->id
            || $user->networks()
                ->wherePivotIn('role', ['owner', 'admin'])
                ->where('networks.id', $review->network_id)
                ->exists();
    }
}
```

Registrar en `AuthServiceProvider`:

```php
// app/Providers/AuthServiceProvider.php
protected $policies = [
    Review::class => ReviewPolicy::class,
];
```

---

### 4. FORM REQUESTS

```bash
php artisan make:request StoreReviewRequest
php artisan make:request UpdateReviewRequest
```

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
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:5120',
            'ticket' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'La calificación es obligatoria',
            'rating.min' => 'La calificación mínima es 1',
            'rating.max' => 'La calificación máxima es 5',
        ];
    }
}
```

---

### 5. SERVICE

```bash
php artisan make:class Services/ReviewService
```

```php
// app/Services/ReviewService.php
namespace App\Services;

use App\Models\Network;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function __construct(
        private MediaService $mediaService,
        private NotificationService $notificationService
    ) {}

    /**
     * Crea una nueva reseña
     */
    public function createReview(
        Network $network,
        array $data,
        User $user
    ): Review {
        DB::beginTransaction();
        try {
            // Crear reseña
            $review = Review::create([
                'network_id' => $network->id,
                'user_id' => $user->id,
                'restaurant_id' => $data['restaurant_id'],
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'date_of_visit' => $data['date_of_visit'],
            ]);

            // Adjuntar fotos si existen
            if (isset($data['photos'])) {
                foreach ($data['photos'] as $photo) {
                    $review->addMedia($photo)
                        ->toMediaCollection('gallery');
                }
            }

            // Adjuntar ticket si existe
            if (isset($data['ticket'])) {
                $review->addMedia($data['ticket'])
                    ->toMediaCollection('ticket');
            }

            // Notificar a miembros de la red
            $this->notificationService->notifyNewReview($review);

            DB::commit();
            return $review->fresh(['user', 'restaurant', 'media']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Error creating review', [
                'network_id' => $network->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Actualiza una reseña
     */
    public function updateReview(Review $review, array $data): Review
    {
        $review->update([
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'date_of_visit' => $data['date_of_visit'],
        ]);

        // Actualizar fotos si se enviaron nuevas
        if (isset($data['photos'])) {
            $review->clearMediaCollection('gallery');
            foreach ($data['photos'] as $photo) {
                $review->addMedia($photo)->toMediaCollection('gallery');
            }
        }

        return $review->fresh();
    }

    /**
     * Elimina una reseña
     */
    public function deleteReview(Review $review): bool
    {
        return $review->delete();
    }
}
```

---

### 6. CONTROLLER

```bash
php artisan make:controller ReviewController --resource
```

```php
// app/Http/Controllers/ReviewController.php
namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Network;
use App\Models\Review;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * Lista de reseñas de la red
     */
    public function index(Network $network)
    {
        $this->authorize('view', $network);

        $reviews = $network->reviews()
            ->with(['user', 'restaurant', 'media'])
            ->latest()
            ->paginate(20);

        return view(skin('pages.review.index'), compact('network', 'reviews'));
    }

    /**
     * Formulario de creación
     */
    public function create(Network $network)
    {
        $this->authorize('view', $network);

        return view(skin('pages.review.create'), compact('network'));
    }

    /**
     * Guardar nueva reseña
     */
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

    /**
     * Ver detalle de reseña
     */
    public function show(Review $review)
    {
        $this->authorize('view', $review);

        $review->load(['user', 'restaurant', 'media', 'comments.user']);

        return view(skin('pages.review.show'), compact('review'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view(skin('pages.review.edit'), compact('review'));
    }

    /**
     * Actualizar reseña
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $review = $this->reviewService->updateReview(
            $review,
            $request->validated()
        );

        return redirect()
            ->route('reviews.show', $review)
            ->with('success', 'Reseña actualizada');
    }

    /**
     * Eliminar reseña
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $networkId = $review->network_id;
        $this->reviewService->deleteReview($review);

        return redirect()
            ->route('networks.reviews.index', $networkId)
            ->with('success', 'Reseña eliminada');
    }
}
```

---

### 7. RUTAS

```php
// routes/web.php

Route::middleware('auth')->group(function () {

    Route::prefix('networks/{network}')->group(function () {

        // Reviews dentro de una red
        Route::resource('reviews', ReviewController::class)
            ->except(['index']);

        Route::get('/reviews', [ReviewController::class, 'index'])
            ->name('networks.reviews.index');
    });

    // Ver review individual (fuera de contexto de red)
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])
        ->name('reviews.show');
});
```

---

### 8. VISTAS (BLADE)

```bash
# Crear estructura de vistas
mkdir -p resources/views/skins/listox/pages/review
```

#### Index (Lista)

```blade
{{-- resources/views/skins/listox/pages/review/index.blade.php --}}
@extends(skin('layouts.app'))

@section('title', 'Reseñas - ' . $network->name)

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Reseñas de {{ $network->name }}</h1>
        <a href="{{ route('networks.reviews.create', $network) }}" class="btn btn-primary">
            <img src="{{ skin_asset('icons/ui/plus.svg') }}" alt="Add">
            Nueva Reseña
        </a>
    </div>

    @if($reviews->isEmpty())
        <div class="empty-state">
            <p>No hay reseñas aún. ¡Sé el primero en añadir una!</p>
        </div>
    @else
        <div class="reviews-grid">
            @foreach($reviews as $review)
                <x-card.review :review="$review" />
            @endforeach
        </div>

        {{ $reviews->links() }}
    @endif
</div>
@endsection
```

#### Create (Formulario)

```blade
{{-- resources/views/skins/listox/pages/review/create.blade.php --}}
@extends(skin('layouts.app'))

@section('title', 'Nueva Reseña')

@section('content')
<div class="container">
    <h1>Nueva Reseña</h1>

    <form action="{{ route('networks.reviews.store', $network) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- Buscar restaurante --}}
        <x-form.restaurant-search name="restaurant_id" required />

        {{-- Rating --}}
        <x-form.rating name="rating" label="Calificación" required />

        {{-- Comentario --}}
        <x-form.textarea
            name="comment"
            label="Tu experiencia"
            placeholder="Cuéntanos sobre tu visita..."
            rows="5"
            required
        />

        {{-- Fecha de visita --}}
        <x-form.input
            type="date"
            name="date_of_visit"
            label="Fecha de visita"
            max="{{ date('Y-m-d') }}"
            required
        />

        {{-- Fotos --}}
        <x-form.gallery-upload name="photos" label="Fotos (máx. 10)" />

        {{-- Ticket --}}
        <x-form.file-upload name="ticket" label="Ticket (opcional)" />

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                Publicar Reseña
            </button>
            <a href="{{ route('networks.reviews.index', $network) }}" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
```

#### Show (Detalle)

```blade
{{-- resources/views/skins/listox/pages/review/show.blade.php --}}
@extends(skin('layouts.app'))

@section('title', $review->restaurant->name)

@section('content')
<article class="review-detail">
    <header class="review-header">
        <div class="user-info">
            <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}">
            <div>
                <h2>{{ $review->user->name }}</h2>
                <time>{{ $review->date_of_visit->format('d/m/Y') }}</time>
            </div>
        </div>

        @can('update', $review)
            <div class="actions">
                <a href="{{ route('reviews.edit', $review) }}">Editar</a>
                <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('¿Eliminar reseña?')">
                        Eliminar
                    </button>
                </form>
            </div>
        @endcan
    </header>

    <div class="review-body">
        <h1>{{ $review->restaurant->name }}</h1>
        <x-rating :rating="$review->rating" />

        <p class="comment">{{ $review->comment }}</p>

        {{-- Galería de fotos --}}
        @if($review->hasMedia('gallery'))
            <div class="photo-gallery">
                @foreach($review->getMedia('gallery') as $photo)
                    <img src="{{ $photo->getUrl() }}" alt="Photo">
                @endforeach
            </div>
        @endif

        {{-- Ticket --}}
        @if($review->hasMedia('ticket'))
            <div class="ticket">
                <h3>Ticket</h3>
                <img src="{{ $review->getFirstMediaUrl('ticket') }}" alt="Ticket">
            </div>
        @endif
    </div>

    {{-- Comentarios --}}
    <section class="comments">
        <h3>Comentarios ({{ $review->comments->count() }})</h3>

        @foreach($review->comments as $comment)
            <x-comment :comment="$comment" />
        @endforeach

        <form action="{{ route('reviews.comments.store', $review) }}" method="POST">
            @csrf
            <x-form.textarea name="body" placeholder="Añade un comentario..." />
            <button type="submit">Comentar</button>
        </form>
    </section>
</article>
@endsection
```

---

### 9. COMPONENTES BLADE

Crear componentes reutilizables:

```bash
php artisan make:component ReviewCard
```

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

    public function render()
    {
        return view(skin('components.cards.review'));
    }
}
```

```blade
{{-- resources/views/skins/listox/components/cards/review.blade.php --}}
<article class="review-card">
    <div class="card-header">
        <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}">
        <div>
            <h4>{{ $review->user->name }}</h4>
            <time>{{ $review->created_at->diffForHumans() }}</time>
        </div>
    </div>

    <div class="card-body">
        <h3>
            <a href="{{ route('restaurants.show', $review->restaurant) }}">
                {{ $review->restaurant->name }}
            </a>
        </h3>

        <x-rating :rating="$review->rating" size="sm" />

        <p>{{ Str::limit($review->comment, 150) }}</p>

        @if($review->hasMedia('gallery'))
            <div class="preview-photos">
                @foreach($review->getMedia('gallery')->take(3) as $photo)
                    <img src="{{ $photo->getUrl('thumb') }}" alt="Photo">
                @endforeach
            </div>
        @endif
    </div>

    <div class="card-footer">
        <a href="{{ route('reviews.show', $review) }}">
            Ver completa
        </a>
    </div>
</article>
```

---

### 10. TESTS

```bash
php artisan make:test ReviewTest
```

```php
// tests/Feature/ReviewTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Network;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_review()
    {
        $user = User::factory()->create();
        $network = Network::factory()->create();
        $restaurant = Restaurant::factory()->create();

        // Añadir usuario a la red
        $network->members()->attach($user, ['role' => 'member']);

        $response = $this->actingAs($user)
            ->post(route('networks.reviews.store', $network), [
                'restaurant_id' => $restaurant->id,
                'rating' => 5,
                'comment' => 'Excelente lugar',
                'date_of_visit' => now()->toDateString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'network_id' => $network->id,
            'user_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'rating' => 5,
        ]);
    }

    public function test_user_cannot_create_review_in_network_they_dont_belong()
    {
        $user = User::factory()->create();
        $network = Network::factory()->create();
        $restaurant = Restaurant::factory()->create();

        // Usuario NO es miembro

        $response = $this->actingAs($user)
            ->post(route('networks.reviews.store', $network), [
                'restaurant_id' => $restaurant->id,
                'rating' => 5,
                'comment' => 'Test',
                'date_of_visit' => now()->toDateString(),
            ]);

        $response->assertForbidden();
    }

    public function test_only_author_can_update_review()
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $network = Network::factory()->create();

        $network->members()->attach([$author->id, $otherUser->id]);

        $review = Review::factory()->create([
            'network_id' => $network->id,
            'user_id' => $author->id,
        ]);

        // Autor puede actualizar
        $response = $this->actingAs($author)
            ->put(route('reviews.update', $review), [
                'rating' => 4,
                'comment' => 'Updated',
                'date_of_visit' => now()->toDateString(),
            ]);

        $response->assertRedirect();

        // Otro usuario NO puede
        $response = $this->actingAs($otherUser)
            ->put(route('reviews.update', $review), [
                'rating' => 1,
                'comment' => 'Hacked',
                'date_of_visit' => now()->toDateString(),
            ]);

        $response->assertForbidden();
    }
}
```

---

## ✅ CHECKLIST FINAL

Antes de considerar completa una funcionalidad:

- [ ] Migración creada y ejecutada
- [ ] Modelo con relaciones
- [ ] Policy con permisos
- [ ] FormRequests con validaciones
- [ ] Service con lógica
- [ ] Controller limpio
- [ ] Rutas registradas
- [ ] Vistas creadas
- [ ] Componentes reutilizables
- [ ] Tests básicos
- [ ] Documentación actualizada
- [ ] Probado manualmente
- [ ] Sin errores en logs

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
