<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('networks.reviews.index', $network) }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    Nueva Reseña
                </h2>
                <p class="mt-1 text-sm text-gray-600">Comparte tu experiencia en {{ $network->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('networks.reviews.store', $network) }}" class="space-y-6">
                @csrf

                <!-- Restaurant Selection/Creation -->
                <div class="bg-white rounded-xl shadow-md p-8" x-data="{ option: 'existing' }">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg p-3">
                            <x-icons.utensils class="text-2xl text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Restaurante</h3>
                            <p class="text-sm text-gray-600">Selecciona o crea un restaurante</p>
                        </div>
                    </div>

                    <!-- Toggle Buttons -->
                    <div class="flex gap-3 mb-6">
                        <button
                            type="button"
                            @click="option = 'existing'"
                            :class="option === 'existing' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="flex-1 px-4 py-3 font-semibold rounded-xl transition"
                        >
                            Seleccionar Existente
                        </button>
                        <button
                            type="button"
                            @click="option = 'new'"
                            :class="option === 'new' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="flex-1 px-4 py-3 font-semibold rounded-xl transition"
                        >
                            Crear Nuevo
                        </button>
                    </div>

                    <input type="hidden" name="restaurant_option" :value="option">

                    <!-- Existing Restaurant Selection -->
                    <div x-show="option === 'existing'" x-transition>
                        <label for="restaurant_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.search class="inline mr-1" /> Buscar Restaurante
                        </label>
                        <select
                            id="restaurant_id"
                            name="restaurant_id"
                            :required="option === 'existing'"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                            <option value="">Selecciona un restaurante...</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                    {{ $restaurant->name }} - {{ $restaurant->city ?? 'Sin ubicación' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('restaurant_id')" class="mt-2" />
                    </div>

                    <!-- New Restaurant Form -->
                    <div x-show="option === 'new'" x-transition class="space-y-4">
                        <!-- Geolocation Button -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-indigo-900">Usar mi ubicación</p>
                                    <p class="text-xs text-indigo-700 mt-1">Detecta automáticamente la dirección del restaurante</p>
                                </div>
                                <button
                                    type="button"
                                    onclick="getLocation()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition"
                                >
                                    <x-icons.location class="inline mr-1" /> Detectar
                                </button>
                            </div>
                        </div>

                        <!-- Restaurant Name -->
                        <div>
                            <label for="restaurant_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nombre del Restaurante *
                            </label>
                            <input
                                id="restaurant_name"
                                type="text"
                                name="restaurant_name"
                                value="{{ old('restaurant_name') }}"
                                :required="option === 'new'"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Ej: La Trattoria Italiana"
                            />
                            <x-input-error :messages="$errors->get('restaurant_name')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="restaurant_address" class="block text-sm font-semibold text-gray-700 mb-2">
                                Dirección *
                            </label>
                            <input
                                id="restaurant_address"
                                type="text"
                                name="restaurant_address"
                                value="{{ old('restaurant_address') }}"
                                :required="option === 'new'"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Calle Principal 123"
                            />
                            <x-input-error :messages="$errors->get('restaurant_address')" class="mt-2" />
                        </div>

                        <!-- City & Cuisine Type -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="restaurant_city" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Ciudad
                                </label>
                                <input
                                    id="restaurant_city"
                                    type="text"
                                    name="restaurant_city"
                                    value="{{ old('restaurant_city') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Madrid"
                                />
                                <x-input-error :messages="$errors->get('restaurant_city')" class="mt-2" />
                            </div>

                            <div>
                                <label for="restaurant_cuisine_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tipo de Cocina *
                                </label>
                                <input
                                    id="restaurant_cuisine_type"
                                    type="text"
                                    name="restaurant_cuisine_type"
                                    value="{{ old('restaurant_cuisine_type') }}"
                                    :required="option === 'new'"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                    placeholder="Italiana, Mexicana, etc."
                                />
                                <x-input-error :messages="$errors->get('restaurant_cuisine_type')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Hidden coordinates -->
                        <input type="hidden" id="restaurant_latitude" name="restaurant_latitude" value="{{ old('restaurant_latitude') }}">
                        <input type="hidden" id="restaurant_longitude" name="restaurant_longitude" value="{{ old('restaurant_longitude') }}">

                        <div id="location-status" class="text-sm text-gray-600"></div>
                    </div>
                </div>

                <script>
                    function getLocation() {
                        const statusEl = document.getElementById('location-status');
                        statusEl.innerHTML = '<span class="text-indigo-600">Obteniendo ubicación...</span>';

                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                function(position) {
                                    const lat = position.coords.latitude;
                                    const lon = position.coords.longitude;

                                    document.getElementById('restaurant_latitude').value = lat;
                                    document.getElementById('restaurant_longitude').value = lon;

                                    // Reverse geocoding with Nominatim
                                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.address) {
                                                const address = data.address;

                                                // Fill in address
                                                if (address.road) {
                                                    const street = address.road + (address.house_number ? ' ' + address.house_number : '');
                                                    document.getElementById('restaurant_address').value = street;
                                                }

                                                // Fill in city
                                                const city = address.city || address.town || address.village || address.municipality;
                                                if (city) {
                                                    document.getElementById('restaurant_city').value = city;
                                                }

                                                statusEl.innerHTML = '<span class="text-green-600">✓ Ubicación detectada exitosamente</span>';
                                            } else {
                                                statusEl.innerHTML = '<span class="text-yellow-600">Coordenadas guardadas. Por favor completa la dirección manualmente.</span>';
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            statusEl.innerHTML = '<span class="text-yellow-600">Coordenadas guardadas. Por favor completa la dirección manualmente.</span>';
                                        });
                                },
                                function(error) {
                                    statusEl.innerHTML = '<span class="text-red-600">Error: No se pudo obtener la ubicación. Por favor ingresa la dirección manualmente.</span>';
                                    console.error('Geolocation error:', error);
                                }
                            );
                        } else {
                            statusEl.innerHTML = '<span class="text-red-600">Tu navegador no soporta geolocalización.</span>';
                        }
                    }
                </script>

                <!-- Rating -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg p-3">
                            <x-icons.star class="text-2xl text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Calificación</h3>
                            <p class="text-sm text-gray-600">¿Qué te pareció tu experiencia?</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Puntuación</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer group">
                                    <input
                                        type="radio"
                                        name="rating"
                                        value="{{ $i }}"
                                        required
                                        class="sr-only peer"
                                        {{ old('rating') == $i ? 'checked' : '' }}
                                    />
                                    <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition">
                                        <x-icons.star class="text-3xl peer-checked:text-yellow-400 text-gray-300 group-hover:text-yellow-300" />
                                        <span class="mt-2 font-bold text-gray-700">{{ $i }}</span>
                                    </div>
                                </label>
                            @endfor
                        </div>
                        <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                    </div>
                </div>

                <!-- Review Content -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Tu Opinión</h3>
                            <p class="text-sm text-gray-600">Cuéntanos sobre tu experiencia</p>
                        </div>
                    </div>

                    <div>
                        <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
                            Comentario
                        </label>
                        <textarea
                            id="comment"
                            name="comment"
                            rows="6"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            placeholder="¿Qué te gustó? ¿Qué platos probaste? ¿Cómo fue el servicio?"
                        >{{ old('comment') }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg p-3">
                            <x-icons.clock class="text-2xl text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Detalles Adicionales</h3>
                            <p class="text-sm text-gray-600">Información opcional sobre tu visita</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Visit Date -->
                        <div>
                            <label for="date_of_visit" class="block text-sm font-semibold text-gray-700 mb-2">
                                <x-icons.clock class="inline mr-1" /> Fecha de Visita
                            </label>
                            <input
                                type="date"
                                id="date_of_visit"
                                name="date_of_visit"
                                value="{{ old('date_of_visit') }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            />
                            <x-input-error :messages="$errors->get('date_of_visit')" class="mt-2" />
                        </div>

                        <!-- Meal Type -->
                        <div>
                            <label for="meal_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <x-icons.utensils class="inline mr-1" /> Tipo de Comida
                            </label>
                            <select
                                id="meal_type"
                                name="meal_type"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="">Selecciona...</option>
                                <option value="breakfast" {{ old('meal_type') == 'breakfast' ? 'selected' : '' }}>Desayuno</option>
                                <option value="lunch" {{ old('meal_type') == 'lunch' ? 'selected' : '' }}>Almuerzo</option>
                                <option value="dinner" {{ old('meal_type') == 'dinner' ? 'selected' : '' }}>Cena</option>
                            </select>
                            <x-input-error :messages="$errors->get('meal_type')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('networks.reviews.index', $network) }}"
                       class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200">
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
                    >
                        Publicar Reseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
