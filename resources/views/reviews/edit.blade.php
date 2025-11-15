<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('networks.reviews.show', [$network, $review]) }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    Editar Reseña
                </h2>
                <p class="mt-1 text-sm text-gray-600">{{ $review->restaurant->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('networks.reviews.update', [$network, $review]) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Restaurant Selection -->
                <x-form-section
                    title="Restaurante"
                    description="Selecciona el restaurante que visitaste"
                    gradient="from-indigo-500 to-purple-600"
                >
                    <x-slot:icon>
                        <x-icons.utensils class="text-2xl text-white" />
                    </x-slot:icon>

                    <div>
                        <label for="restaurant_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <x-icons.search class="inline mr-1" /> Buscar Restaurante
                        </label>
                        <select
                            id="restaurant_id"
                            name="restaurant_id"
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                            <option value="">Selecciona un restaurante...</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ (old('restaurant_id', $review->restaurant_id) == $restaurant->id) ? 'selected' : '' }}>
                                    {{ $restaurant->name }} - {{ $restaurant->city ?? 'Sin ubicación' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('restaurant_id')" class="mt-2" />
                    </div>
                </x-form-section>

                <!-- Rating -->
                <x-form-section
                    title="Calificación"
                    description="¿Qué te pareció tu experiencia?"
                    gradient="from-amber-500 to-orange-600"
                >
                    <x-slot:icon>
                        <x-icons.star class="text-2xl text-white" />
                    </x-slot:icon>

                    <x-rating-selector :value="old('rating', $review->rating)" />
                    <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                </x-form-section>

                <!-- Review Content -->
                <x-form-section
                    title="Tu Opinión"
                    description="Cuéntanos sobre tu experiencia"
                    gradient="from-pink-500 to-rose-600"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </x-slot:icon>

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
                        >{{ old('comment', $review->comment) }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>
                </x-form-section>

                <!-- Additional Details -->
                <x-form-section
                    title="Detalles Adicionales"
                    description="Información opcional sobre tu visita"
                    gradient="from-emerald-500 to-teal-600"
                >
                    <x-slot:icon>
                        <x-icons.clock class="text-2xl text-white" />
                    </x-slot:icon>

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
                                value="{{ old('date_of_visit', $review->date_of_visit?->format('Y-m-d')) }}"
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
                                <option value="breakfast" {{ old('meal_type', $review->meal_type) == 'breakfast' ? 'selected' : '' }}>Desayuno</option>
                                <option value="lunch" {{ old('meal_type', $review->meal_type) == 'lunch' ? 'selected' : '' }}>Almuerzo</option>
                                <option value="dinner" {{ old('meal_type', $review->meal_type) == 'dinner' ? 'selected' : '' }}>Cena</option>
                            </select>
                            <x-input-error :messages="$errors->get('meal_type')" class="mt-2" />
                        </div>
                    </div>
                </x-form-section>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <form method="POST" action="{{ route('networks.reviews.destroy', [$network, $review]) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta reseña?');">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition duration-200"
                        >
                            Eliminar Reseña
                        </button>
                    </form>

                    <div class="flex gap-4">
                        <a href="{{ route('networks.reviews.show', [$network, $review]) }}"
                           class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200">
                            Cancelar
                        </a>
                        <button
                            type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
                        >
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
