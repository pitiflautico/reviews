<div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-24 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Descubre y Comparte los Mejores Restaurantes
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                Únete a redes gastronómicas, comparte reseñas auténticas y encuentra tu próximo lugar favorito
            </p>

            <!-- Search Bar -->
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-2xl p-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <x-icons.search class="absolute left-4 top-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Busca restaurantes, cocina, platos..."
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>
                    <div class="flex-1 relative">
                        <x-icons.map-marker class="absolute left-4 top-4 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Ciudad o dirección"
                            class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        />
                    </div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 text-center flex items-center justify-center">
                            Buscar
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 text-center flex items-center justify-center">
                            Buscar
                        </a>
                    @endauth
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-indigo-50 transition duration-200">
                        Ir al Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 font-semibold px-8 py-3 rounded-lg hover:bg-indigo-50 transition duration-200">
                        Crear Cuenta Gratis
                    </a>
                    <a href="{{ route('login') }}" class="inline-block bg-transparent border-2 border-white text-white font-semibold px-8 py-3 rounded-lg hover:bg-white hover:text-indigo-600 transition duration-200">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>
