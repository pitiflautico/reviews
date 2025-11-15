<div class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 text-white py-32 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-20 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Encuentra los Lugares que<br />Amarás Descubrir
            </h1>
            <p class="text-xl md:text-2xl mb-12 text-white/90 max-w-3xl mx-auto">
                Únete a redes gastronómicas privadas, comparte reseñas auténticas con personas de confianza y descubre tu próximo restaurante favorito
            </p>

            <!-- Search Bar -->
            <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl p-8">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <!-- Category Selector -->
                    <div class="w-full md:w-auto">
                        <select class="w-full md:w-48 px-4 py-4 rounded-xl border border-gray-300 text-gray-900 font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-gray-50">
                            <option>Todos</option>
                            <option>Restaurantes</option>
                            <option>Cafeterías</option>
                            <option>Bares</option>
                            <option>Pizzerías</option>
                            <option>Fast Food</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <x-icons.search class="absolute left-4 top-5 text-gray-400 text-lg" />
                        <input
                            type="text"
                            placeholder="Busca restaurantes, cocina, platos..."
                            class="w-full pl-12 pr-4 py-4 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                        />
                    </div>

                    <!-- Location Input -->
                    <div class="flex-1 relative">
                        <x-icons.map-marker class="absolute left-4 top-5 text-gray-400 text-lg" />
                        <input
                            type="text"
                            placeholder="Ciudad o dirección"
                            class="w-full pl-12 pr-4 py-4 rounded-xl border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg"
                        />
                    </div>

                    <!-- Search Button -->
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full md:w-auto bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-10 py-4 rounded-xl transition duration-200 text-center flex items-center justify-center shadow-lg">
                            <x-icons.search class="mr-2" /> Buscar
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="w-full md:w-auto bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-10 py-4 rounded-xl transition duration-200 text-center flex items-center justify-center shadow-lg">
                            <x-icons.search class="mr-2" /> Buscar
                        </a>
                    @endauth
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-white text-indigo-600 font-bold px-10 py-4 rounded-xl hover:bg-gray-50 transition duration-200 shadow-lg">
                        <x-icons.users class="mr-2 text-xl" /> Ir al Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center bg-white text-indigo-600 font-bold px-10 py-4 rounded-xl hover:bg-gray-50 transition duration-200 shadow-lg">
                        <x-icons.user-plus class="mr-2 text-xl" /> Crear Cuenta Gratis
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center bg-white/10 backdrop-blur border-2 border-white text-white font-bold px-10 py-4 rounded-xl hover:bg-white hover:text-indigo-600 transition duration-200">
                        <x-icons.users class="mr-2 text-xl" /> Iniciar Sesión
                    </a>
                @endauth
            </div>

            <!-- Quick Stats -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-4xl font-bold">1,000+</div>
                    <div class="text-white/80 mt-1">Restaurantes</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold">500+</div>
                    <div class="text-white/80 mt-1">Redes</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold">5,000+</div>
                    <div class="text-white/80 mt-1">Reseñas</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold">2,500+</div>
                    <div class="text-white/80 mt-1">Usuarios</div>
                </div>
            </div>
        </div>
    </div>
</div>
