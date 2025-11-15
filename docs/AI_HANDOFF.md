# 🤖 GUÍA DE HANDOFF PARA IAs
## Documento de Conexión para Asistentes de IA

---

## 📋 INFORMACIÓN CRÍTICA DE INICIO

### ¿QUÉ ES ESTE PROYECTO?
**Plataforma de Reseñas Gastronómicas Privadas** basada en Laravel 11 con arquitectura modular y sistema de skins.

**Concepto central**: Cada usuario puede crear su propia **red privada** donde invita a amigos/familia para compartir reseñas de restaurantes. No es público, es íntimo y privado.

---

## 🎯 ESTADO ACTUAL DEL PROYECTO

**FASE ACTUAL**: FASE 0 - FUNDAMENTOS

Para verificar la fase actual, revisa:
1. El archivo `/docs/PROJECT_STATUS.md` (si existe)
2. Las migraciones en `/database/migrations/`
3. Los modelos existentes en `/app/Models/`

**IMPORTANTE**: NO avances a la siguiente fase hasta que la actual esté 100% completa y validada.

---

## 📁 UBICACIÓN DE ARCHIVOS CLAVE

### Documentación Técnica
```
/docs/
├── AI_HANDOFF.md          ← ESTÁS AQUÍ (lee esto primero)
├── PROJECT_STRUCTURE.md   ← Mapa completo de ubicaciones
├── architecture.md        ← Decisiones de arquitectura
├── database.md            ← Esquemas y relaciones
├── services.md            ← Servicios disponibles
├── flows.md               ← Flujos de usuario
├── skins.md               ← Sistema de temas
├── endpoints.md           ← API y rutas
└── DEVELOPMENT_GUIDE.md   ← Guía de desarrollo
```

### Código Laravel
```
/app/
├── Models/                ← Eloquent models
├── Services/              ← Lógica de negocio
├── Http/
│   ├── Controllers/       ← Controllers (lógica mínima)
│   ├── Requests/          ← FormRequests (validaciones)
│   └── Resources/         ← API Resources
├── Policies/              ← Autorización
└── View/
    └── Components/        ← Blade components

/resources/
└── views/
    ├── skins/             ← Vistas por skin
    │   ├── listox/        ← Skin por defecto
    │   ├── dark/
    │   └── pastel/
    ├── components/        ← Componentes compartidos
    └── pages/             ← Páginas principales

/database/
├── migrations/            ← Migraciones
└── seeders/              ← Datos de prueba

/routes/
├── web.php               ← Rutas web
└── api.php               ← Rutas API
```

---

## 🏗️ ARQUITECTURA FUNDAMENTAL

### Principios Clave

1. **MODULARIDAD**: Cada funcionalidad es un módulo independiente
2. **SERVICIOS**: Lógica de negocio SIEMPRE en Services, NO en Controllers
3. **COMPONENTES**: UI reutilizable mediante Blade Components
4. **SKINS**: Separación total de lógica y presentación
5. **PRIVACIDAD**: Todo gira alrededor de redes privadas

### Patrón de Desarrollo

```
Usuario hace request
    ↓
Controller (validación mínima)
    ↓
FormRequest (validación completa)
    ↓
Service (lógica de negocio)
    ↓
Model (acceso a datos)
    ↓
Response (Resource o View)
```

---

## 🔑 CONCEPTOS ESENCIALES

### 1. REDES (Networks)
- Una red es un grupo privado de personas
- Cada red es independiente
- Los datos NO se comparten entre redes
- Cada red tiene su propio feed, restaurantes, reseñas

### 2. MEMBRESÍAS (Memberships)
- Tabla pivot entre Users y Networks
- Define roles: owner, admin, member
- Controla permisos dentro de la red

### 3. RESTAURANTES (Restaurants)
- Pueden estar en múltiples redes
- Tienen coordenadas (lat/lng) para mapas
- Búsqueda con autocompletado

### 4. RESEÑAS (Reviews)
- Pertenecen a una red
- Incluyen: rating, comentario, fotos, ticket
- Solo visibles para miembros de la red

### 5. SKINS
- Cambian la apariencia sin tocar lógica
- Configurado en `.env`: `APP_SKIN=listox`
- Función helper: `skin('path')`

---

## ⚙️ COMANDOS ÚTILES

### Verificar estado
```bash
# Ver migraciones aplicadas
php artisan migrate:status

# Ver rutas
php artisan route:list

# Ver modelos
ls -la app/Models/

# Ver qué skin está activo
cat .env | grep APP_SKIN
```

### Desarrollo
```bash
# Crear migración
php artisan make:migration create_X_table

# Crear modelo con todo
php artisan make:model X -mfsc

# Crear servicio
php artisan make:class Services/XService

# Crear componente Blade
php artisan make:component XComponent
```

---

## 📋 FASES DEL PROYECTO

| Fase | Nombre | Estado | Prioridad |
|------|--------|--------|-----------|
| 0 | Fundamentos | 🔄 EN CURSO | CRÍTICA |
| 1 | Autenticación | ⏳ PENDIENTE | CRÍTICA |
| 2 | Sistema de Redes | ⏳ PENDIENTE | CRÍTICA |
| 3 | Restaurantes | ⏳ PENDIENTE | ALTA |
| 4 | Reseñas | ⏳ PENDIENTE | ALTA |
| 5 | Comentarios | ⏳ PENDIENTE | MEDIA |
| 6 | Lista "Por Visitar" | ⏳ PENDIENTE | MEDIA |
| 7 | Mapa de la Red | ⏳ PENDIENTE | ALTA |
| 8 | Filtros y Búsquedas | ⏳ PENDIENTE | MEDIA |
| 9 | Super Sistema | ⏳ PENDIENTE | BAJA |
| 10 | Skins | ⏳ PENDIENTE | MEDIA |
| 11 | Testing | ⏳ PENDIENTE | ALTA |
| 12 | Deploy | ⏳ PENDIENTE | CRÍTICA |

---

## 🚨 REGLAS ESTRICTAS

### ❌ NUNCA HACER

1. **NO avances de fase sin validar la anterior**
2. **NO pongas lógica de negocio en Controllers**
3. **NO hardcodees estilos en Blade (usa skins)**
4. **NO mezcles datos entre redes**
5. **NO ignores permisos y policies**
6. **NO crees archivos sin seguir la estructura**
7. **NO hagas queries N+1 (usa eager loading)**
8. **NO expongas datos privados en APIs**

### ✅ SIEMPRE HACER

1. **SIEMPRE usa Services para lógica compleja**
2. **SIEMPRE valida con FormRequests**
3. **SIEMPRE usa Blade Components**
4. **SIEMPRE respeta la privacidad de redes**
5. **SIEMPRE documenta cambios importantes**
6. **SIEMPRE sigue PSR-12**
7. **SIEMPRE usa eager loading**
8. **SIEMPRE prueba antes de avanzar**

---

## 🎨 SISTEMA DE SKINS

### Cómo funciona

```php
// En .env
APP_SKIN=listox

// En config/app.php
'skin' => env('APP_SKIN', 'listox'),

// Helper global
skin('components.card')
// → 'skins.listox.components.card'

// En Blade
@include(skin('components.card'))
```

### Estructura de un Skin

```
resources/views/skins/listox/
├── layouts/
│   ├── app.blade.php
│   └── guest.blade.php
├── components/
│   ├── card.blade.php
│   ├── button.blade.php
│   └── form/
│       ├── input.blade.php
│       └── textarea.blade.php
├── pages/
│   ├── dashboard.blade.php
│   └── network/
│       └── show.blade.php
└── partials/
    ├── header.blade.php
    └── footer.blade.php
```

---

## 🔐 SEGURIDAD Y PERMISOS

### Roles por Red

- **owner**: Creador de la red, todos los permisos
- **admin**: Puede invitar, expulsar, moderar
- **member**: Puede ver, crear reseñas, comentar

### Verificación de Permisos

```php
// En Controllers
$this->authorize('view', $network);

// En Blade
@can('update', $network)
    ...
@endcan

// En código
if ($user->can('delete', $review)) {
    ...
}
```

---

## 📊 MODELOS PRINCIPALES

### Relaciones Clave

```
User
├── belongsToMany(Network) through Membership
├── hasMany(Review)
├── hasMany(ReviewComment)
└── hasMany(VisitWish)

Network
├── belongsToMany(User) through Membership
├── hasMany(Review)
├── hasMany(VisitWish)
└── hasMany(Invitation)

Restaurant
├── hasMany(Review)
├── hasMany(VisitWish)
└── belongsToMany(Network) through reviews

Review
├── belongsTo(User)
├── belongsTo(Network)
├── belongsTo(Restaurant)
├── hasMany(ReviewComment)
└── morphMany(Media) via Spatie MediaLibrary
```

---

## 📝 FLUJO DE TRABAJO RECOMENDADO

### Cuando recibes una petición:

1. **Lee** `PROJECT_STRUCTURE.md` para ubicarte
2. **Verifica** fase actual en migraciones/modelos
3. **Consulta** el documento de la fase en `/docs/`
4. **Planifica** los pasos necesarios
5. **Implementa** siguiendo buenas prácticas
6. **Valida** que funciona correctamente
7. **Documenta** los cambios realizados
8. **Actualiza** el estado de la fase

### Ejemplo de Respuesta Estructurada:

```
## Entendido: [descripción de la petición]

### Estado Actual
- Fase: X
- Archivos existentes: [lista]
- Próximo paso: [acción]

### Plan de Implementación
1. Paso 1
2. Paso 2
3. Paso 3

### Código Generado
[código con explicaciones]

### Validación
[cómo probar que funciona]

### Próximos Pasos
[qué sigue]
```

---

## 🧪 TESTING

### Por cada funcionalidad crear:

```
tests/
├── Feature/
│   ├── Auth/
│   ├── Network/
│   ├── Review/
│   └── ...
└── Unit/
    ├── Services/
    └── Models/
```

### Ejecutar tests

```bash
php artisan test
php artisan test --filter=NetworkTest
```

---

## 🆘 DEBUGGING

### Logs importantes

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Query log (activar en AppServiceProvider)
DB::enableQueryLog();
dd(DB::getQueryLog());
```

### Telescope (si está instalado)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

Acceder: `/telescope`

---

## 📚 RECURSOS ADICIONALES

### Documentos a leer en orden:

1. **AI_HANDOFF.md** ← Estás aquí
2. **PROJECT_STRUCTURE.md** ← Mapa de ubicaciones
3. **DEVELOPMENT_GUIDE.md** ← Guía paso a paso
4. **architecture.md** ← Decisiones arquitectónicas
5. **database.md** ← Esquemas de BD
6. **services.md** ← Servicios disponibles
7. **flows.md** ← Flujos de usuario
8. **skins.md** ← Sistema de temas
9. **endpoints.md** ← Rutas y APIs

### Enlaces Laravel

- [Laravel 11 Docs](https://laravel.com/docs/11.x)
- [Spatie MediaLibrary](https://spatie.be/docs/laravel-medialibrary)
- [Spatie Permissions](https://spatie.be/docs/laravel-permission)
- [Livewire 3](https://livewire.laravel.com)

---

## 🎯 CHECKLIST ANTES DE RESPONDER

Antes de dar cualquier respuesta, verifica:

- [ ] He leído PROJECT_STRUCTURE.md
- [ ] Sé en qué fase estamos
- [ ] Entiendo qué se pide
- [ ] Conozco la ubicación de archivos relevantes
- [ ] Mi respuesta sigue las buenas prácticas
- [ ] No rompo la arquitectura establecida
- [ ] Respeto el sistema de skins
- [ ] Respeto la privacidad de redes
- [ ] Incluyo validaciones y permisos
- [ ] Documento los cambios

---

## 💡 TIPS FINALES

1. **Lee primero, codifica después**: Siempre revisa la documentación antes de actuar
2. **Pregunta si hay dudas**: Mejor aclarar que asumir
3. **Coherencia sobre rapidez**: Es mejor ir lento y bien que rápido y mal
4. **Documenta mientras codificas**: No dejes la documentación para después
5. **Piensa en el próximo desarrollador**: Escribe código que tú entenderías en 6 meses

---

## 📞 CONTACTO Y AYUDA

Si algo no está claro:
1. Revisa `/docs/PROJECT_STRUCTURE.md`
2. Busca en los otros documentos de `/docs/`
3. Consulta con el desarrollador principal
4. NO asumas, pregunta

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
**Mantenido por**: Sistema de IAs del proyecto

---

## ⏭️ PRÓXIMO PASO

**Lee ahora**: `/docs/PROJECT_STRUCTURE.md`

Este documento te mostrará exactamente dónde está cada cosa en el proyecto.
