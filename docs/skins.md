# 🎨 SISTEMA DE SKINS
## Guía Completa de Temas Visuales

---

## 🎯 PROPÓSITO

Este documento explica:
- Cómo funciona el sistema de skins
- Cómo usar la plantilla Listox
- Organización de assets (CSS, JS, iconos)
- Creación de componentes reutilizables
- Buenas prácticas de implementación

---

## 📋 PLANTILLA BASE: LISTOX

### Información de la Plantilla

**Fuente**: https://themegavias.com/wp/listox/

**Características**:
- Tema WordPress profesional para directorios
- Múltiples módulos reutilizables
- Diseño responsive
- Iconografía completa
- Componentes modernos

### ⚠️ REGLAS IMPORTANTES DE IMPLEMENTACIÓN

#### 1. NO usar SVG inline

❌ **Incorrecto**:
```html
<svg width="24" height="24">
    <path d="M12 2L2 7v10c0..."/>
</svg>
```

✅ **Correcto**:
```html
<img src="/assets/listox/icons/star.svg" alt="Star">
<!-- o -->
<i class="icon-star"></i>
```

**Razón**: Los SVG inline aumentan el tamaño de las vistas y son difíciles de mantener.

#### 2. NO usar URLs externas (excepto librerías públicas)

❌ **Incorrecto**:
```html
<link href="https://fonts.googleapis.com/..." rel="stylesheet">
<script src="https://cdn.example.com/custom-lib.js"></script>
```

✅ **Correcto**:
```html
<!-- Librerías públicas OK -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Assets propios: copiar localmente -->
<link href="/assets/listox/css/fonts.css" rel="stylesheet">
<script src="/assets/listox/js/custom-lib.js"></script>
```

**Excepciones permitidas**:
- jQuery
- Bootstrap CDN (si se usa)
- Google Fonts (opcional)

#### 3. Copiar TODOS los assets localmente

**Assets a copiar**:
```
public/assets/listox/
├── css/
│   ├── bootstrap.min.css
│   ├── style.css
│   ├── components.css
│   ├── responsive.css
│   └── fonts.css
├── js/
│   ├── main.js
│   ├── components.js
│   ├── map.js
│   └── plugins/
│       ├── slick.min.js
│       ├── isotope.min.js
│       └── ...
├── fonts/
│   ├── Poppins-Regular.woff2
│   ├── Poppins-Bold.woff2
│   └── ...
└── icons/
    ├── star.svg
    ├── map-marker.svg
    ├── user.svg
    └── ...
```

#### 4. Crear componentes exhaustivamente

Cada elemento visual reutilizable debe ser un componente Blade.

**Ejemplos de componentes a crear**:
- Botones
- Cards
- Formularios
- Ratings
- Badges
- Modales
- Dropdowns
- Navegación
- Footer
- Headers

---

## 🏗️ ESTRUCTURA DEL SISTEMA DE SKINS

### Configuración Base

```php
// config/app.php
return [
    // ...
    'skin' => env('APP_SKIN', 'listox'),
];
```

```env
# .env
APP_SKIN=listox
```

### Helper Global

```php
// app/helpers.php
if (!function_exists('skin')) {
    /**
     * Obtiene la ruta de una vista del skin actual
     *
     * @param string $path Ruta relativa de la vista
     * @return string Ruta completa con el skin
     */
    function skin(string $path): string
    {
        $skin = config('app.skin', 'listox');
        return "skins.{$skin}.{$path}";
    }
}

if (!function_exists('skin_asset')) {
    /**
     * Obtiene la URL de un asset del skin actual
     *
     * @param string $path Ruta relativa del asset
     * @return string URL completa del asset
     */
    function skin_asset(string $path): string
    {
        $skin = config('app.skin', 'listox');
        return asset("assets/{$skin}/{$path}");
    }
}
```

```json
// composer.json
{
    "autoload": {
        "files": [
            "app/helpers.php"
        ]
    }
}
```

---

## 📁 ORGANIZACIÓN DE ARCHIVOS

### Vistas por Skin

```
resources/views/skins/listox/
├── layouts/
│   ├── app.blade.php              ← Layout principal autenticado
│   ├── guest.blade.php            ← Layout para invitados
│   └── dashboard.blade.php        ← Layout del dashboard
│
├── components/
│   ├── buttons/
│   │   ├── primary.blade.php      ← <x-listox::button.primary />
│   │   ├── secondary.blade.php
│   │   └── icon.blade.php
│   │
│   ├── cards/
│   │   ├── review.blade.php       ← Card de reseña
│   │   ├── restaurant.blade.php   ← Card de restaurante
│   │   └── network.blade.php      ← Card de red
│   │
│   ├── form/
│   │   ├── input.blade.php
│   │   ├── textarea.blade.php
│   │   ├── select.blade.php
│   │   ├── rating.blade.php
│   │   ├── file-upload.blade.php
│   │   └── gallery-upload.blade.php
│   │
│   ├── navigation/
│   │   ├── navbar.blade.php
│   │   ├── sidebar.blade.php
│   │   └── breadcrumb.blade.php
│   │
│   ├── map/
│   │   ├── network.blade.php      ← Mapa de red
│   │   └── marker.blade.php       ← Marcador de mapa
│   │
│   └── ui/
│       ├── badge.blade.php
│       ├── modal.blade.php
│       ├── dropdown.blade.php
│       ├── alert.blade.php
│       └── loader.blade.php
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
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── show.blade.php
│   │   └── edit.blade.php
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
│       └── index.blade.php
│
└── partials/
    ├── header.blade.php
    ├── footer.blade.php
    ├── scripts.blade.php
    └── styles.blade.php
```

### Assets por Skin

```
public/assets/listox/
├── css/
│   ├── vendors/                   ← CSS de terceros
│   │   ├── bootstrap.min.css
│   │   └── slick.css
│   ├── base/                      ← Estilos base
│   │   ├── reset.css
│   │   ├── typography.css
│   │   └── variables.css
│   ├── components/                ← Estilos de componentes
│   │   ├── buttons.css
│   │   ├── cards.css
│   │   ├── forms.css
│   │   ├── navigation.css
│   │   └── modals.css
│   ├── layouts/                   ← Estilos de layouts
│   │   ├── header.css
│   │   ├── footer.css
│   │   └── sidebar.css
│   ├── pages/                     ← Estilos por página
│   │   ├── dashboard.css
│   │   ├── reviews.css
│   │   └── map.css
│   ├── responsive.css             ← Media queries
│   └── style.css                  ← CSS principal (importa todo)
│
├── js/
│   ├── vendors/                   ← JS de terceros
│   │   ├── slick.min.js
│   │   ├── isotope.min.js
│   │   └── leaflet.js
│   ├── components/                ← JS de componentes
│   │   ├── modal.js
│   │   ├── dropdown.js
│   │   ├── rating.js
│   │   └── gallery.js
│   ├── modules/                   ← Módulos funcionales
│   │   ├── map.js
│   │   ├── search.js
│   │   └── filters.js
│   └── main.js                    ← JS principal
│
├── icons/                         ← TODOS los iconos SVG
│   ├── social/
│   │   ├── facebook.svg
│   │   ├── instagram.svg
│   │   └── twitter.svg
│   ├── ui/
│   │   ├── star.svg
│   │   ├── star-filled.svg
│   │   ├── heart.svg
│   │   ├── map-marker.svg
│   │   ├── user.svg
│   │   ├── search.svg
│   │   └── close.svg
│   └── categories/
│       ├── italian.svg
│       ├── mexican.svg
│       └── ...
│
├── images/
│   ├── logo.png
│   ├── logo-white.png
│   ├── placeholder.jpg
│   └── backgrounds/
│
└── fonts/
    ├── Poppins-Regular.woff2
    ├── Poppins-Bold.woff2
    └── ...
```

---

## 🧩 CREACIÓN DE COMPONENTES

### Principios

1. **Exhaustividad**: Cada elemento reutilizable es un componente
2. **Documentación**: Cada componente documenta sus props
3. **Flexibilidad**: Aceptar props para personalización
4. **Consistencia**: Seguir el mismo patrón

### Ejemplo Completo: Componente de Rating

#### Paso 1: Crear clase del componente

```php
// app/View/Components/Rating.php
namespace App\View\Components;

use Illuminate\View\Component;

class Rating extends Component
{
    public int $rating;
    public int $maxRating;
    public string $size;
    public bool $readonly;

    /**
     * @param int $rating Calificación (1-5)
     * @param int $maxRating Máxima calificación posible
     * @param string $size Tamaño: 'sm', 'md', 'lg'
     * @param bool $readonly Solo lectura o interactivo
     */
    public function __construct(
        int $rating,
        int $maxRating = 5,
        string $size = 'md',
        bool $readonly = true
    ) {
        $this->rating = max(0, min($rating, $maxRating));
        $this->maxRating = $maxRating;
        $this->size = $size;
        $this->readonly = $readonly;
    }

    public function render()
    {
        return view(skin('components.rating'));
    }
}
```

#### Paso 2: Crear vista del componente

```blade
{{-- resources/views/skins/listox/components/rating.blade.php --}}
@props([
    'rating' => 0,
    'maxRating' => 5,
    'size' => 'md',
    'readonly' => true
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8'
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="rating-container {{ $readonly ? 'readonly' : 'interactive' }}"
     {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }}>

    @for ($i = 1; $i <= $maxRating; $i++)
        @if ($readonly)
            <img
                src="{{ skin_asset('icons/ui/' . ($i <= $rating ? 'star-filled.svg' : 'star.svg')) }}"
                alt="Star {{ $i }}"
                class="{{ $sizeClass }}"
            >
        @else
            <button
                type="button"
                class="rating-star {{ $sizeClass }}"
                data-rating="{{ $i }}"
            >
                <img
                    src="{{ skin_asset('icons/ui/' . ($i <= $rating ? 'star-filled.svg' : 'star.svg')) }}"
                    alt="Star {{ $i }}"
                    class="w-full h-full"
                >
            </button>
        @endif
    @endfor

    @unless ($readonly)
        <input type="hidden" name="rating" value="{{ $rating }}" class="rating-input">
    @endunless
</div>

@unless ($readonly)
    @push('scripts')
    <script>
        document.querySelectorAll('.rating-star').forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                const container = this.closest('.rating-container');
                const input = container.querySelector('.rating-input');

                input.value = rating;

                // Actualizar visualmente
                container.querySelectorAll('.rating-star').forEach((s, index) => {
                    const img = s.querySelector('img');
                    if (index < rating) {
                        img.src = '{{ skin_asset('icons/ui/star-filled.svg') }}';
                    } else {
                        img.src = '{{ skin_asset('icons/ui/star.svg') }}';
                    }
                });
            });
        });
    </script>
    @endpush
@endunless
```

#### Paso 3: Usar el componente

```blade
{{-- Solo lectura --}}
<x-rating :rating="$review->rating" />

{{-- Diferentes tamaños --}}
<x-rating :rating="4" size="sm" />
<x-rating :rating="5" size="lg" />

{{-- Interactivo (formulario) --}}
<x-rating :rating="0" :readonly="false" />
```

---

## 🎨 MÓDULOS DE LISTOX A IMPLEMENTAR

Según el tema Listox, estos son los módulos principales:

### 1. Hero Section
```blade
{{-- resources/views/skins/listox/components/hero.blade.php --}}
<section class="hero-section">
    <div class="hero-content">
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
        {{ $slot }}
    </div>
</section>

{{-- Uso --}}
<x-hero title="Encuentra los mejores restaurantes" subtitle="Comparte tus experiencias">
    <x-button.primary href="{{ route('networks.create') }}">
        Crear Red
    </x-button.primary>
</x-hero>
```

### 2. Search Bar
```blade
{{-- components/search-bar.blade.php --}}
<form action="{{ route('restaurants.search') }}" class="search-bar">
    <input type="text" name="q" placeholder="Buscar restaurantes...">
    <button type="submit">
        <img src="{{ skin_asset('icons/ui/search.svg') }}" alt="Buscar">
    </button>
</form>
```

### 3. Restaurant Card
```blade
{{-- components/cards/restaurant.blade.php --}}
@props(['restaurant'])

<div class="restaurant-card">
    <div class="card-image">
        <img src="{{ $restaurant->photo_url }}" alt="{{ $restaurant->name }}">
        <span class="badge">{{ $restaurant->cuisine_type }}</span>
    </div>
    <div class="card-content">
        <h3>{{ $restaurant->name }}</h3>
        <p class="address">
            <img src="{{ skin_asset('icons/ui/map-marker.svg') }}" alt="Location">
            {{ $restaurant->city }}
        </p>
        <div class="card-footer">
            <x-rating :rating="$restaurant->average_rating" size="sm" />
            <span class="price-range">{{ $restaurant->price_range }}</span>
        </div>
    </div>
</div>
```

### 4. Review Card
```blade
{{-- components/cards/review.blade.php --}}
@props(['review'])

<article class="review-card">
    <header class="review-header">
        <div class="user-info">
            <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}">
            <div>
                <h4>{{ $review->user->name }}</h4>
                <time>{{ $review->date_of_visit->format('d/m/Y') }}</time>
            </div>
        </div>
        <x-rating :rating="$review->rating" size="sm" />
    </header>

    <div class="review-body">
        <h3>
            <a href="{{ route('restaurants.show', $review->restaurant) }}">
                {{ $review->restaurant->name }}
            </a>
        </h3>
        <p>{{ $review->comment }}</p>

        @if ($review->hasMedia('gallery'))
            <div class="review-gallery">
                @foreach ($review->getMedia('gallery') as $photo)
                    <img src="{{ $photo->getUrl('thumb') }}" alt="Photo">
                @endforeach
            </div>
        @endif
    </div>

    <footer class="review-footer">
        <button class="btn-like">
            <img src="{{ skin_asset('icons/ui/heart.svg') }}" alt="Like">
            <span>Me gusta</span>
        </button>
        <button class="btn-comment">
            <img src="{{ skin_asset('icons/ui/comment.svg') }}" alt="Comment">
            <span>{{ $review->comments_count }} comentarios</span>
        </button>
    </footer>
</article>
```

### 5. Filter Sidebar
```blade
{{-- components/filters.blade.php --}}
<aside class="filters-sidebar">
    <h3>Filtros</h3>

    <div class="filter-group">
        <label>Calificación mínima</label>
        <x-rating :rating="0" :readonly="false" />
    </div>

    <div class="filter-group">
        <label>Tipo de comida</label>
        <select name="cuisine_type">
            <option value="">Todas</option>
            <option value="italian">Italiana</option>
            <option value="mexican">Mexicana</option>
            <option value="japanese">Japonesa</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Rango de precio</label>
        <div class="price-options">
            @foreach(['$', '$$', '$$$', '$$$$'] as $price)
                <label>
                    <input type="checkbox" name="price[]" value="{{ $price }}">
                    {{ $price }}
                </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="btn-apply-filters">
        Aplicar Filtros
    </button>
</aside>
```

---

## 📝 LAYOUTS PRINCIPALES

### Layout App (Autenticado)

```blade
{{-- resources/views/skins/listox/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ skin_asset('css/vendors/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ skin_asset('css/style.css') }}">
    @stack('styles')
</head>
<body>
    {{-- Header --}}
    @include(skin('partials.header'))

    {{-- Main Content --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include(skin('partials.footer'))

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ skin_asset('js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ skin_asset('js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

### Layout Guest

```blade
{{-- resources/views/skins/listox/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title')</title>

    <link rel="stylesheet" href="{{ skin_asset('css/style.css') }}">
    @stack('styles')
</head>
<body class="guest-layout">
    <div class="guest-container">
        @yield('content')
    </div>

    <script src="{{ skin_asset('js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

---

## 🎯 BUENAS PRÁCTICAS

### 1. Nomenclatura consistente

```blade
{{-- Componentes --}}
<x-button.primary />        ← buttons/primary.blade.php
<x-card.review />           ← cards/review.blade.php
<x-form.input />            ← form/input.blade.php

{{-- Assets --}}
skin_asset('css/style.css')
skin_asset('icons/ui/star.svg')
```

### 2. Documentar componentes

```blade
{{--
    Componente: Rating Stars
    Propósito: Mostrar calificación con estrellas

    Props:
    - rating (int): Calificación de 1 a 5
    - maxRating (int): Máxima calificación (default: 5)
    - size (string): 'sm', 'md', 'lg' (default: 'md')
    - readonly (bool): Solo lectura (default: true)

    Ejemplo:
    <x-rating :rating="4" size="lg" />
--}}
```

### 3. Usar @stack para scripts/styles

```blade
{{-- En componente --}}
@push('scripts')
<script src="{{ skin_asset('js/components/gallery.js') }}"></script>
@endpush

{{-- En layout --}}
@stack('scripts')
```

### 4. Iconos reutilizables

Crear helper para iconos:

```php
// app/helpers.php
function icon(string $name, string $alt = '', array $attributes = []): string
{
    $class = $attributes['class'] ?? '';
    $size = $attributes['size'] ?? 'w-6 h-6';

    return sprintf(
        '<img src="%s" alt="%s" class="%s %s">',
        skin_asset("icons/ui/{$name}.svg"),
        $alt ?: $name,
        $size,
        $class
    );
}
```

```blade
{!! icon('star', 'Rating') !!}
{!! icon('map-marker', 'Location', ['size' => 'w-4 h-4']) !!}
```

---

## 🔄 CAMBIAR DE SKIN

```env
# .env
APP_SKIN=listox  # Tema por defecto
# APP_SKIN=dark    # Tema oscuro
# APP_SKIN=pastel  # Tema pastel
```

Todo el código se adapta automáticamente sin cambios.

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
