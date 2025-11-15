# 🌐 ENDPOINTS Y RUTAS
## Lista Completa de Rutas Web y API

---

## 🎯 PROPÓSITO

Este documento lista todas las rutas disponibles en la aplicación con sus métodos, parámetros y respuestas.

---

## 🔐 RUTAS DE AUTENTICACIÓN

### POST /register
**Propósito**: Registrar nuevo usuario

**Parámetros**:
```json
{
    "name": "string|required|max:255",
    "email": "string|required|email|unique:users",
    "password": "string|required|min:8|confirmed"
}
```

**Respuesta**:
- Redirect a `/dashboard`
- Session con usuario autenticado

---

### POST /login
**Propósito**: Iniciar sesión

**Parámetros**:
```json
{
    "email": "string|required|email",
    "password": "string|required",
    "remember": "boolean|optional"
}
```

**Respuesta**:
- Redirect a `/dashboard`
- Session con usuario autenticado

---

### POST /logout
**Propósito**: Cerrar sesión

**Respuesta**:
- Redirect a `/`

---

### POST /forgot-password
**Propósito**: Solicitar reset de contraseña

**Parámetros**:
```json
{
    "email": "string|required|email"
}
```

---

## 🏠 RUTAS GENERALES

### GET /
**Propósito**: Landing page

**Respuesta**: Vista `welcome`

---

### GET /dashboard
**Auth**: Requerido

**Propósito**: Dashboard principal del usuario

**Respuesta**: Vista `dashboard` con:
- Redes del usuario
- Reseñas recientes
- Estadísticas

---

## 🌐 RUTAS DE REDES

### GET /networks
**Auth**: Requerido
**Nombre**: `networks.index`

**Propósito**: Listar redes del usuario

**Respuesta**: Vista con colección de Networks

---

### GET /networks/create
**Auth**: Requerido
**Nombre**: `networks.create`

**Propósito**: Formulario de creación de red

**Respuesta**: Vista con formulario

---

### POST /networks
**Auth**: Requerido
**Nombre**: `networks.store`

**Parámetros**:
```json
{
    "name": "string|required|max:255",
    "description": "string|nullable|max:1000",
    "logo": "file|nullable|image|max:2048"
}
```

**Respuesta**: Redirect a `networks.show`

---

### GET /networks/{network}
**Auth**: Requerido + Membership
**Nombre**: `networks.show`

**Propósito**: Dashboard de la red

**Respuesta**: Vista con:
- Info de la red
- Estadísticas
- Reseñas recientes
- Miembros

---

### GET /networks/{network}/edit
**Auth**: Requerido + Owner/Admin
**Nombre**: `networks.edit`

**Propósito**: Formulario de edición

**Respuesta**: Vista con formulario prellenado

---

### PUT /networks/{network}
**Auth**: Requerido + Owner/Admin
**Nombre**: `networks.update`

**Parámetros**:
```json
{
    "name": "string|required|max:255",
    "description": "string|nullable|max:1000",
    "logo": "file|nullable|image|max:2048"
}
```

**Respuesta**: Redirect a `networks.show`

---

### DELETE /networks/{network}
**Auth**: Requerido + Owner
**Nombre**: `networks.destroy`

**Respuesta**: Redirect a `networks.index`

---

### GET /networks/{network}/map
**Auth**: Requerido + Membership
**Nombre**: `networks.map`

**Propósito**: Vista de mapa de la red

**Respuesta**: Vista con mapa interactivo

---

### GET /networks/{network}/map-data
**Auth**: Requerido + Membership
**Nombre**: `networks.map-data`

**Propósito**: Datos JSON para el mapa

**Respuesta JSON**:
```json
{
    "restaurants": [
        {
            "id": 1,
            "name": "Restaurant Name",
            "lat": 40.4168,
            "lng": -3.7038,
            "type": "visited|wishlist",
            "rating": 4.5,
            "reviews_count": 3
        }
    ]
}
```

---

## 👥 RUTAS DE MIEMBROS E INVITACIONES

### POST /networks/{network}/invite
**Auth**: Requerido + Owner/Admin
**Nombre**: `networks.invite`

**Parámetros**:
```json
{
    "email": "string|required|email",
    "role": "string|required|in:admin,member"
}
```

**Respuesta**: Redirect back con mensaje

---

### GET /invitations/{token}
**Auth**: Opcional
**Nombre**: `invitations.show`

**Propósito**: Ver invitación y aceptar

**Respuesta**: Vista con detalles de la invitación

---

### POST /invitations/{token}/accept
**Auth**: Requerido
**Nombre**: `invitations.accept`

**Respuesta**: Redirect a `networks.show`

---

### POST /networks/{network}/members/{user}/remove
**Auth**: Requerido + Owner/Admin
**Nombre**: `networks.members.remove`

**Respuesta**: Redirect back

---

## 📝 RUTAS DE RESEÑAS

### GET /networks/{network}/reviews
**Auth**: Requerido + Membership
**Nombre**: `networks.reviews.index`

**Query params**:
- `page`: int (paginación)
- `sort`: string (created_at, rating, etc.)

**Respuesta**: Vista con lista paginada

---

### GET /networks/{network}/reviews/create
**Auth**: Requerido + Membership
**Nombre**: `networks.reviews.create`

**Propósito**: Formulario de nueva reseña

**Respuesta**: Vista con formulario

---

### POST /networks/{network}/reviews
**Auth**: Requerido + Membership
**Nombre**: `networks.reviews.store`

**Parámetros**:
```json
{
    "restaurant_id": "integer|required|exists:restaurants,id",
    "rating": "integer|required|min:1|max:5",
    "comment": "string|required|max:1000",
    "date_of_visit": "date|required",
    "meal_type": "string|nullable|in:breakfast,lunch,dinner,brunch",
    "price_paid": "numeric|nullable",
    "would_return": "boolean|nullable",
    "tags": "array|nullable",
    "photos": "array|nullable|max:10",
    "photos.*": "image|max:5120",
    "ticket": "file|nullable|max:5120"
}
```

**Respuesta**: Redirect a `reviews.show`

---

### GET /reviews/{review}
**Auth**: Requerido + Membership de red
**Nombre**: `reviews.show`

**Propósito**: Ver detalle de reseña

**Respuesta**: Vista con:
- Datos de la reseña
- Fotos y ticket
- Comentarios
- Restaurante

---

### GET /reviews/{review}/edit
**Auth**: Requerido + Autor
**Nombre**: `reviews.edit`

**Respuesta**: Vista con formulario

---

### PUT /reviews/{review}
**Auth**: Requerido + Autor
**Nombre**: `reviews.update`

**Parámetros**: Igual que `store`

**Respuesta**: Redirect a `reviews.show`

---

### DELETE /reviews/{review}
**Auth**: Requerido + Autor o Admin
**Nombre**: `reviews.destroy`

**Respuesta**: Redirect a `networks.reviews.index`

---

### POST /networks/{network}/reviews/filter
**Auth**: Requerido + Membership
**Nombre**: `networks.reviews.filter`

**Parámetros**:
```json
{
    "rating_min": "integer|nullable|min:1|max:5",
    "cuisine_type": "string|nullable",
    "price_range": "array|nullable",
    "date_from": "date|nullable",
    "date_to": "date|nullable",
    "city": "string|nullable",
    "tags": "array|nullable",
    "search": "string|nullable"
}
```

**Respuesta JSON**:
```json
{
    "data": [
        {
            "id": 1,
            "restaurant": {...},
            "user": {...},
            "rating": 5,
            "comment": "...",
            "date_of_visit": "2024-01-15"
        }
    ],
    "meta": {
        "total": 50,
        "per_page": 20,
        "current_page": 1
    }
}
```

---

## 💬 RUTAS DE COMENTARIOS

### POST /reviews/{review}/comments
**Auth**: Requerido + Membership
**Nombre**: `reviews.comments.store`

**Parámetros**:
```json
{
    "body": "string|required|max:500",
    "parent_id": "integer|nullable|exists:review_comments,id"
}
```

**Respuesta**: Redirect back

---

### DELETE /comments/{comment}
**Auth**: Requerido + Autor o Admin
**Nombre**: `comments.destroy`

**Respuesta**: Redirect back

---

## 🍽️ RUTAS DE RESTAURANTES

### GET /restaurants
**Auth**: Requerido
**Nombre**: `restaurants.index`

**Query params**:
- `search`: string
- `city`: string
- `cuisine`: string

**Respuesta**: Vista con lista

---

### GET /restaurants/search
**Auth**: Requerido
**Nombre**: `restaurants.search`

**Query params**:
- `q`: string (término de búsqueda)

**Respuesta JSON**:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Restaurant Name",
            "address": "Street 123",
            "city": "Madrid",
            "cuisine_type": "Italian"
        }
    ]
}
```

**Uso**: Autocompletado en formularios

---

### POST /restaurants
**Auth**: Requerido
**Nombre**: `restaurants.store`

**Parámetros**:
```json
{
    "name": "string|required|max:255",
    "address": "string|nullable|max:500",
    "city": "string|nullable|max:255",
    "country": "string|nullable|max:255",
    "cuisine_type": "string|nullable|max:100",
    "price_range": "string|nullable|in:$,$$,$$$,$$$$",
    "phone": "string|nullable|max:50",
    "website": "string|nullable|url"
}
```

**Respuesta JSON**:
```json
{
    "data": {
        "id": 123,
        "name": "New Restaurant",
        ...
    }
}
```

---

### GET /restaurants/{restaurant}
**Auth**: Requerido
**Nombre**: `restaurants.show`

**Respuesta**: Vista con:
- Info del restaurante
- Reseñas (de redes del usuario)
- Estadísticas

---

## 📌 RUTAS DE "POR VISITAR"

### GET /networks/{network}/wishlist
**Auth**: Requerido + Membership
**Nombre**: `networks.wishlist`

**Respuesta**: Vista con lista de wishes

---

### POST /networks/{network}/wishlist
**Auth**: Requerido + Membership
**Nombre**: `networks.wishlist.store`

**Parámetros**:
```json
{
    "restaurant_id": "integer|required|exists:restaurants,id",
    "notes": "string|nullable|max:500",
    "priority": "string|nullable|in:low,medium,high"
}
```

**Respuesta**: Redirect back

---

### DELETE /wishlist/{wish}
**Auth**: Requerido + Autor
**Nombre**: `wishlist.destroy`

**Respuesta**: Redirect back

---

### POST /wishlist/{wish}/convert
**Auth**: Requerido + Autor
**Nombre**: `wishlist.convert`

**Parámetros**: Igual que crear reseña

**Respuesta**: Redirect a `reviews.show`

---

## 📊 RUTAS DE API (OPCIONAL)

Si se necesita API para móvil o integraciones:

### Todas las rutas en `/api/v1/`

**Headers requeridos**:
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

**Autenticación**: Laravel Sanctum

**Endpoints principales**:
```
GET    /api/v1/networks
POST   /api/v1/networks
GET    /api/v1/networks/{id}
GET    /api/v1/networks/{id}/reviews
POST   /api/v1/networks/{id}/reviews
GET    /api/v1/reviews/{id}
POST   /api/v1/reviews/{id}/comments
GET    /api/v1/restaurants/search?q=
```

**Formato de respuesta**:
```json
{
    "data": {...},
    "message": "Success",
    "meta": {...}
}
```

**Errores**:
```json
{
    "message": "Error message",
    "errors": {
        "field": ["Validation error"]
    }
}
```

---

## 🔒 MIDDLEWARES APLICADOS

### Global
- `web`: Sesiones, CSRF, cookies

### Rutas autenticadas
- `auth`: Usuario debe estar autenticado

### Rutas de redes
- `belongs-to-network`: Usuario debe ser miembro

### Verificaciones adicionales
- Policies en controllers via `authorize()`

---

## 📝 CONVENCIÓN DE NOMBRES

### Rutas web
```
networks.index          → GET /networks
networks.create         → GET /networks/create
networks.store          → POST /networks
networks.show           → GET /networks/{network}
networks.edit           → GET /networks/{network}/edit
networks.update         → PUT /networks/{network}
networks.destroy        → DELETE /networks/{network}

networks.reviews.index  → GET /networks/{network}/reviews
networks.reviews.create → GET /networks/{network}/reviews/create
...
```

### Rutas API
```
api.v1.networks.index   → GET /api/v1/networks
api.v1.networks.show    → GET /api/v1/networks/{id}
...
```

---

## 🧪 TESTING DE RUTAS

```php
// tests/Feature/NetworkRoutesTest.php
public function test_user_can_create_network()
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('networks.store'), [
            'name' => 'My Network',
            'description' => 'Test'
        ]);

    $response->assertRedirect(route('networks.show', 1));
    $this->assertDatabaseHas('networks', ['name' => 'My Network']);
}
```

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
