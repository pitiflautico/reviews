# 🗄️ ESQUEMA DE BASE DE DATOS
## Estructura, Relaciones y Migraciones

---

## 🎯 PROPÓSITO

Este documento describe:
- Todas las tablas del sistema
- Relaciones entre modelos
- Campos y tipos de datos
- Índices y constraints
- Migraciones esperadas

---

## 📊 DIAGRAMA GENERAL DE RELACIONES

```
┌─────────────┐
│    Users    │
└──────┬──────┘
       │
       │ many-to-many
       │ (memberships)
       ↓
┌─────────────┐         ┌──────────────┐
│  Networks   │────────→│  Invitations │
└──────┬──────┘         └──────────────┘
       │
       │ one-to-many
       │
       ↓
┌─────────────┐         ┌──────────────┐
│   Reviews   │────────→│    Media     │
└──────┬──────┘         └──────────────┘
       │
       ├──→ one-to-many → ReviewComments
       │
       └──→ many-to-one → Restaurants
                          ↓
                    ┌──────────────┐
                    │ Visit Wishes │
                    └──────────────┘
```

---

## 📋 TABLAS PRINCIPALES

### 1. `users` - Usuarios del Sistema

**Propósito**: Usuarios que pueden crear redes y reseñas

**Campos**:
```sql
CREATE TABLE users (
    id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name                VARCHAR(255) NOT NULL,
    email               VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at   TIMESTAMP NULL,
    password            VARCHAR(255) NOT NULL,
    remember_token      VARCHAR(100) NULL,
    profile_photo_path  VARCHAR(2048) NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL
);

-- Índices
CREATE INDEX idx_users_email ON users(email);
```

**Relaciones**:
- `belongsToMany(Network)` through `memberships`
- `hasMany(Review)`
- `hasMany(ReviewComment)`
- `hasMany(VisitWish)`
- `hasMany(Invitation)` as sender

**Migración**: `2024_01_01_000000_create_users_table.php`

---

### 2. `networks` - Redes Privadas

**Propósito**: Grupos privados de reseñas gastronómicas

**Campos**:
```sql
CREATE TABLE networks (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    description     TEXT NULL,
    is_private      BOOLEAN DEFAULT TRUE,
    settings        JSON NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL  -- Soft deletes
);

-- Índices
CREATE INDEX idx_networks_slug ON networks(slug);
CREATE INDEX idx_networks_deleted_at ON networks(deleted_at);
```

**Settings JSON estructura**:
```json
{
    "allow_comments": true,
    "require_approval": false,
    "visible_to_members": true,
    "max_members": null
}
```

**Relaciones**:
- `belongsToMany(User)` through `memberships`
- `hasMany(Review)`
- `hasMany(VisitWish)`
- `hasMany(Invitation)`

**Migración**: `2024_01_02_000000_create_networks_table.php`

---

### 3. `memberships` - Tabla Pivot Users-Networks

**Propósito**: Relacionar usuarios con redes y definir roles

**Campos**:
```sql
CREATE TABLE memberships (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    network_id      BIGINT UNSIGNED NOT NULL,
    role            ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at       TIMESTAMP NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_network (user_id, network_id)
);

-- Índices
CREATE INDEX idx_memberships_user ON memberships(user_id);
CREATE INDEX idx_memberships_network ON memberships(network_id);
CREATE INDEX idx_memberships_role ON memberships(role);
```

**Roles**:
- `owner`: Creador de la red, todos los permisos
- `admin`: Puede invitar, expulsar, moderar
- `member`: Puede ver, crear reseñas, comentar

**Migración**: `2024_01_02_100000_create_memberships_table.php`

---

### 4. `invitations` - Invitaciones a Redes

**Propósito**: Invitar nuevos miembros a redes

**Campos**:
```sql
CREATE TABLE invitations (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    network_id      BIGINT UNSIGNED NOT NULL,
    sender_id       BIGINT UNSIGNED NOT NULL,
    email           VARCHAR(255) NOT NULL,
    role            ENUM('admin', 'member') DEFAULT 'member',
    token           VARCHAR(64) NOT NULL UNIQUE,
    status          ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    expires_at      TIMESTAMP NOT NULL,
    accepted_at     TIMESTAMP NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Índices
CREATE INDEX idx_invitations_email ON invitations(email);
CREATE INDEX idx_invitations_token ON invitations(token);
CREATE INDEX idx_invitations_status ON invitations(status);
CREATE INDEX idx_invitations_expires_at ON invitations(expires_at);
```

**Migración**: `2024_01_02_200000_create_invitations_table.php`

---

### 5. `restaurants` - Restaurantes

**Propósito**: Lugares que se pueden reseñar

**Campos**:
```sql
CREATE TABLE restaurants (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL,
    address         VARCHAR(500) NULL,
    city            VARCHAR(255) NULL,
    country         VARCHAR(255) NULL,
    postal_code     VARCHAR(20) NULL,
    latitude        DECIMAL(10, 7) NULL,
    longitude       DECIMAL(10, 7) NULL,
    cuisine_type    VARCHAR(100) NULL,
    price_range     ENUM('$', '$$', '$$$', '$$$$') NULL,
    phone           VARCHAR(50) NULL,
    website         VARCHAR(500) NULL,
    external_urls   JSON NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL
);

-- Índices
CREATE INDEX idx_restaurants_name ON restaurants(name);
CREATE INDEX idx_restaurants_slug ON restaurants(slug);
CREATE INDEX idx_restaurants_city ON restaurants(city);
CREATE INDEX idx_restaurants_cuisine ON restaurants(cuisine_type);
CREATE INDEX idx_restaurants_location ON restaurants(latitude, longitude);
CREATE FULLTEXT INDEX ft_restaurants_search ON restaurants(name, address, city);
```

**External URLs JSON**:
```json
{
    "google_maps": "https://...",
    "tripadvisor": "https://...",
    "instagram": "https://..."
}
```

**Relaciones**:
- `hasMany(Review)`
- `hasMany(VisitWish)`

**Migración**: `2024_01_03_000000_create_restaurants_table.php`

---

### 6. `reviews` - Reseñas

**Propósito**: Reseñas de restaurantes dentro de una red

**Campos**:
```sql
CREATE TABLE reviews (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    network_id      BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    restaurant_id   BIGINT UNSIGNED NOT NULL,
    rating          TINYINT UNSIGNED NOT NULL,  -- 1-5
    comment         TEXT NULL,
    date_of_visit   DATE NOT NULL,
    meal_type       ENUM('breakfast', 'lunch', 'dinner', 'brunch', 'other') NULL,
    price_paid      DECIMAL(10, 2) NULL,
    would_return    BOOLEAN NULL,
    tags            JSON NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,

    CONSTRAINT chk_rating CHECK (rating >= 1 AND rating <= 5)
);

-- Índices
CREATE INDEX idx_reviews_network ON reviews(network_id);
CREATE INDEX idx_reviews_user ON reviews(user_id);
CREATE INDEX idx_reviews_restaurant ON reviews(restaurant_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);
CREATE INDEX idx_reviews_date ON reviews(date_of_visit);
CREATE INDEX idx_reviews_created ON reviews(created_at);
CREATE INDEX idx_reviews_deleted_at ON reviews(deleted_at);
```

**Tags JSON ejemplo**:
```json
["romantic", "family-friendly", "outdoor-seating", "vegan-options"]
```

**Relaciones**:
- `belongsTo(Network)`
- `belongsTo(User)`
- `belongsTo(Restaurant)`
- `hasMany(ReviewComment)`
- `morphMany(Media)` via Spatie MediaLibrary

**Migración**: `2024_01_04_000000_create_reviews_table.php`

---

### 7. `review_comments` - Comentarios en Reseñas

**Propósito**: Permitir a miembros comentar reseñas

**Campos**:
```sql
CREATE TABLE review_comments (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    review_id       BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    parent_id       BIGINT UNSIGNED NULL,  -- Para hilos de respuestas
    body            TEXT NOT NULL,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,
    deleted_at      TIMESTAMP NULL,

    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES review_comments(id) ON DELETE CASCADE
);

-- Índices
CREATE INDEX idx_comments_review ON review_comments(review_id);
CREATE INDEX idx_comments_user ON review_comments(user_id);
CREATE INDEX idx_comments_parent ON review_comments(parent_id);
CREATE INDEX idx_comments_created ON review_comments(created_at);
```

**Relaciones**:
- `belongsTo(Review)`
- `belongsTo(User)`
- `belongsTo(ReviewComment, 'parent_id')` - comentario padre
- `hasMany(ReviewComment, 'parent_id')` - respuestas

**Migración**: `2024_01_05_000000_create_review_comments_table.php`

---

### 8. `visit_wishes` - Lista "Por Visitar"

**Propósito**: Restaurantes que los miembros quieren visitar

**Campos**:
```sql
CREATE TABLE visit_wishes (
    id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    network_id      BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    restaurant_id   BIGINT UNSIGNED NOT NULL,
    notes           TEXT NULL,
    priority        ENUM('low', 'medium', 'high') DEFAULT 'medium',
    added_by_user   BIGINT UNSIGNED NULL,  -- Quién lo recomendó
    visited_at      TIMESTAMP NULL,  -- Cuando se convirtió en reseña
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    FOREIGN KEY (network_id) REFERENCES networks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by_user) REFERENCES users(id) ON DELETE SET NULL,

    UNIQUE KEY unique_user_restaurant_wish (user_id, restaurant_id, network_id)
);

-- Índices
CREATE INDEX idx_wishes_network ON visit_wishes(network_id);
CREATE INDEX idx_wishes_user ON visit_wishes(user_id);
CREATE INDEX idx_wishes_restaurant ON visit_wishes(restaurant_id);
CREATE INDEX idx_wishes_priority ON visit_wishes(priority);
CREATE INDEX idx_wishes_visited ON visit_wishes(visited_at);
```

**Relaciones**:
- `belongsTo(Network)`
- `belongsTo(User)`
- `belongsTo(Restaurant)`
- `belongsTo(User, 'added_by_user')` - recomendador

**Migración**: `2024_01_06_000000_create_visit_wishes_table.php`

---

### 9. `media` - Spatie MediaLibrary

**Propósito**: Almacenar fotos de reseñas, tickets, logos

**Campos**:
```sql
CREATE TABLE media (
    id                  BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    model_type          VARCHAR(255) NOT NULL,
    model_id            BIGINT UNSIGNED NOT NULL,
    uuid                CHAR(36) NULL UNIQUE,
    collection_name     VARCHAR(255) NOT NULL,
    name                VARCHAR(255) NOT NULL,
    file_name           VARCHAR(255) NOT NULL,
    mime_type           VARCHAR(255) NULL,
    disk                VARCHAR(255) NOT NULL,
    conversions_disk    VARCHAR(255) NULL,
    size                BIGINT UNSIGNED NOT NULL,
    manipulations       JSON NULL,
    custom_properties   JSON NULL,
    generated_conversions JSON NULL,
    responsive_images   JSON NULL,
    order_column        INT UNSIGNED NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL
);

-- Índices
CREATE INDEX idx_media_model ON media(model_type, model_id);
CREATE INDEX idx_media_collection ON media(collection_name);
CREATE INDEX idx_media_uuid ON media(uuid);
```

**Colecciones**:
- `ticket` - Ticket/factura de la reseña
- `gallery` - Fotos de la comida
- `logo` - Logo de red
- `avatar` - Avatar de usuario

**Relaciones**:
- Polymorphic `morphTo()`

**Migración**: Instalada por Spatie MediaLibrary

---

## 🔗 RELACIONES DETALLADAS

### User ↔ Network (many-to-many)

```php
// User.php
public function networks(): BelongsToMany
{
    return $this->belongsToMany(Network::class, 'memberships')
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}

public function ownedNetworks(): BelongsToMany
{
    return $this->networks()->wherePivot('role', 'owner');
}

// Network.php
public function members(): BelongsToMany
{
    return $this->belongsToMany(User::class, 'memberships')
        ->withPivot('role', 'joined_at')
        ->withTimestamps();
}

public function owner(): BelongsToMany
{
    return $this->members()->wherePivot('role', 'owner');
}

public function admins(): BelongsToMany
{
    return $this->members()->wherePivotIn('role', ['owner', 'admin']);
}
```

### Network → Reviews (one-to-many)

```php
// Network.php
public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

public function recentReviews(int $limit = 10): HasMany
{
    return $this->reviews()
        ->with(['user', 'restaurant'])
        ->latest()
        ->limit($limit);
}

// Review.php
public function network(): BelongsTo
{
    return $this->belongsTo(Network::class);
}
```

### Review → Restaurant (many-to-one)

```php
// Review.php
public function restaurant(): BelongsTo
{
    return $this->belongsTo(Restaurant::class);
}

// Restaurant.php
public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

public function averageRating(): float
{
    return round($this->reviews()->avg('rating'), 1);
}
```

### Review → Comments (one-to-many)

```php
// Review.php
public function comments(): HasMany
{
    return $this->hasMany(ReviewComment::class);
}

public function rootComments(): HasMany
{
    return $this->comments()->whereNull('parent_id');
}

// ReviewComment.php
public function review(): BelongsTo
{
    return $this->belongsTo(Review::class);
}

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function parent(): BelongsTo
{
    return $this->belongsTo(ReviewComment::class, 'parent_id');
}

public function replies(): HasMany
{
    return $this->hasMany(ReviewComment::class, 'parent_id');
}
```

### Review → Media (polymorphic)

```php
// Review.php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Review extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('ticket')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);

        $this->addMediaCollection('gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
    }
}
```

---

## 📝 ORDEN DE MIGRACIONES

Las migraciones DEBEN ejecutarse en este orden:

```
1.  2024_01_01_000000_create_users_table.php
2.  2024_01_01_000001_create_password_reset_tokens_table.php
3.  2024_01_01_000002_create_failed_jobs_table.php
4.  2024_01_01_000003_create_personal_access_tokens_table.php

5.  2024_01_02_000000_create_networks_table.php
6.  2024_01_02_100000_create_memberships_table.php
7.  2024_01_02_200000_create_invitations_table.php

8.  2024_01_03_000000_create_restaurants_table.php

9.  2024_01_04_000000_create_reviews_table.php
10. 2024_01_04_100000_create_media_table.php  (Spatie)

11. 2024_01_05_000000_create_review_comments_table.php

12. 2024_01_06_000000_create_visit_wishes_table.php
```

---

## 🔍 QUERIES COMUNES OPTIMIZADOS

### Obtener todas las reviews de una red con datos relacionados

```php
$reviews = Review::where('network_id', $networkId)
    ->with([
        'user:id,name,email',
        'restaurant:id,name,address,cuisine_type',
        'media',
        'comments' => fn($q) => $q->with('user:id,name')->latest()
    ])
    ->latest()
    ->paginate(20);
```

### Buscar restaurantes por nombre o ciudad

```php
$restaurants = Restaurant::query()
    ->where(function($q) use ($search) {
        $q->where('name', 'LIKE', "%{$search}%")
          ->orWhere('city', 'LIKE', "%{$search}%")
          ->orWhere('address', 'LIKE', "%{$search}%");
    })
    ->get();
```

### Obtener estadísticas de una red

```php
$stats = [
    'total_reviews' => $network->reviews()->count(),
    'total_members' => $network->members()->count(),
    'total_restaurants' => $network->reviews()
        ->distinct('restaurant_id')
        ->count(),
    'avg_rating' => round($network->reviews()->avg('rating'), 1),
    'recent_activity' => $network->reviews()
        ->with('user:id,name', 'restaurant:id,name')
        ->latest()
        ->limit(5)
        ->get()
];
```

### Obtener restaurantes en un área geográfica

```php
$restaurants = Restaurant::query()
    ->whereBetween('latitude', [$minLat, $maxLat])
    ->whereBetween('longitude', [$minLng, $maxLng])
    ->get();
```

---

## 🎯 ÍNDICES Y PERFORMANCE

### Índices críticos ya definidos:

1. **Búsquedas frecuentes**:
   - `restaurants(name)` - búsqueda de restaurantes
   - `restaurants(city)` - filtro por ciudad
   - `restaurants(cuisine_type)` - filtro por tipo

2. **Foreign keys**:
   - Todos tienen índices automáticos

3. **Filtros y ordenamiento**:
   - `reviews(rating)` - filtro por calificación
   - `reviews(date_of_visit)` - ordenar por fecha
   - `reviews(created_at)` - ordenar por recientes

4. **Geolocalización**:
   - `restaurants(latitude, longitude)` - búsquedas en mapa

### Considerar agregar después:

```sql
-- Si hay muchas búsquedas de texto
CREATE FULLTEXT INDEX ft_reviews_comment ON reviews(comment);

-- Si se filtran mucho por tags
CREATE INDEX idx_reviews_tags ON reviews(tags);  -- Solo MySQL 8.0+
```

---

## 🔒 CONSTRAINTS Y VALIDACIONES

### A nivel de base de datos:

```sql
-- Rating entre 1 y 5
ALTER TABLE reviews ADD CONSTRAINT chk_rating
    CHECK (rating >= 1 AND rating <= 5);

-- Email único
ALTER TABLE users ADD UNIQUE (email);

-- Usuario no puede estar dos veces en la misma red
ALTER TABLE memberships ADD UNIQUE KEY unique_user_network (user_id, network_id);

-- Usuario no puede tener el mismo restaurante dos veces en "por visitar"
ALTER TABLE visit_wishes ADD UNIQUE KEY unique_user_restaurant_wish
    (user_id, restaurant_id, network_id);
```

### A nivel de aplicación (Models):

```php
// Review.php
protected $casts = [
    'rating' => 'integer',
    'date_of_visit' => 'date',
    'would_return' => 'boolean',
    'tags' => 'array',
];

protected $fillable = [
    'network_id', 'user_id', 'restaurant_id',
    'rating', 'comment', 'date_of_visit',
    'meal_type', 'price_paid', 'would_return', 'tags'
];

// Validar que rating esté en rango
protected static function booted()
{
    static::saving(function ($review) {
        if ($review->rating < 1 || $review->rating > 5) {
            throw new \InvalidArgumentException('Rating debe estar entre 1 y 5');
        }
    });
}
```

---

## 📊 SEEDERS RECOMENDADOS

### DatabaseSeeder.php

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        NetworkSeeder::class,
        RestaurantSeeder::class,
        ReviewSeeder::class,
    ]);
}
```

### Datos de prueba sugeridos:

- **3-5 usuarios** diferentes
- **2-3 redes** con diferentes configuraciones
- **10-20 restaurantes** variados
- **20-50 reseñas** distribuidas
- **5-10 comentarios** en algunas reseñas
- **5-10 wishes** en diferentes usuarios

---

## 🔄 MIGRACIONES FUTURAS (EXTENSIBILIDAD)

Si en el futuro se necesita:

### Favoritos de restaurantes
```sql
CREATE TABLE favorite_restaurants (
    user_id BIGINT UNSIGNED,
    restaurant_id BIGINT UNSIGNED,
    PRIMARY KEY (user_id, restaurant_id)
);
```

### Notificaciones
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255),
    notifiable_type VARCHAR(255),
    notifiable_id BIGINT UNSIGNED,
    data JSON,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL
);
```

### Actividad / Feed
```sql
CREATE TABLE activities (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    network_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    type VARCHAR(50),
    subject_type VARCHAR(255),
    subject_id BIGINT UNSIGNED,
    created_at TIMESTAMP
);
```

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
