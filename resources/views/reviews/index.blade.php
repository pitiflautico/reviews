<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    Reseñas de {{ $network->name }}
                </h2>
                @if($network->description)
                    <p class="mt-1 text-sm text-gray-600">{{ $network->description }}</p>
                @endif
            </div>
            <a href="{{ route('networks.reviews.create', $network) }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Reseña
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium mb-1">Total Reseñas</p>
                            <p class="text-3xl font-bold">{{ $reviews->total() }}</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-lg p-3">
                            <x-icons.star class="text-2xl" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium mb-1">Restaurantes</p>
                            <p class="text-3xl font-bold">{{ $network->reviews->pluck('restaurant_id')->unique()->count() }}</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-lg p-3">
                            <x-icons.utensils class="text-2xl" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium mb-1">Promedio</p>
                            <p class="text-3xl font-bold">{{ number_format($network->reviews->avg('rating'), 1) }}</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-lg p-3">
                            <x-icons.award class="text-2xl" />
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white/80 text-sm font-medium mb-1">Miembros</p>
                            <p class="text-3xl font-bold">{{ $network->members->count() }}</p>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-lg p-3">
                            <x-icons.users class="text-2xl" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Sort -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Ordenar por:</label>
                        <select class="rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option>Más recientes</option>
                            <option>Mejor valoradas</option>
                            <option>Peor valoradas</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Calificación:</label>
                        <select class="rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option>Todas</option>
                            <option>5 estrellas</option>
                            <option>4+ estrellas</option>
                            <option>3+ estrellas</option>
                        </select>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex gap-2">
                        <button class="p-2 bg-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reviews Grid -->
            @if($reviews->isEmpty())
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-6">
                        <x-icons.star class="text-4xl text-indigo-600" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay reseñas todavía</h3>
                    <p class="text-gray-600 mb-6">
                        Sé el primero en compartir tu experiencia en un restaurante
                    </p>
                    <a href="{{ route('networks.reviews.create', $network) }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Primera Reseña
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    @foreach($reviews as $review)
                        <x-review-card :review="$review" :network="$network" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
