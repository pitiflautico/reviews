# 🌍 SISTEMA GLOBAL DE RESTAURANTES
## Catálogo Unificado para Todas las Redes

---

## 🎯 PROPÓSITO

Este documento explica el concepto fundamental del **catálogo global unificado** de restaurantes, cómo funciona la normalización automática, la detección de duplicados y el proceso de fusión.

---

## 💡 CONCEPTO FUNDAMENTAL

### ¿Qué es el Sistema Global?

En lugar de que cada red tenga su propia copia de restaurantes, existe **UN SOLO CATÁLOGO GLOBAL** compartido por todas las redes.

### Analogía

Imagina una biblioteca pública (catálogo global de restaurantes) donde:
- Los libros (restaurantes) están en estanterías compartidas
- Cada persona (red) tiene su propia lista de libros favoritos (reseñas)
- Pero los libros físicos son los mismos para todos
- Si dos personas encuentran el mismo libro duplicado, se fusiona en uno solo

---

## ⚖️ COMPARACIÓN: Sistema Global vs. Sistema por Red

| Aspecto | Sistema por Red (❌ NO) | Sistema Global (✅ SÍ) |
|---------|------------------------|----------------------|
| **Almacenamiento** | Cada red tiene copia del restaurante | Un solo registro global |
| **Duplicados** | "La Pepita" existe 50 veces | "La Pepita" existe UNA vez |
| **Actualización** | Cambiar nombre en 50 lugares | Cambiar nombre en 1 lugar |
| **Rankings globales** | Imposible comparar | Posible sin romper privacidad |
| **Búsqueda** | Solo dentro de la red | Global, más resultados |
| **Consistencia** | Datos desactualizados | Datos siempre actuales |

---

## 🏗️ ARQUITECTURA DEL SISTEMA

### Modelo de Datos

```
┌──────────────┐         ┌────────────────┐         ┌──────────────┐
│   Network A  │         │  RESTAURANTS   │         │   Network B  │
│              │         │    (GLOBAL)    │         │              │
│  Reviews ────┼────────→│                │←────────┼──── Reviews  │
│              │         │  La Pepita     │         │              │
└──────────────┘         │  ID: 123       │         └──────────────┘
                         │  name: ...     │
┌──────────────┐         │  lat/lng: ...  │         ┌──────────────┐
│   Network C  │         └────────────────┘         │   Network D  │
│              │                 ↑                   │              │
│  Reviews ────┼─────────────────┘                   │              │
│              │                                     │  VisitWish ──┤
└──────────────┘                                     └──────────────┘
```

Todas las redes **apuntan al mismo restaurante global** mediante `restaurant_id`.

---

## 🔄 PROCESO DE NORMALIZACIÓN AUTOMÁTICA

### ¿Qué es la Normalización?

Convertir datos a un formato estándar para facilitar comparación:

```
Original:            Normalizado:
"La Pépita"     →   "la pepita"
"LA PEPITA"     →   "la pepita"
"la pepita  "   →   "la pepita"
```

### Campos Normalizados

```sql
CREATE TABLE restaurants (
    id                  BIGINT,
    name                VARCHAR(255),           -- Original: "La Pépita Burger"
    name_normalized     VARCHAR(255),           -- Normalizado: "la pepita burger"
    address             VARCHAR(500),           -- Original: "Calle Mayor, 123  "
    address_normalized  VARCHAR(500),           -- Normalizado: "calle mayor 123"
    ...
);
```

### Funciones de Normalización

```php
// app/Services/RestaurantNormalizationService.php

class RestaurantNormalizationService
{
    /**
     * Normaliza un nombre de restaurante
     */
    public function normalizeName(string $name): string
    {
        // 1. Convertir a minúsculas
        $normalized = mb_strtolower($name, 'UTF-8');

        // 2. Quitar acentos
        $normalized = $this->removeAccents($normalized);

        // 3. Quitar caracteres especiales (excepto espacios, guiones)
        $normalized = preg_replace('/[^a-z0-9\s\-]/', '', $normalized);

        // 4. Normalizar espacios múltiples
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // 5. Trim
        $normalized = trim($normalized);

        return $normalized;
    }

    /**
     * Normaliza una dirección
     */
    public function normalizeAddress(string $address): string
    {
        $normalized = mb_strtolower($address, 'UTF-8');
        $normalized = $this->removeAccents($normalized);

        // Normalizar abreviaturas comunes
        $normalized = str_replace(['calle', 'c/', 'c.', 'avenida', 'av/', 'av.'], '', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);

        return $normalized;
    }

    /**
     * Quitar acentos de texto
     */
    private function removeAccents(string $text): string
    {
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ñ' => 'n', 'Ñ' => 'n',
            'ü' => 'u', 'Ü' => 'u',
        ];

        return strtr($text, $replacements);
    }
}
```

### Cuándo se Normaliza

Normalización automática en estos eventos:

1. **Al crear restaurante**: Observer en `Restaurant::creating()`
2. **Al actualizar restaurante**: Observer en `Restaurant::updating()`

```php
// app/Models/Restaurant.php

protected static function booted()
{
    static::saving(function ($restaurant) {
        $normalizer = app(RestaurantNormalizationService::class);

        $restaurant->name_normalized = $normalizer->normalizeName($restaurant->name);
        $restaurant->address_normalized = $normalizer->normalizeAddress($restaurant->address);
    });
}
```

---

## 🔍 DETECCIÓN DE DUPLICADOS

### Métodos de Detección

#### 1. Por Nombre Normalizado

```php
public function findSimilarByName(string $name): Collection
{
    $normalized = $this->normalizer->normalizeName($name);

    return Restaurant::where('name_normalized', 'LIKE', "%{$normalized}%")
        ->orWhereRaw('SOUNDEX(name_normalized) = SOUNDEX(?)', [$normalized])
        ->get();
}
```

**Detecta**:
- "La Pepita" vs "LA PEPITA"
- "La Pépita" vs "la pepita"
- "Pepita Burger" vs "La Pepita"

#### 2. Por Dirección Normalizada

```php
public function findSimilarByAddress(string $address): Collection
{
    $normalized = $this->normalizer->normalizeAddress($address);

    return Restaurant::where('address_normalized', 'LIKE', "%{$normalized}%")
        ->get();
}
```

#### 3. Por Proximidad Geográfica

```php
public function findNearby(float $lat, float $lng, float $radiusKm = 0.1): Collection
{
    // Fórmula Haversine para calcular distancia
    $R = 6371; // Radio de la Tierra en km

    return Restaurant::selectRaw("
        *,
        ( {$R} * acos(
            cos( radians(?) ) *
            cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(?) ) +
            sin( radians(?) ) *
            sin( radians( latitude ) )
        ) ) AS distance
    ", [$lat, $lng, $lat])
    ->having('distance', '<', $radiusKm)
    ->orderBy('distance')
    ->get();
}
```

**Detecta**:
- Dos registros del mismo restaurante a 50 metros de distancia

#### 4. Combinación de Métodos (Score de Similitud)

```php
public function calculateSimilarityScore(Restaurant $a, Restaurant $b): float
{
    $score = 0;

    // Similaridad de nombre (40%)
    $nameSimilarity = $this->levenshteinSimilarity(
        $a->name_normalized,
        $b->name_normalized
    );
    $score += $nameSimilarity * 40;

    // Similaridad de dirección (30%)
    if ($a->address_normalized && $b->address_normalized) {
        $addressSimilarity = $this->levenshteinSimilarity(
            $a->address_normalized,
            $b->address_normalized
        );
        $score += $addressSimilarity * 30;
    }

    // Proximidad geográfica (30%)
    if ($a->latitude && $b->latitude) {
        $distance = $this->calculateDistance(
            $a->latitude, $a->longitude,
            $b->latitude, $b->longitude
        );

        if ($distance < 0.1) { // < 100 metros
            $score += 30;
        } elseif ($distance < 0.5) { // < 500 metros
            $score += 15;
        }
    }

    return $score;
}

private function levenshteinSimilarity(string $a, string $b): float
{
    $distance = levenshtein($a, $b);
    $maxLength = max(strlen($a), strlen($b));

    if ($maxLength === 0) return 1.0;

    return 1 - ($distance / $maxLength);
}
```

### Umbrales de Detección

```php
const DUPLICATE_THRESHOLDS = [
    'high_confidence'   => 85.0,  // Muy probable duplicado
    'medium_confidence' => 70.0,  // Posible duplicado
    'low_confidence'    => 50.0,  // Revisar manualmente
];
```

---

## 🔄 FLUJO DE CREACIÓN DE RESTAURANTE

```
Usuario intenta crear restaurante
        │
        ↓
┌────────────────────────┐
│ Ingresar datos:        │
│ - Nombre               │
│ - Dirección            │
│ - Lat/Lng (opcional)   │
└────────┬───────────────┘
         │
         ↓
┌────────────────────────┐
│ Normalizar datos       │
│ (automático)           │
└────────┬───────────────┘
         │
         ↓
┌────────────────────────┐
│ Buscar duplicados:     │
│ - Por nombre           │
│ - Por dirección        │
│ - Por ubicación        │
└────────┬───────────────┘
         │
         ├──→ No hay similares → Crear restaurante
         │                       ↓
         │               ┌───────────────────┐
         │               │ Restaurant creado │
         │               │ ID: 456           │
         │               └───────────────────┘
         │
         └──→ Hay similares → Mostrar lista
                              ↓
                    ┌─────────────────────┐
                    │ ¿Es uno de estos?   │
                    │                     │
                    │ 1. La Pepita Burger │
                    │    Calle Mayor 123  │
                    │    (Score: 92%)     │
                    │                     │
                    │ 2. Pepita Food      │
                    │    Calle Mayor 125  │
                    │    (Score: 78%)     │
                    │                     │
                    │ [Usar existente]    │
                    │ [Crear nuevo]       │
                    └─────────┬───────────┘
                              │
                              ├──→ Usar existente → Redirigir a ese ID
                              │
                              └──→ Crear nuevo → Guardar + Marcar como candidato
                                                 ↓
                                        ┌───────────────────────┐
                                        │ duplicate_restaurant_ │
                                        │ candidates            │
                                        │                       │
                                        │ restaurant_a: 123     │
                                        │ restaurant_b: 456     │
                                        │ similarity: 92.0      │
                                        │ status: pending       │
                                        └───────────────────────┘
```

---

## 🔀 FUSIÓN DE RESTAURANTES DUPLICADOS

### Cuando se Fusiona

- Super admin revisa `duplicate_restaurant_candidates`
- Confirma que son duplicados
- Ejecuta fusión

### Proceso de Fusión

```php
// app/Services/RestaurantMergeService.php

public function mergeRestaurants(Restaurant $keep, Restaurant $remove): void
{
    DB::beginTransaction();
    try {
        // 1. Mover todas las reseñas del eliminado al que se mantiene
        Review::where('restaurant_id', $remove->id)
            ->update(['restaurant_id' => $keep->id]);

        // 2. Mover todos los "por visitar"
        VisitWish::where('restaurant_id', $remove->id)
            ->update(['restaurant_id' => $keep->id]);

        // 3. Actualizar registro de duplicados
        DuplicateRestaurantCandidate::where('restaurant_a_id', $remove->id)
            ->orWhere('restaurant_b_id', $remove->id)
            ->update([
                'status' => 'merged',
                'merged_into_id' => $keep->id,
                'reviewed_at' => now(),
            ]);

        // 4. Combinar datos si el que se mantiene tiene campos vacíos
        if (!$keep->phone && $remove->phone) {
            $keep->phone = $remove->phone;
        }
        if (!$keep->website && $remove->website) {
            $keep->website = $remove->website;
        }
        // ... otros campos

        $keep->save();

        // 5. Eliminar el restaurante duplicado
        $remove->delete();

        DB::commit();

        Log::info("Merged restaurant {$remove->id} into {$keep->id}");
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

---

## 📊 ESTADÍSTICAS GLOBALES SIN ROMPER PRIVACIDAD

### Lo que SÍ se puede hacer

```php
// Rankings globales ANÓNIMOS
$topRestaurants = Restaurant::select('id', 'name', 'city')
    ->withCount('reviews')
    ->withAvg('reviews', 'rating')
    ->orderByDesc('reviews_avg_rating')
    ->limit(100)
    ->get();

// Restaurante más reseñado
$mostReviewed = Restaurant::withCount('reviews')
    ->orderByDesc('reviews_count')
    ->first();
```

### Lo que NO se puede hacer (privacidad)

```php
// ❌ NO: Mostrar qué redes han reseñado un restaurante
Restaurant::find(123)->reviews()->with('network')->get();

// ❌ NO: Mostrar usuarios que han reseñado
Restaurant::find(123)->reviews()->with('user')->get();

// ✅ SÍ: Solo datos agregados anónimos
Restaurant::find(123)->reviews()->count();
Restaurant::find(123)->reviews()->avg('rating');
```

---

## 🎯 VENTAJAS DEL SISTEMA GLOBAL

### 1. Evita Duplicados

Sin sistema global:
- Red A crea "La Pepita"
- Red B crea "LA PEPITA"
- Red C crea "la pépita"
- **Total: 3 registros del mismo lugar**

Con sistema global:
- Primer usuario crea "La Pepita" → ID 123
- Todos los demás usan ID 123
- **Total: 1 registro**

### 2. Facilita Actualizaciones

Cambio de dirección del restaurante:
- Sin global: Actualizar en 50 redes diferentes
- Con global: Actualizar 1 vez, todos ven el cambio

### 3. Permite Rankings

```
Top 10 Restaurantes Globales (por AVG rating):
1. La Pepita (4.8★ de 127 reseñas)
2. Burger King (4.7★ de 89 reseñas)
...
```

Sin comprometer privacidad:
- NO se sabe qué redes los reseñaron
- NO se sabe quiénes los reseñaron
- Solo datos agregados

### 4. Búsqueda Más Rica

Usuario busca "burger":
- Sin global: Solo resultados de SU red (3 resultados)
- Con global: Todos los restaurantes (50 resultados)

Luego decide si añadir a su lista "por visitar"

---

## 🚫 LIMITACIONES Y CONSIDERACIONES

### Restaurantes Homónimos

Problema:
- "La Pepita" en Madrid
- "La Pepita" en Barcelona
- Mismo nombre, lugares diferentes

Solución:
- Dirección diferente → No son duplicados
- Lat/Lng diferentes → No son duplicados
- Sistema los mantiene separados

### Restaurantes Móviles

Problema:
- Food trucks que cambian de ubicación

Solución:
- Actualizar lat/lng cuando cambian
- O dejar lat/lng vacío

### Restaurantes Cerrados

Opciones:
1. Marcar como `is_closed: true` (mantener historial)
2. Soft delete (mantener para reseñas antiguas)
3. NO eliminar nunca (mantener integridad)

---

## 🔧 COMANDOS ÚTILES

### Detectar Duplicados Manualmente

```bash
php artisan restaurants:detect-duplicates

# Output:
# Found 23 potential duplicates
# - La Pepita (ID: 123) vs LA PEPITA (ID: 456) - Score: 95%
# - Burger King (ID: 234) vs BurgerKing (ID: 567) - Score: 88%
# ...
```

### Fusionar Duplicados

```bash
php artisan restaurants:merge {keep_id} {remove_id}

# Ejemplo:
php artisan restaurants:merge 123 456

# Output:
# Merging restaurant 456 into 123...
# - Moved 12 reviews
# - Moved 3 visit wishes
# - Restaurant 456 deleted
# Done!
```

### Estadísticas del Catálogo

```bash
php artisan restaurants:stats

# Output:
# Total restaurants: 1,234
# With reviews: 987
# Without reviews: 247
# Pending duplicates: 23
# Average reviews per restaurant: 3.4
```

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

- [ ] Tabla `restaurants` con campos `name_normalized` y `address_normalized`
- [ ] Tabla `duplicate_restaurant_candidates`
- [ ] `RestaurantNormalizationService` creado
- [ ] `DuplicateDetectionService` creado
- [ ] `RestaurantMergeService` creado
- [ ] Observer en `Restaurant` para normalización automática
- [ ] Comando `restaurants:detect-duplicates`
- [ ] Comando `restaurants:merge`
- [ ] Panel super admin para revisar duplicados
- [ ] Tests de normalización
- [ ] Tests de detección de duplicados
- [ ] Tests de fusión

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0

---

## ⏭️ PRÓXIMO PASO

Lee: `/docs/database.md` para ver el esquema completo de tablas.
