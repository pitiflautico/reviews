<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Cuenta - Gastro Reviews</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex">
        <!-- Left Side - Gradient Background with Info -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 relative overflow-hidden items-center justify-center p-12">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 text-white max-w-lg">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur rounded-full mb-6">
                        <x-icons.rocket class="text-5xl" />
                    </div>
                    <h2 class="text-5xl font-bold mb-4">Empieza tu Aventura Gastronómica</h2>
                    <p class="text-xl text-white/90 mb-8">
                        Crea tu cuenta gratis y únete a una comunidad apasionada por la buena comida
                    </p>
                </div>

                <!-- Benefits -->
                <div class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur rounded-full">
                                <x-icons.check class="text-2xl" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold mb-1">100% Gratis</h3>
                            <p class="text-white/80">Sin costos ocultos ni suscripciones</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur rounded-full">
                                <x-icons.bolt class="text-2xl" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold mb-1">Registro Rápido</h3>
                            <p class="text-white/80">Crea tu cuenta en menos de 2 minutos</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur rounded-full">
                                <x-icons.shield class="text-2xl" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold mb-1">Datos Seguros</h3>
                            <p class="text-white/80">Tu privacidad es nuestra prioridad</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial -->
                <div class="mt-12 p-6 bg-white/10 backdrop-blur rounded-2xl">
                    <div class="flex items-start mb-3">
                        <x-icons.quote class="text-2xl mr-3" />
                        <p class="text-white/90 italic">
                            "La mejor forma de descubrir restaurantes auténticos. Las recomendaciones de mi red nunca fallan."
                        </p>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/30 rounded-full flex items-center justify-center font-bold mr-3">
                            MC
                        </div>
                        <div>
                            <div class="font-semibold">María Carmen</div>
                            <div class="text-white/70 text-sm">Food Blogger</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12 bg-white">
            <div class="w-full max-w-md">
                <!-- Logo & Back to Home -->
                <div class="mb-8">
                    <a href="/" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition mb-6">
                        <x-icons.utensils class="mr-2 text-xl" />
                        <span class="font-bold text-2xl text-indigo-600">Gastro Reviews</span>
                    </a>
                </div>

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Crea tu Cuenta</h1>
                    <p class="text-gray-600">Únete a nuestra comunidad gastronómica</p>
                </div>

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.user-plus class="inline mr-1" /> Nombre Completo
                        </label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Juan Pérez"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.users class="inline mr-1" /> Correo Electrónico
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="tu@email.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.shield class="inline mr-1" /> Contraseña
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Mínimo 8 caracteres"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.shield class="inline mr-1" /> Confirmar Contraseña
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="Repite tu contraseña"
                        />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-xl transition duration-200 shadow-lg flex items-center justify-center"
                    >
                        <x-icons.rocket class="mr-2" /> Crear Cuenta Gratis
                    </button>

                    <!-- Terms -->
                    <p class="text-xs text-gray-500 text-center">
                        Al registrarte, aceptas nuestros
                        <a href="#" class="text-indigo-600 hover:text-indigo-500">Términos de Servicio</a>
                        y
                        <a href="#" class="text-indigo-600 hover:text-indigo-500">Política de Privacidad</a>
                    </p>

                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-gray-600">
                            ¿Ya tienes cuenta?
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition">
                                Inicia sesión
                            </a>
                        </p>
                    </div>
                </form>

                <!-- Social Register (Optional) -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">O regístrate con</span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            <i class="fab fa-google mr-2 text-red-500"></i>
                            <span class="text-gray-700 font-medium">Google</span>
                        </button>
                        <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            <i class="fab fa-facebook mr-2 text-blue-600"></i>
                            <span class="text-gray-700 font-medium">Facebook</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
