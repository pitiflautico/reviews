<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gastro Reviews - Descubre y Comparte los Mejores Restaurantes</title>
    <meta name="description" content="Únete a redes gastronómicas privadas, comparte reseñas auténticas y encuentra tu próximo lugar favorito. Catálogo global de restaurantes con opiniones verificadas.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Header -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-indigo-600">
                        <x-icons.utensils class="inline mr-2" />
                        Gastro Reviews
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-700 hover:text-indigo-600 font-medium transition">Características</a>
                    <a href="#how-it-works" class="text-gray-700 hover:text-indigo-600 font-medium transition">Cómo Funciona</a>
                    <a href="#testimonials" class="text-gray-700 hover:text-indigo-600 font-medium transition">Testimonios</a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-medium transition">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium transition">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <x-landing.hero />

    <!-- Categories Section -->
    <x-landing.categories />

    <!-- Features Section -->
    <div id="features">
        <x-landing.features />
    </div>

    <!-- How It Works Section -->
    <div id="how-it-works">
        <x-landing.how-it-works />
    </div>

    <!-- Stats Section -->
    <x-landing.stats />

    <!-- Testimonials Section -->
    <div id="testimonials">
        <x-landing.testimonials />
    </div>

    <!-- CTA Section -->
    <x-landing.cta />

    <!-- Footer -->
    <x-landing.footer />
</body>
</html>
