# 🔄 FLUJOS DE USUARIO
## Diagramas y Procesos Clave

---

## 🎯 PROPÓSITO

Este documento describe los flujos principales de usuario a través de la aplicación con diagramas ASCII simples.

---

## 1. FLUJO DE REGISTRO E INICIO DE SESIÓN

```
┌─────────────────┐
│  Landing Page   │
└────────┬────────┘
         │
         ├──→ Click "Registrarse"
         │    ↓
         │    ┌──────────────────┐
         │    │ Formulario       │
         │    │ - Email          │
         │    │ - Password       │
         │    │ - Nombre         │
         │    └────────┬─────────┘
         │             │
         │             ↓
         │    ┌──────────────────┐
         │    │ Validación       │
         │    └────────┬─────────┘
         │             │
         │             ├──→ Error → Volver a formulario
         │             │
         │             ↓ OK
         │    ┌──────────────────┐
         │    │ Cuenta creada    │
         │    │ Email enviado    │
         │    └────────┬─────────┘
         │             │
         │             ↓
         │    ┌──────────────────┐
         │    │ Dashboard        │
         │    └──────────────────┘
         │
         └──→ Click "Iniciar Sesión"
              ↓
              ┌──────────────────┐
              │ Login Form       │
              │ - Email          │
              │ - Password       │
              └────────┬─────────┘
                       │
                       ↓
              ┌──────────────────┐
              │ Autenticación    │
              └────────┬─────────┘
                       │
                       ├──→ Error → Volver a login
                       │
                       ↓ OK
              ┌──────────────────┐
              │ Dashboard        │
              └──────────────────┘
```

---

## 2. FLUJO DE CREACIÓN DE RED

```
Usuario autenticado
        │
        ↓
┌──────────────────┐
│ Dashboard        │
└────────┬─────────┘
         │
         ↓ Click "Crear Red"
┌──────────────────┐
│ Formulario Red   │
│ - Nombre         │
│ - Descripción    │
│ - Logo (opt)     │
└────────┬─────────┘
         │
         ↓ Submit
┌──────────────────┐
│ Validación       │
└────────┬─────────┘
         │
         ├──→ Error → Volver
         │
         ↓ OK
┌──────────────────┐
│ NetworkService   │
│ .createNetwork() │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Red creada       │
│ User = Owner     │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Dashboard Red    │
│ (vacío, sin      │
│  miembros)       │
└────────┬─────────┘
         │
         ├──→ Invitar miembros
         ├──→ Añadir primera reseña
         └──→ Configurar red
```

---

## 3. FLUJO DE INVITACIÓN Y ACEPTACIÓN

```
Admin/Owner de Red
        │
        ↓ Click "Invitar"
┌──────────────────┐
│ Form Invitación  │
│ - Email          │
│ - Rol (admin/    │
│   member)        │
└────────┬─────────┘
         │
         ↓ Submit
┌──────────────────┐
│ NetworkService   │
│ .inviteMember()  │
└────────┬─────────┘
         │
         ├──→ Email no existe → Enviar invitación por email
         │                      ↓
         │              ┌──────────────────┐
         │              │ Email con link   │
         │              │ /invite/{token}  │
         │              └────────┬─────────┘
         │                       │
         │                       ↓
         │              ┌──────────────────┐
         │              │ Destinatario     │
         │              │ click link       │
         │              └────────┬─────────┘
         │                       │
         │                       ├──→ No registrado → Registro + Aceptar
         │                       │
         │                       └──→ Registrado → Login + Aceptar
         │                                         ↓
         │                                ┌──────────────────┐
         │                                │ Aceptar          │
         │                                │ invitación       │
         │                                └────────┬─────────┘
         │                                         │
         │                                         ↓
         └──→ Email existe → Notificación interna
                            ↓
                    ┌──────────────────┐
                    │ Usuario ve       │
                    │ notificación     │
                    └────────┬─────────┘
                             │
                             ↓ Click "Aceptar"
                    ┌──────────────────┐
                    │ Membership       │
                    │ creada           │
                    └────────┬─────────┘
                             │
                             ↓
                    ┌──────────────────┐
                    │ Acceso a red     │
                    │ Dashboard        │
                    └──────────────────┘
```

---

## 4. FLUJO DE CREACIÓN DE RESEÑA

```
Usuario en Red
        │
        ↓ Click "Nueva Reseña"
┌──────────────────┐
│ Buscar           │
│ Restaurante      │
└────────┬─────────┘
         │
         ├──→ Existe → Seleccionar
         │    ↓
         │    ┌──────────────────┐
         │    │ Form Reseña      │
         │    │ - Rating (1-5)   │
         │    │ - Comentario     │
         │    │ - Fecha visita   │
         │    │ - Tipo comida    │
         │    │ - Precio         │
         │    │ - Fotos          │
         │    │ - Ticket         │
         │    └────────┬─────────┘
         │             │
         │             ↓ Submit
         │    ┌──────────────────┐
         │    │ Validación       │
         │    └────────┬─────────┘
         │             │
         │             ├──→ Error → Volver
         │             │
         │             ↓ OK
         │    ┌──────────────────┐
         │    │ ReviewService    │
         │    │ .createReview()  │
         │    └────────┬─────────┘
         │             │
         │             ↓
         │    ┌──────────────────┐
         │    │ MediaService     │
         │    │ .attachPhotos()  │
         │    │ .attachTicket()  │
         │    └────────┬─────────┘
         │             │
         │             ↓
         │    ┌──────────────────┐
         │    │ Notification     │
         │    │ Service          │
         │    └────────┬─────────┘
         │             │
         │             ↓
         │    ┌──────────────────┐
         │    │ Reseña creada    │
         │    │ Vista detalle    │
         │    └──────────────────┘
         │
         └──→ No existe → Crear restaurante
              ↓
              ┌──────────────────┐
              │ Form Restaurant  │
              │ - Nombre         │
              │ - Dirección      │
              │ - Tipo cocina    │
              │ - Precio         │
              └────────┬─────────┘
                       │
                       ↓ Submit
              ┌──────────────────┐
              │ RestaurantService│
              │ .createRestaurant│
              └────────┬─────────┘
                       │
                       ↓
              Continúa con Form Reseña (arriba)
```

---

## 5. FLUJO DE COMENTARIOS

```
Usuario ve Reseña
        │
        ↓ Click "Comentar"
┌──────────────────┐
│ Textarea         │
│ comentario       │
└────────┬─────────┘
         │
         ↓ Submit
┌──────────────────┐
│ Validación       │
│ - Usuario en red │
│ - Texto válido   │
└────────┬─────────┘
         │
         ├──→ Error → Mensaje error
         │
         ↓ OK
┌──────────────────┐
│ Crear            │
│ ReviewComment    │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Notificar autor  │
│ de la reseña     │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Comentario       │
│ visible          │
└──────────────────┘
         │
         └──→ Opción "Responder" → Crear comentario hijo
                                    (parent_id = comentario_id)
```

---

## 6. FLUJO "POR VISITAR"

```
Usuario navegando
        │
        ├──→ Ve restaurante interesante
        │    ↓
        │    Click "Añadir a Por Visitar"
        │    ↓
        │    ┌──────────────────┐
        │    │ Modal/Form       │
        │    │ - Notas (opt)    │
        │    │ - Prioridad      │
        │    └────────┬─────────┘
        │             │
        │             ↓
        │    ┌──────────────────┐
        │    │ VisitWishService │
        │    │ .addToWishlist() │
        │    └────────┬─────────┘
        │             │
        │             ↓
        │    ┌──────────────────┐
        │    │ Añadido a lista  │
        │    └──────────────────┘
        │
        └──→ En lista "Por Visitar"
             ↓
             Click restaurante
             ↓
             ┌──────────────────┐
             │ Vista detalle    │
             │ + botón "Ya fui" │
             └────────┬─────────┘
                      │
                      ↓ Click "Ya fui"
             ┌──────────────────┐
             │ Form Reseña      │
             │ (prellenado con  │
             │  restaurante)    │
             └────────┬─────────┘
                      │
                      ↓ Submit
             ┌──────────────────┐
             │ VisitWishService │
             │ .convertToReview │
             └────────┬─────────┘
                      │
                      ├──→ Marcar wish como visitado
                      ↓
             ┌──────────────────┐
             │ Reseña creada    │
             └──────────────────┘
```

---

## 7. FLUJO DE MAPA

```
Usuario en Dashboard Red
        │
        ↓ Click "Mapa"
┌──────────────────┐
│ Vista Mapa       │
│ (Leaflet/GMaps)  │
└────────┬─────────┘
         │
         ↓ Al cargar
┌──────────────────┐
│ AJAX Request     │
│ /network/{id}/   │
│ map-data         │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ MapService       │
│ .getNetworkMap   │
│ Data()           │
└────────┬─────────┘
         │
         ↓ Retorna JSON
┌──────────────────┐
│ Marcadores       │
│ - Visitados      │
│   (green)        │
│ - Por visitar    │
│   (blue)         │
└────────┬─────────┘
         │
         ↓ Renderizar
┌──────────────────┐
│ Mapa con pins    │
└────────┬─────────┘
         │
         ├──→ Click marcador
         │    ↓
         │    ┌──────────────────┐
         │    │ Popup con info   │
         │    │ - Nombre         │
         │    │ - Rating         │
         │    │ - Link detalle   │
         │    └──────────────────┘
         │
         ├──→ Mover mapa / Zoom
         │    ↓
         │    ┌──────────────────┐
         │    │ Actualizar bounds│
         │    │ Filtrar markers  │
         │    └──────────────────┘
         │
         └──→ Aplicar filtros
              ↓
              ┌──────────────────┐
              │ Recargar markers │
              │ filtrados        │
              └──────────────────┘
```

---

## 8. FLUJO DE FILTROS Y BÚSQUEDA

```
Usuario en lista de Reseñas
        │
        ↓ Abre panel filtros
┌──────────────────┐
│ Sidebar Filtros  │
│ - Rating min     │
│ - Tipo cocina    │
│ - Precio         │
│ - Fecha          │
│ - Ciudad         │
│ - Tags           │
└────────┬─────────┘
         │
         ↓ Selecciona opciones
┌──────────────────┐
│ Estado filtros   │
│ en JS            │
└────────┬─────────┘
         │
         ↓ Click "Aplicar"
┌──────────────────┐
│ AJAX POST        │
│ /network/{id}/   │
│ reviews/filter   │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ ReviewService    │
│ .getFiltered     │
│ Reviews()        │
└────────┬─────────┘
         │
         ↓ Query con filtros
┌──────────────────┐
│ Reviews          │
│ Collection       │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ ReviewResource   │
│ ::collection()   │
└────────┬─────────┘
         │
         ↓ JSON
┌──────────────────┐
│ Actualizar DOM   │
│ con resultados   │
└────────┬─────────┘
         │
         └──→ Si hay búsqueda de texto
              ↓
              ┌──────────────────┐
              │ Buscar en:       │
              │ - Nombre rest.   │
              │ - Comentarios    │
              │ - Tags           │
              └──────────────────┘
```

---

## 9. FLUJO DE PERMISOS

```
Request recibido
        │
        ↓
┌──────────────────┐
│ Middleware       │
│ 'auth'           │
└────────┬─────────┘
         │
         ├──→ No autenticado → Redirect /login
         │
         ↓ Autenticado
┌──────────────────┐
│ Route Model      │
│ Binding          │
│ (Network)        │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Middleware       │
│ 'belongs-to-     │
│  network'        │
└────────┬─────────┘
         │
         ├──→ No es miembro → 403 Forbidden
         │
         ↓ Es miembro
┌──────────────────┐
│ Controller       │
│ authorize()      │
└────────┬─────────┘
         │
         ↓
┌──────────────────┐
│ Policy           │
│ verificar acción │
└────────┬─────────┘
         │
         ├──→ No permitido → 403
         │
         ↓ Permitido
┌──────────────────┐
│ Ejecutar acción  │
└──────────────────┘
```

---

**Última actualización**: 2025-11-15
**Versión**: 1.0.0
