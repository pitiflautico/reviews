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
            <form method="POST" action="{{ route('networks.reviews.store', $network) }}" enctype="multipart/form-data" class="space-y-6">
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
                                <select
                                    id="restaurant_cuisine_type"
                                    name="restaurant_cuisine_type"
                                    :required="option === 'new'"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                >
                                    <option value="">Selecciona un tipo...</option>
                                    <option value="Italiana" {{ old('restaurant_cuisine_type') == 'Italiana' ? 'selected' : '' }}>Italiana</option>
                                    <option value="Mexicana" {{ old('restaurant_cuisine_type') == 'Mexicana' ? 'selected' : '' }}>Mexicana</option>
                                    <option value="Española" {{ old('restaurant_cuisine_type') == 'Española' ? 'selected' : '' }}>Española</option>
                                    <option value="China" {{ old('restaurant_cuisine_type') == 'China' ? 'selected' : '' }}>China</option>
                                    <option value="Japonesa" {{ old('restaurant_cuisine_type') == 'Japonesa' ? 'selected' : '' }}>Japonesa</option>
                                    <option value="India" {{ old('restaurant_cuisine_type') == 'India' ? 'selected' : '' }}>India</option>
                                    <option value="Francesa" {{ old('restaurant_cuisine_type') == 'Francesa' ? 'selected' : '' }}>Francesa</option>
                                    <option value="Americana" {{ old('restaurant_cuisine_type') == 'Americana' ? 'selected' : '' }}>Americana</option>
                                    <option value="Argentina" {{ old('restaurant_cuisine_type') == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                                    <option value="Peruana" {{ old('restaurant_cuisine_type') == 'Peruana' ? 'selected' : '' }}>Peruana</option>
                                    <option value="Mediterránea" {{ old('restaurant_cuisine_type') == 'Mediterránea' ? 'selected' : '' }}>Mediterránea</option>
                                    <option value="Asiática (Fusión)" {{ old('restaurant_cuisine_type') == 'Asiática (Fusión)' ? 'selected' : '' }}>Asiática (Fusión)</option>
                                    <option value="Árabe" {{ old('restaurant_cuisine_type') == 'Árabe' ? 'selected' : '' }}>Árabe</option>
                                    <option value="Vegetariana/Vegana" {{ old('restaurant_cuisine_type') == 'Vegetariana/Vegana' ? 'selected' : '' }}>Vegetariana/Vegana</option>
                                    <option value="Marisquería" {{ old('restaurant_cuisine_type') == 'Marisquería' ? 'selected' : '' }}>Marisquería</option>
                                    <option value="Parrilla/Asador" {{ old('restaurant_cuisine_type') == 'Parrilla/Asador' ? 'selected' : '' }}>Parrilla/Asador</option>
                                    <option value="Fast Food" {{ old('restaurant_cuisine_type') == 'Fast Food' ? 'selected' : '' }}>Fast Food</option>
                                    <option value="Tapas/Pinchos" {{ old('restaurant_cuisine_type') == 'Tapas/Pinchos' ? 'selected' : '' }}>Tapas/Pinchos</option>
                                    <option value="Cafetería" {{ old('restaurant_cuisine_type') == 'Cafetería' ? 'selected' : '' }}>Cafetería</option>
                                    <option value="Otro" {{ old('restaurant_cuisine_type') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
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

                <!-- Photo Gallery -->
                <div class="bg-white rounded-xl shadow-md p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg p-3">
                            <x-icons.image class="text-2xl text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Galería de Fotos</h3>
                            <p class="text-sm text-gray-600">Comparte fotos de tu experiencia</p>
                        </div>
                    </div>

                    <div>
                        <label for="photos" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.image class="inline mr-1" /> Fotos (Múltiples)
                        </label>
                        <input
                            type="file"
                            id="photos"
                            name="photos[]"
                            multiple
                            accept="image/*"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                        <x-input-error :messages="$errors->get('photos')" class="mt-2" />
                        <p class="mt-2 text-sm text-gray-600">
                            Puedes seleccionar múltiples fotos de la comida, el ambiente, etc.
                        </p>
                    </div>

                    <div id="photo-preview" class="grid grid-cols-3 md:grid-cols-4 gap-4 mt-4 hidden">
                        <!-- Photos preview will appear here -->
                    </div>
                </div>

                <!-- Price & Ticket (Required) -->
                <div class="bg-white rounded-xl shadow-md p-8 border-2 border-indigo-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900">Precio y Ticket *</h3>
                            <p class="text-sm text-gray-600">Debes añadir al menos el precio O subir la foto del ticket</p>
                        </div>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">OBLIGATORIO</span>
                    </div>

                    <div class="space-y-6">
                        <!-- Price Amount -->
                        <div>
                            <label for="price_amount" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Precio Total (€)
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                id="price_amount"
                                name="price_amount"
                                value="{{ old('price_amount') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="25.50"
                            />
                            <x-input-error :messages="$errors->get('price_amount')" class="mt-2" />
                        </div>

                        <!-- Ticket Photo -->
                        <div>
                            <label for="ticket_photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                <x-icons.image class="inline mr-1" /> Foto del Ticket/Cuenta
                            </label>
                            <input
                                type="file"
                                id="ticket_photo"
                                name="ticket_photo"
                                accept="image/*"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                            />
                            <x-input-error :messages="$errors->get('ticket_photo')" class="mt-2" />
                            <p class="mt-2 text-sm text-gray-600">
                                📸 El sistema intentará detectar automáticamente el precio del ticket usando OCR
                            </p>
                        </div>

                        <!-- Price Notes -->
                        <div>
                            <label for="price_notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Notas sobre el Gasto (Opcional)
                            </label>
                            <textarea
                                id="price_notes"
                                name="price_notes"
                                rows="2"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Ej: 2 personas, incluye bebida, entrante y postre"
                            >{{ old('price_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('price_notes')" class="mt-2" />
                        </div>

                        <!-- Detected Price Display (hidden initially) -->
                        <div id="detected-price-container" class="hidden bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-semibold text-indigo-900">Precio detectado por OCR:</span>
                                <span id="detected-price" class="text-lg font-bold text-indigo-600"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Photo preview
                    document.getElementById('photos').addEventListener('change', function(e) {
                        const preview = document.getElementById('photo-preview');
                        preview.innerHTML = '';

                        if (this.files.length > 0) {
                            preview.classList.remove('hidden');

                            for (let i = 0; i < Math.min(this.files.length, 8); i++) {
                                const file = this.files[i];
                                const reader = new FileReader();

                                reader.onload = function(e) {
                                    const img = document.createElement('img');
                                    img.src = e.target.result;
                                    img.className = 'w-full h-24 object-cover rounded-lg border-2 border-gray-200';
                                    preview.appendChild(img);
                                }

                                reader.readAsDataURL(file);
                            }
                        } else {
                            preview.classList.add('hidden');
                        }
                    });

                    // Ticket OCR simulation (basic)
                    document.getElementById('ticket_photo').addEventListener('change', function(e) {
                        if (this.files.length > 0) {
                            // Simulate OCR processing
                            const container = document.getElementById('detected-price-container');
                            const priceDisplay = document.getElementById('detected-price');

                            // Show loading
                            container.classList.remove('hidden');
                            priceDisplay.textContent = 'Procesando...';

                            // Simulate OCR delay
                            setTimeout(() => {
                                // In production, this would call an actual OCR API
                                // For now, we'll show a placeholder
                                const randomPrice = (Math.random() * 50 + 10).toFixed(2);
                                priceDisplay.textContent = randomPrice + ' €';

                                // Auto-fill the price field if empty
                                const priceInput = document.getElementById('price_amount');
                                if (!priceInput.value) {
                                    priceInput.value = randomPrice;
                                }
                            }, 1500);
                        }
                    });

                    // Validation: at least price OR ticket photo
                    document.querySelector('form').addEventListener('submit', function(e) {
                        const priceAmount = document.getElementById('price_amount').value;
                        const ticketPhoto = document.getElementById('ticket_photo').files.length;

                        if (!priceAmount && ticketPhoto === 0) {
                            e.preventDefault();
                            alert('Debes añadir al menos el precio O subir la foto del ticket');
                            document.getElementById('price_amount').focus();
                            return false;
                        }
                    });
                </script>

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
