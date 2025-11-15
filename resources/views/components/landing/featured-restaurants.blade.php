<div class="py-20 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Restaurantes Destacados
            </h2>
            <p class="text-xl text-gray-600">
                Los lugares mejor valorados por nuestra comunidad
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Restaurant Card 1 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                <!-- Image Section -->
                <div class="relative h-56 bg-gradient-to-br from-orange-400 to-red-500 overflow-hidden">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-all"></div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">Abierto</span>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">
                            <x-icons.star class="inline text-xs" /> Destacado
                        </span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <x-icons.utensils class="text-white text-6xl opacity-50" />
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-sm font-medium">
                            <x-icons.pizza class="inline text-xs" /> Italiana
                        </span>
                        <button class="text-red-500 hover:text-red-600">
                            <x-icons.heart class="text-xl" />
                        </button>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">La Bella Napoli</h3>
                    <p class="text-gray-600 mb-4">Auténtica pizza napolitana con ingredientes frescos importados</p>

                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                        </div>
                        <span class="text-gray-600 ml-2 text-sm">(45 reseñas)</span>
                    </div>

                    <div class="flex items-center text-gray-600 mb-2">
                        <x-icons.map-marker class="text-indigo-600 mr-2" />
                        <span class="text-sm">Centro, Madrid</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-2xl font-bold text-indigo-600">€€€</span>
                        <a href="#" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>

            <!-- Restaurant Card 2 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                <!-- Image Section -->
                <div class="relative h-56 bg-gradient-to-br from-green-400 to-teal-500 overflow-hidden">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-all"></div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">Abierto</span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <x-icons.coffee class="text-white text-6xl opacity-50" />
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm font-medium">
                            <x-icons.coffee class="inline text-xs" /> Cafetería
                        </span>
                        <button class="text-red-500 hover:text-red-600">
                            <x-icons.heart class="text-xl" />
                        </button>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Café Bohemio</h3>
                    <p class="text-gray-600 mb-4">Espacio acogedor con café de especialidad y repostería artesanal</p>

                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm text-gray-300" />
                        </div>
                        <span class="text-gray-600 ml-2 text-sm">(32 reseñas)</span>
                    </div>

                    <div class="flex items-center text-gray-600 mb-2">
                        <x-icons.map-marker class="text-indigo-600 mr-2" />
                        <span class="text-sm">Malasaña, Madrid</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-2xl font-bold text-indigo-600">€€</span>
                        <a href="#" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>

            <!-- Restaurant Card 3 -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group">
                <!-- Image Section -->
                <div class="relative h-56 bg-gradient-to-br from-pink-400 to-purple-500 overflow-hidden">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition-all"></div>
                    <div class="absolute top-4 right-4">
                        <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-sm font-semibold">Cerrado</span>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-semibold">
                            <x-icons.star class="inline text-xs" /> Destacado
                        </span>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <x-icons.cocktail class="text-white text-6xl opacity-50" />
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-sm font-medium">
                            <x-icons.cocktail class="inline text-xs" /> Cocktail Bar
                        </span>
                        <button class="text-red-500 hover:text-red-600">
                            <x-icons.heart class="text-xl" />
                        </button>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">The Mixology Lab</h3>
                    <p class="text-gray-600 mb-4">Bar de cócteles de autor con mixólogos profesionales</p>

                    <div class="flex items-center mb-3">
                        <div class="flex text-yellow-400">
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                            <x-icons.star class="text-sm" />
                        </div>
                        <span class="text-gray-600 ml-2 text-sm">(67 reseñas)</span>
                    </div>

                    <div class="flex items-center text-gray-600 mb-2">
                        <x-icons.map-marker class="text-indigo-600 mr-2" />
                        <span class="text-sm">Chueca, Madrid</span>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                        <span class="text-2xl font-bold text-indigo-600">€€€€</span>
                        <a href="#" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- View All Button -->
        <div class="text-center mt-12">
            <a href="#" class="inline-flex items-center bg-indigo-600 text-white px-10 py-4 rounded-xl font-bold hover:bg-indigo-700 transition duration-200 shadow-lg">
                <x-icons.search class="mr-2" /> Ver Todos los Restaurantes
            </a>
        </div>
    </div>
</div>
