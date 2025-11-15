# 🍽️ PLATAFORMA DE RESEÑAS GASTRONÓMICAS PRIVADAS

> Crea tu propia red privada de reseñas gastronómicas con amigos y familia

---

## 📋 TABLA DE CONTENIDOS

- [Acerca del Proyecto](#-acerca-del-proyecto)
- [Características Principales](#-características-principales)
- [Tecnologías Utilizadas](#️-tecnologías-utilizadas)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Documentación](#-documentación)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Desarrollo](#-desarrollo)
- [Testing](#-testing)
- [Despliegue](#-despliegue)
- [Roadmap](#️-roadmap)
- [Contribuir](#-contribuir)

---

## 🎯 ACERCA DEL PROYECTO

Esta plataforma permite a los usuarios crear **redes privadas** donde pueden compartir reseñas de restaurantes exclusivamente con amigos y familia. A diferencia de plataformas públicas, aquí la privacidad es fundamental.

### ¿Qué hace especial a este proyecto?

- **100% Privado**: Cada red es completamente independiente
- **Íntimo**: Solo las personas que invites verán las reseñas
- **Completo**: Calificaciones, fotos, tickets, comentarios, mapas
- **Flexible**: Sistema de skins para personalizar la apariencia
- **Organizado**: Lista de restaurantes "por visitar"

### Caso de uso típico

```
María crea la red "Familia González"
   ↓
Invita a su esposo, hijos y padres
   ↓
Todos comparten reseñas de restaurantes que visitan
   ↓
Pueden comentar, ver en mapa, filtrar por tipo de comida
   ↓
Nadie fuera de la familia puede ver esta información
```

---

## ✨ CARACTERÍSTICAS PRINCIPALES

### 🔐 Sistema de Redes Privadas
- Crear redes ilimitadas
- Invitar miembros por email
- Roles: Owner, Admin, Member
- Privacidad total entre redes

### 📝 Reseñas Completas
- Calificación de 1 a 5 estrellas
- Comentarios detallados
- Subir hasta 10 fotos
- Adjuntar ticket/factura
- Fecha de visita y tipo de comida
- Tags personalizados

### 🍴 Gestión de Restaurantes
- Búsqueda con autocompletado
- Crear nuevos restaurantes si no existen
- Información completa: dirección, tipo de cocina, rango de precio
- Geolocalización para mapas

### 💬 Interacción Social (Privada)
- Comentar reseñas
- Responder a comentarios (hilos)
- Sistema de notificaciones

### 📌 Lista "Por Visitar"
- Guardar restaurantes para visitar después
- Notas personales
- Prioridad (baja, media, alta)
- Convertir fácilmente en reseña tras visitar

### 🗺️ Mapas Interactivos
- Ver todos los restaurantes en mapa
- Marcadores diferenciados: visitados vs. por visitar
- Filtros en el mapa
- Click en marcador para ver detalles

### 🔍 Filtros Avanzados
- Por calificación mínima
- Por tipo de cocina
- Por rango de precio
- Por fecha de visita
- Por ciudad
- Por tags
- Búsqueda de texto libre

### 🎨 Sistema de Skins
- Cambiar apariencia sin modificar código
- Tema base: **Listox** (profesional)
- Temas adicionales: Dark, Pastel
- White-label ready

### 🔒 Seguridad y Permisos
- Autenticación robusta (Laravel Breeze)
- Policies para cada acción
- Middleware de verificación de membresía
- Protección CSRF
- Validaciones exhaustivas

---

## 🛠️ TECNOLOGÍAS UTILIZADAS

### Backend
- **Laravel 11** - Framework PHP
- **MySQL/MariaDB** - Base de datos
- **Spatie MediaLibrary** - Gestión de archivos
- **Spatie Permissions** - Sistema de roles y permisos

### Frontend
- **Blade Components** - Templating
- **Tailwind CSS** - Estilos (vía Listox)
- **JavaScript Vanilla** - Interactividad
- **Livewire 3** (opcional) - Componentes reactivos

### Mapas
- **Leaflet** o **Google Maps** - Visualización de mapas

### Herramientas
- **Composer** - Gestor de dependencias PHP
- **NPM** - Gestor de dependencias JS
- **Vite** - Build tool

---

## 📦 REQUISITOS

### Software necesario

- PHP >= 8.2
- Composer >= 2.5
- Node.js >= 18
- MySQL >= 8.0 o MariaDB >= 10.5
- Git

### Extensiones PHP requeridas

- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- GD o Imagick (para procesamiento de imágenes)

---

## 🚀 INSTALACIÓN

### 1. Clonar el repositorio

```bash
git clone https://github.com/pitiflautico/reviews.git
cd reviews
```

### 2. Instalar dependencias

```bash
# PHP
composer install

# JavaScript
npm install
```

### 3. Configurar entorno

```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar key de aplicación
php artisan key:generate
```

### 4. Configurar base de datos

Editar `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reviews
DB_USERNAME=root
DB_PASSWORD=tu_password

# Skin (listox es el default)
APP_SKIN=listox

# Si usas Google Maps
GOOGLE_MAPS_KEY=tu_api_key
```

### 5. Ejecutar migraciones y seeders

```bash
# Crear tablas
php artisan migrate

# (Opcional) Cargar datos de prueba
php artisan db:seed
```

### 6. Crear symlink para storage

```bash
php artisan storage:link
```

### 7. Compilar assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 8. Iniciar servidor de desarrollo

```bash
php artisan serve
```

Visita: http://localhost:8000

---

## 📚 DOCUMENTACIÓN

El proyecto cuenta con documentación exhaustiva en `/docs/`:

### Para Desarrolladores e IAs

| Documento | Propósito |
|-----------|-----------|
| **[AI_HANDOFF.md](docs/AI_HANDOFF.md)** | **LEER PRIMERO** - Guía completa para IAs y nuevos desarrolladores |
| **[PROJECT_STRUCTURE.md](docs/PROJECT_STRUCTURE.md)** | Mapa de ubicación de todos los archivos |
| **[DEVELOPMENT_GUIDE.md](docs/DEVELOPMENT_GUIDE.md)** | Guía paso a paso de desarrollo |

### Documentación Técnica

| Documento | Contenido |
|-----------|-----------|
| [architecture.md](docs/architecture.md) | Decisiones arquitectónicas y patrones |
| [database.md](docs/database.md) | Esquemas, relaciones, migraciones |
| [services.md](docs/services.md) | Servicios disponibles y uso |
| [flows.md](docs/flows.md) | Flujos de usuario (diagramas) |
| [skins.md](docs/skins.md) | Sistema de temas visuales |
| [endpoints.md](docs/endpoints.md) | Rutas web y API |

### Orden de lectura recomendado

1. `README.md` (este archivo)
2. `docs/AI_HANDOFF.md`
3. `docs/PROJECT_STRUCTURE.md`
4. `docs/DEVELOPMENT_GUIDE.md`
5. Resto según necesidad

---

## 📁 ESTRUCTURA DEL PROYECTO

```
reviews/
├── app/
│   ├── Http/
│   │   ├── Controllers/      ← Lógica mínima
│   │   ├── Requests/         ← Validaciones
│   │   └── Resources/        ← Transformadores API
│   ├── Models/               ← Eloquent models
│   ├── Policies/             ← Autorización
│   ├── Services/             ← LÓGICA DE NEGOCIO
│   └── View/Components/      ← Componentes Blade
│
├── database/
│   ├── migrations/           ← Migraciones
│   └── seeders/              ← Datos de prueba
│
├── docs/                     ← DOCUMENTACIÓN COMPLETA
│
├── public/
│   └── assets/               ← Assets por skin
│       ├── listox/
│       ├── dark/
│       └── pastel/
│
├── resources/
│   └── views/
│       └── skins/            ← Vistas por skin
│           ├── listox/       ← Default
│           ├── dark/
│           └── pastel/
│
├── routes/
│   ├── web.php               ← Rutas web
│   └── api.php               ← Rutas API
│
└── tests/
    ├── Feature/              ← Tests de features
    └── Unit/                 ← Tests unitarios
```

---

## 💻 DESARROLLO

### Comandos útiles

```bash
# Servidor de desarrollo
php artisan serve

# Compilar assets en watch mode
npm run dev

# Ejecutar tests
php artisan test

# Ver rutas
php artisan route:list

# Crear modelo completo
php artisan make:model Restaurant -mfsc

# Limpiar caché
php artisan optimize:clear
```

### Convenciones de código

- **PSR-12** para PHP
- **Controllers limpios** - Delegar a Services
- **Componentes Blade** para UI reutilizable
- **FormRequests** para validaciones
- **Policies** para autorización
- **Tests** para features críticas

### Workflow de desarrollo

1. Leer documentación pertinente
2. Crear rama: `git checkout -b feature/nombre`
3. Desarrollar siguiendo `DEVELOPMENT_GUIDE.md`
4. Escribir tests
5. Commit con mensajes descriptivos
6. Push y crear PR

---

## 🧪 TESTING

```bash
# Todos los tests
php artisan test

# Test específico
php artisan test --filter=ReviewTest

# Con coverage
php artisan test --coverage

# Solo Feature tests
php artisan test tests/Feature

# Solo Unit tests
php artisan test tests/Unit
```

### Tipos de tests

- **Feature**: Flujos completos de usuario
- **Unit**: Lógica de servicios y modelos
- **Browser** (opcional): Tests E2E con Laravel Dusk

---

## 🌐 DESPLIEGUE

### Requisitos del servidor

- PHP 8.2+
- Nginx o Apache
- MySQL/MariaDB
- Composer
- Node.js (para compilar assets)
- SSL (recomendado)

### Pasos de deploy

```bash
# 1. Clonar repo
git clone https://github.com/pitiflautico/reviews.git
cd reviews

# 2. Instalar dependencias
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Configurar .env
cp .env.example .env
php artisan key:generate

# 4. Migraciones
php artisan migrate --force

# 5. Storage link
php artisan storage:link

# 6. Optimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Permisos
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Variables de entorno críticas

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=...
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

---

## 🗺️ ROADMAP

### ✅ Fase 0 - Fundamentos (ACTUAL)
- [x] Documentación completa
- [ ] Instalación de Laravel
- [ ] Configuración base

### 🔄 Fase 1 - Autenticación
- [ ] Laravel Breeze
- [ ] Integración con Listox
- [ ] Reset de contraseña

### 📋 Fase 2 - Sistema de Redes
- [ ] CRUD de redes
- [ ] Sistema de invitaciones
- [ ] Roles y permisos
- [ ] Dashboard de red

### 🍴 Fase 3 - Restaurantes
- [ ] Modelo de restaurante
- [ ] Búsqueda con autocompletado
- [ ] Crear restaurante nuevo
- [ ] Geolocalización

### ⭐ Fase 4 - Reseñas
- [ ] CRUD de reseñas
- [ ] Subida de fotos (hasta 10)
- [ ] Subida de ticket
- [ ] Rating 1-5
- [ ] Tags

### 💬 Fase 5 - Comentarios
- [ ] Comentar reseñas
- [ ] Hilos de respuestas
- [ ] Notificaciones

### 📌 Fase 6 - "Por Visitar"
- [ ] Añadir a wishlist
- [ ] Gestión de wishlist
- [ ] Convertir a reseña

### 🗺️ Fase 7 - Mapas
- [ ] Integración Leaflet/GMaps
- [ ] Marcadores dinámicos
- [ ] Filtros en mapa
- [ ] Popups informativos

### 🔍 Fase 8 - Filtros y Búsqueda
- [ ] Filtros avanzados
- [ ] Búsqueda de texto
- [ ] Combinación de filtros
- [ ] Guardado de búsquedas

### 🌐 Fase 9 - Super Sistema
- [ ] Panel super admin
- [ ] Estadísticas globales
- [ ] Rankings agregados
- [ ] Gestión de redes

### 🎨 Fase 10 - Skins
- [ ] Sistema completo de skins
- [ ] Tema Listox
- [ ] Tema Dark
- [ ] Tema Pastel

### 🧪 Fase 11 - Testing
- [ ] Tests de cada fase
- [ ] Cobertura >80%
- [ ] Tests E2E

### 🚀 Fase 12 - Deploy
- [ ] Optimizaciones
- [ ] Configuración servidor
- [ ] CI/CD
- [ ] Monitoreo

---

## 🤝 CONTRIBUIR

### Cómo contribuir

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add: AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Convenciones de commits

```
Add: Nueva funcionalidad
Update: Mejora de funcionalidad existente
Fix: Corrección de bug
Refactor: Refactorización de código
Docs: Cambios en documentación
Test: Añadir o modificar tests
Style: Cambios de formato/estilo
```

### Reportar bugs

Usa GitHub Issues con:
- Descripción clara del problema
- Pasos para reproducir
- Comportamiento esperado vs. actual
- Screenshots si aplica
- Versión de PHP, Laravel, navegador

---

## 📄 LICENCIA

Este proyecto es privado y de uso interno.

---

## 👥 EQUIPO

- **Desarrollo**: Claude AI + Equipo de desarrollo
- **Diseño**: Basado en tema Listox

---

## 📞 SOPORTE

Para dudas o problemas:
1. Consulta la [documentación](docs/)
2. Revisa [issues existentes](https://github.com/pitiflautico/reviews/issues)
3. Crea un nuevo issue si es necesario

---

## 🙏 AGRADECIMIENTOS

- [Laravel](https://laravel.com) - Framework PHP
- [Spatie](https://spatie.be) - Paquetes de calidad
- [Listox Theme](https://themegavias.com/wp/listox/) - Diseño base
- Comunidad de Laravel

---

## 📊 ESTADO DEL PROYECTO

**Fase actual**: Fase 0 - Fundamentos
**Progreso**: 5%
**Última actualización**: 2025-11-15

---

**¿Listo para empezar?**

👉 **Lee primero**: [docs/AI_HANDOFF.md](docs/AI_HANDOFF.md)

---

Hecho con ❤️ para compartir experiencias gastronómicas con quien más quieres
