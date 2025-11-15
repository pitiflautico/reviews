# Gastronomy Reviews Platform - Development Documentation

## Project Overview

A Laravel 11 gastronomy reviews platform that allows users to create private networks and share restaurant reviews with their friends and family.

### Core Concept
- **Global Restaurant Catalog**: Centralized database of restaurants used across all networks
- **Private Networks**: Users can create and manage private review networks
- **Social Reviews**: Members share restaurant experiences within their networks

---

## Current Status (Phase 0 - 2025-01-15)

### ✅ Completed Features

#### 1. Authentication System
- Google OAuth integration via Laravel Socialite
- Custom login/register pages with modern UI design
- Session management

#### 2. Network Management
- **Create Networks**: Users can create private review networks
- **Network Settings**: Owners can configure network properties
- **Member Roles**: Three-tier permission system
  - **Owner**: Full control, can promote admins, manage all members
  - **Admin**: Can manage members (except other admins), invite users
  - **Member**: Can view reviews, create reviews, optionally invite members

#### 3. Member Management System
- **Invite Members**: Add users by email (must be registered)
- **Role Management**: Owners can promote/demote members
- **Remove Members**: Admins and owners can remove members
- **Permission Controls**:
  - `allow_member_invites` setting controls if regular members can invite
  - Only owners can promote to admin
  - Admins cannot remove other admins

**Member Management UI Locations:**
- Network show page header: "Miembros" button
- Network stats card: "Invitar" button (if permitted)
- Members tab: "Invitar Miembro" button (if permitted)
- Members index page: Full member management interface

#### 4. Restaurant Management
- Global restaurant catalog (shared across networks)
- Restaurant fields:
  - `name` (required)
  - `address` (required)
  - `city` (optional)
  - `cuisine_type` (required)
  - `latitude` (optional)
  - `longitude` (optional)

#### 5. Review System
- **Create Reviews**: Two-mode review creation
  - **Select Existing Restaurant**: Choose from global catalog
  - **Create New Restaurant**: Add restaurant on-the-fly with geolocation
- **Geolocation Integration**:
  - HTML5 Geolocation API for current position
  - Nominatim/OpenStreetMap reverse geocoding for address
  - Auto-fills: address, city, latitude, longitude
- **Review Fields**:
  - Rating (1-5 stars, required)
  - Comment (required)
  - Date of visit (optional)
  - Meal type (breakfast/lunch/dinner, optional)
- **View Reviews**:
  - Network dashboard with recent reviews
  - Individual review pages
  - Reviews tab in network view

#### 6. Modern UI Design
- Listox-inspired landing page
- Gradient cards and buttons
- Responsive design (mobile, tablet, desktop)
- Tab-based navigation in network view
- Modern dropdown components (no dark mode)
- Statistics cards with icons and gradients

---

## Database Schema

### Core Tables

#### `users`
```
- id (primary key)
- name
- email
- password (nullable - OAuth users)
- avatar (nullable)
- google_id (nullable)
- timestamps
```

#### `networks`
```
- id (primary key)
- name
- description (nullable)
- owner_id (foreign key -> users.id)
- allow_member_invites (boolean, default false)
- timestamps
```

#### `restaurants`
```
- id (primary key)
- name
- address
- city (nullable)
- cuisine_type
- latitude (nullable)
- longitude (nullable)
- timestamps
```

#### `reviews`
```
- id (primary key)
- network_id (foreign key -> networks.id)
- user_id (foreign key -> users.id)
- restaurant_id (foreign key -> restaurants.id)
- rating (integer 1-5)
- comment (text)
- date_of_visit (date, nullable)
- meal_type (enum: breakfast/lunch/dinner, nullable)
- timestamps
```

#### `memberships` (pivot table)
```
- id (primary key)
- network_id (foreign key -> networks.id)
- user_id (foreign key -> users.id)
- role (enum: owner/admin/member)
- joined_at (timestamp)
- timestamps
```

---

## Routes Structure

### Authentication Routes
```php
// Laravel Breeze default routes + Google OAuth
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
```

### Network Routes
```php
Route::middleware('auth')->group(function () {
    // CRUD for networks
    Route::resource('networks', NetworkController::class);

    // Member management
    Route::get('networks/{network}/members', [NetworkMemberController::class, 'index'])
        ->name('networks.members.index');
    Route::get('networks/{network}/members/invite', [NetworkMemberController::class, 'invite'])
        ->name('networks.members.invite');
    Route::post('networks/{network}/members', [NetworkMemberController::class, 'store'])
        ->name('networks.members.store');
    Route::patch('networks/{network}/members/{user}/role', [NetworkMemberController::class, 'updateRole'])
        ->name('networks.members.updateRole');
    Route::delete('networks/{network}/members/{user}', [NetworkMemberController::class, 'destroy'])
        ->name('networks.members.destroy');

    // Reviews (nested under networks)
    Route::resource('networks.reviews', ReviewController::class);

    // Review comments (future feature)
    Route::post('networks/{network}/reviews/{review}/comments', [ReviewCommentController::class, 'store'])
        ->name('networks.reviews.comments.store');
});
```

---

## Key Controllers

### NetworkController
**Location**: `app/Http/Controllers/NetworkController.php`
- `index()`: List user's networks
- `create()`: Show network creation form
- `store()`: Create new network (user becomes owner)
- `show()`: Display network dashboard with stats and tabs
- `edit()`: Network settings (owner only)
- `update()`: Update network settings
- `destroy()`: Delete network

### NetworkMemberController
**Location**: `app/Http/Controllers/NetworkMemberController.php`
- `index()`: List all network members with role badges
- `invite()`: Show invite member form
- `store()`: Add member by email with permission checks
- `updateRole()`: Promote/demote members (owner only for admin promotion)
- `destroy()`: Remove members with permission checks

### ReviewController
**Location**: `app/Http/Controllers/ReviewController.php`
- `index()`: List all reviews in network
- `create()`: Show review creation form with restaurant toggle
- `store()`: Create review + optionally create restaurant
  - Dynamic validation based on `restaurant_option` field
  - Creates restaurant inline if `restaurant_option === 'new'`
- `show()`: Display individual review
- `edit()`: Edit review form (author or network owner/admin)
- `update()`: Update review
- `destroy()`: Delete review

---

## Permission System

### Member Role Hierarchy
1. **Owner** (one per network)
   - All permissions
   - Can promote/demote admins
   - Can delete network
   - Cannot be removed or demoted

2. **Admin** (multiple allowed)
   - Can invite members
   - Can remove regular members
   - Cannot remove other admins
   - Cannot promote to admin
   - Can manage network settings (future)

3. **Member** (default role)
   - Can create reviews
   - Can view all reviews in network
   - Can invite members (if `allow_member_invites` enabled)

### Permission Checks

**Invite Members:**
```php
if (!in_array($memberRole, ['owner', 'admin'])) {
    if (!$network->allow_member_invites || $memberRole !== 'member') {
        abort(403);
    }
}
```

**Promote to Admin:**
```php
if ($validated['role'] === 'admin' && $memberRole !== 'owner') {
    abort(403, 'Solo el propietario puede promover administradores.');
}
```

**Remove Members:**
```php
// Cannot remove owner
if ($userRole === 'owner') {
    abort(403, 'No se puede eliminar al propietario.');
}

// Admins cannot remove other admins
if ($memberRole === 'admin' && $userRole === 'admin') {
    abort(403, 'Los administradores no pueden eliminar a otros administradores.');
}
```

---

## Important Model Methods

### Network Model
**Location**: `app/Models/Network.php`

```php
// Get user's role in this network
public function getMemberRole(User $user): ?string
{
    $membership = $this->members()
        ->where('user_id', $user->id)
        ->first();

    return $membership?->pivot->role;
}

// Relationships
public function owner() // belongsTo User
public function members() // belongsToMany User (memberships pivot)
public function reviews() // hasMany Review
```

### User Model
**Location**: `app/Models/User.php`

```php
// Relationships
public function ownedNetworks() // hasMany Network
public function networks() // belongsToMany Network (memberships pivot)
public function reviews() // hasMany Review
```

---

## Views Structure

### Layouts
- `resources/views/layouts/app.blade.php`: Main authenticated layout
- `resources/views/layouts/guest.blade.php`: Guest layout (login/register)

### Network Views
- `resources/views/networks/index.blade.php`: Network grid
- `resources/views/networks/create.blade.php`: Create network form
- `resources/views/networks/show.blade.php`: Network dashboard with tabs
  - Stats cards (Members, Reviews, Restaurants)
  - Reviews tab (recent reviews grid)
  - Members tab (member cards with invite button)
- `resources/views/networks/edit.blade.php`: Network settings

### Member Management Views
- `resources/views/networks/members/index.blade.php`: Full member list with actions
- `resources/views/networks/members/invite.blade.php`: Invite member form

### Review Views
- `resources/views/reviews/create.blade.php`: Review creation with restaurant toggle
  - Alpine.js for dynamic form switching
  - Geolocation button with Nominatim integration
  - Two modes: select existing or create new restaurant
- `resources/views/reviews/show.blade.php`: Individual review display
- `resources/views/reviews/edit.blade.php`: Edit review form

### Blade Components
- `resources/views/components/dropdown.blade.php`: Modern dropdown (no dark mode)
- `resources/views/components/dropdown-link.blade.php`: Dropdown link item
- `resources/views/components/form-section.blade.php`: Form section with gradient header
- Custom icon components in `resources/views/components/icons/`

---

## Frontend Technologies

### CSS Framework
- **Tailwind CSS**: Utility-first CSS framework
- Custom gradient combinations:
  - Indigo-purple: Primary actions
  - Amber-orange: Reviews/ratings
  - Emerald-teal: Restaurants
  - Gray gradients: Cards and backgrounds

### JavaScript
- **Alpine.js**: Lightweight reactive framework
  - Tab switching in network view
  - Restaurant toggle in review creation
  - Dropdown menus

### Geolocation Integration
```javascript
// HTML5 Geolocation API
navigator.geolocation.getCurrentPosition(position => {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

    // Reverse geocoding with Nominatim
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            // Auto-fill address and city fields
        });
});
```

---

## Recent Fixes (2025-01-15)

### Database Table Name Fix
**Issue**: `NetworkMemberController` was using incorrect pivot table name
```php
// BEFORE (incorrect)
$members = $network->members()->orderBy('network_user.joined_at', 'desc')->get();

// AFTER (correct)
$members = $network->members()->orderBy('memberships.joined_at', 'desc')->get();
```

### UI Improvements
1. **Added "Invitar" button to Members stats card**
   - Compact design (text-xs, px-3 py-1)
   - White background with indigo text
   - Conditional display based on permissions

2. **Added "Invitar Miembro" button to Members tab**
   - Full-size gradient button
   - Positioned at top-right of tab content
   - Same permission checks as other invite buttons

---

## Pending Features / TODOs

### High Priority
1. **Review Comments System**
   - Model and migration already referenced in routes
   - Need to implement comment CRUD
   - Display comments on review detail page

2. **Network Statistics**
   - More detailed analytics in dashboard
   - Member contribution rankings
   - Most reviewed restaurants

3. **Restaurant Details Page**
   - Aggregate reviews across all networks
   - Show average rating
   - Display all reviews for a restaurant

### Medium Priority
4. **Search and Filters**
   - Search restaurants by name, city, cuisine
   - Filter reviews by rating, date, meal type
   - Sort options for reviews and members

5. **Image Uploads**
   - Restaurant photos
   - Review photos
   - Avatar uploads (currently only Google OAuth avatars)

6. **Notifications**
   - Email notifications for new invites
   - In-app notifications for new reviews

### Low Priority
7. **Export Features**
   - Export network reviews as PDF
   - CSV export of restaurant data

8. **Privacy Settings**
   - Private vs. public networks
   - Member visibility settings

---

## Development Environment

### Requirements
- PHP 8.2+
- Laravel 11
- MySQL 8.0+
- Node.js 18+ (for Vite)
- Composer

### Key Dependencies
```json
{
    "laravel/framework": "^11.0",
    "laravel/breeze": "^2.0",
    "laravel/socialite": "^5.0",
    "tailwindcss": "^3.4",
    "alpinejs": "^3.13"
}
```

### Environment Variables
```env
# Google OAuth
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/auth/google/callback

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reviews
DB_USERNAME=root
DB_PASSWORD=
```

---

## Git Workflow

### Current Branch
`claude/gastro-platform-phase-0-01GtVkvKRte2PTAWRjdHnP3c`

### Commit Message Convention
- **Add**: New features or files
- **Update**: Modifications to existing features
- **Fix**: Bug fixes
- **Refactor**: Code restructuring without feature changes

### Recent Commits
```
2032995 Add: Restaurant creation with geolocation in review form
b69590d Update: Modern UI design for networks and dashboard (Phase 1)
7328ca7 Add: Complete review system with modern UI (Phase 1)
54739c1 Add: Google OAuth authentication with Laravel Socialite
f173bf2 Update: Complete redesign of login and register pages
0ee241e Update: Enhance landing page with Listox-inspired design
```

---

## Testing Notes

### Manual Testing Checklist

**Authentication:**
- ✅ Google OAuth login
- ✅ Email/password login
- ✅ Registration

**Networks:**
- ✅ Create network
- ✅ View network dashboard
- ✅ Edit network settings (owner only)
- ✅ Delete network (owner only)

**Members:**
- ✅ Invite member by email
- ✅ Promote member to admin (owner only)
- ✅ Demote admin to member (owner only)
- ✅ Remove member (admin/owner)
- ✅ Permission checks for all operations

**Reviews:**
- ✅ Create review with existing restaurant
- ✅ Create review with new restaurant
- ✅ Geolocation auto-fill
- ✅ View review details
- ⚠️ Edit review (needs testing)
- ⚠️ Delete review (needs testing)

**UI/UX:**
- ✅ Responsive design on mobile
- ✅ Tab switching in network view
- ✅ Dropdown menus
- ✅ Gradient cards and buttons

---

## Known Issues

1. **No validation for duplicate restaurants**
   - Multiple restaurants with same name can be created
   - Future: Add fuzzy matching or duplicate detection

2. **Geolocation requires HTTPS in production**
   - HTML5 Geolocation API requires secure context
   - Works on localhost, needs HTTPS in production

3. **No pagination**
   - Review lists and member lists not paginated
   - Will need pagination when lists grow large

---

## Architecture Decisions

### Why Global Restaurant Catalog?
- **Consistency**: Same restaurant data across all networks
- **Efficiency**: No duplicate restaurant entries
- **Future-proof**: Enables cross-network features (public ratings, etc.)

### Why Private Networks?
- **Privacy**: Users want to share with specific groups
- **Flexibility**: Different networks for different contexts (family, foodie friends, work)
- **Control**: Network owners control who sees their reviews

### Why Three-Tier Permissions?
- **Owner**: Someone needs ultimate control
- **Admin**: Helps owners manage larger networks
- **Member**: Default role for participants

### Why Inline Restaurant Creation?
- **User Experience**: Don't break review flow
- **Immediate Feedback**: Users can review right away
- **Optional Enhancement**: Geolocation makes it even easier

---

## For Next Developer

### Quick Start
1. Pull latest from `claude/gastro-platform-phase-0-01GtVkvKRte2PTAWRjdHnP3c`
2. Run `composer install` and `npm install`
3. Copy `.env.example` to `.env` and configure
4. Run `php artisan migrate`
5. Run `php artisan serve` and `npm run dev`

### Priority Tasks
1. Implement review comments system (routes already defined)
2. Add edit/delete functionality testing for reviews
3. Implement restaurant detail pages
4. Add search and filter features

### Code Style
- Follow PSR-12 for PHP
- Use descriptive variable names in Spanish (matching existing code: `$memberRole`, `$network`)
- Keep Blade templates under 300 lines (extract components if needed)
- Use Tailwind utility classes (avoid custom CSS unless necessary)

### Questions?
Review this documentation first, then check:
- Laravel 11 docs: https://laravel.com/docs/11.x
- Tailwind CSS: https://tailwindcss.com/docs
- Alpine.js: https://alpinejs.dev/
- Nominatim API: https://nominatim.org/release-docs/latest/api/Reverse/

---

**Last Updated**: 2025-01-15
**Phase**: 0 (Foundation)
**Status**: Core features complete, ready for Phase 1 enhancements
