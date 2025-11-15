<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('networks.show', $network) }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    {{ $restaurant->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">{{ $network->name }}</p>
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Restaurant Header -->
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-xl shadow-xl p-8 text-white relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                @if($restaurant->logo_url)
                                    <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" class="w-20 h-20 rounded-xl bg-white p-2">
                                @endif
                                <div>
                                    <h1 class="text-4xl font-bold mb-2">{{ $restaurant->name }}</h1>
                                    <div class="flex items-center gap-3">
                                        @if($restaurant->status === 'open')
                                            <span class="px-3 py-1 bg-green-500 rounded-full text-xs font-semibold">Abierto</span>
                                        @elseif($restaurant->status === 'closed')
                                            <span class="px-3 py-1 bg-red-500 rounded-full text-xs font-semibold">Cerrado</span>
                                        @else
                                            <span class="px-3 py-1 bg-yellow-500 rounded-full text-xs font-semibold">Temporalmente Cerrado</span>
                                        @endif
                                        <span class="text-white/90">{{ $reviews->count() }} {{ $reviews->count() === 1 ? 'reseña' : 'reseñas' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-4 text-white/90">
                                @if($restaurant->city)
                                    <span class="flex items-center">
                                        <x-icons.location class="mr-1" />
                                        {{ $restaurant->city }}{{ $restaurant->country ? ', ' . $restaurant->country : '' }}
                                    </span>
                                @endif
                                @if($restaurant->cuisine_type)
                                    <span class="flex items-center">
                                        <x-icons.utensils class="mr-1" />
                                        {{ $restaurant->cuisine_type }}
                                    </span>
                                @endif
                                @if($restaurant->address)
                                    <span class="flex items-center">
                                        <x-icons.map-marker class="mr-1" />
                                        {{ $restaurant->address }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-2xl px-6 py-4 text-center">
                            <div class="text-5xl font-bold">{{ number_format($avgRatings['overall'], 1) }}</div>
                            <div class="flex mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <x-icons.star class="{{ $i <= round($avgRatings['overall']) ? 'text-yellow-300' : 'text-white/30' }}" />
                                @endfor
                            </div>
                            <div class="mt-2 text-xs text-white/80">Calificación promedio</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Description -->
                    @if($restaurant->description)
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Descripción</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $restaurant->description }}</p>
                        </div>
                    @endif

                    <!-- Photo Gallery from Members -->
                    @if($allPhotos->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <x-icons.image class="mr-2 text-indigo-600" />
                                Galería de Fotos
                                <span class="ml-2 text-sm text-gray-500 font-normal">({{ $allPhotos->count() }} fotos de miembros)</span>
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($allPhotos->take(12) as $photo)
                                    <div class="relative group overflow-hidden rounded-lg">
                                        <img src="{{ $photo->photo_url }}" alt="{{ $photo->caption }}" class="w-full h-48 object-cover transition-transform group-hover:scale-110">
                                        @if($photo->caption)
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                                <p class="text-white text-sm">{{ $photo->caption }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Prices Section (replaces Opening Hours) -->
                    @if($allPrices->isNotEmpty())
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Precios
                            </h3>
                            <div class="space-y-4">
                                @foreach($allPrices as $price)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3">
                                            @if($price->member->avatar)
                                                <img src="{{ $price->member->avatar }}" alt="{{ $price->member->name }}" class="w-10 h-10 rounded-full border-2 border-indigo-200">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($price->member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $price->member->name }}</div>
                                                @if($price->notes)
                                                    <div class="text-sm text-gray-600">{{ $price->notes }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-lg font-bold text-indigo-600">{{ $price->formatted_price }}</span>
                                            @if($price->ticket_photo_url)
                                                <a href="{{ $price->ticket_photo_url }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold hover:bg-indigo-200 transition">
                                                    <x-icons.image class="mr-1 text-xs" />
                                                    Ver ticket
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Amenities (Conditional) -->
                    @if($restaurant->amenities && count($restaurant->amenities) > 0)
                        <div class="bg-white rounded-xl shadow-md p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Amenities</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($restaurant->amenities as $amenity)
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>{{ $amenity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Reviews with Detailed Ratings -->
                    <div class="bg-white rounded-xl shadow-md p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Reseñas</h3>

                        <!-- Rating Breakdown -->
                        @if($reviews->isNotEmpty())
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-lg p-4 border border-amber-200">
                                    <div class="text-xs text-gray-600 mb-1">Calidad</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($avgRatings['quality'], 1) }}</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-icons.star class="{{ $i <= round($avgRatings['quality']) ? 'text-yellow-400' : 'text-gray-300' }} text-xs" />
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                                    <div class="text-xs text-gray-600 mb-1">Hospitalidad</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($avgRatings['hospitality'], 1) }}</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-icons.star class="{{ $i <= round($avgRatings['hospitality']) ? 'text-yellow-400' : 'text-gray-300' }} text-xs" />
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                                    <div class="text-xs text-gray-600 mb-1">Servicio</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($avgRatings['service'], 1) }}</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-icons.star class="{{ $i <= round($avgRatings['service']) ? 'text-yellow-400' : 'text-gray-300' }} text-xs" />
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
                                    <div class="text-xs text-gray-600 mb-1">Precio</div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-2xl font-bold text-gray-900">{{ number_format($avgRatings['pricing'], 1) }}</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-icons.star class="{{ $i <= round($avgRatings['pricing']) ? 'text-yellow-400' : 'text-gray-300' }} text-xs" />
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Reviews List -->
                        <div class="space-y-6">
                            @forelse($reviews as $review)
                                <div class="border-l-4 border-indigo-200 pl-6 py-4">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            @if($review->user->avatar)
                                                <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-12 h-12 rounded-full border-2 border-indigo-200">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $review->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-lg px-4 py-2 flex items-center gap-2">
                                            <x-icons.star class="text-yellow-300 text-sm" />
                                            <span class="font-bold">{{ $review->rating }}</span>
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-700 mb-4">{{ $review->comment }}</p>
                                    @endif
                                    <a href="{{ route('networks.reviews.show', [$network, $review]) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                        Ver detalles
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <p>No hay reseñas todavía. ¡Sé el primero en dejar una!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Information -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Información de Contacto</h3>
                        <div class="space-y-3">
                            @if($restaurant->phone)
                                <div class="flex items-center gap-3 text-gray-700">
                                    <x-icons.phone class="text-indigo-600" />
                                    <a href="tel:{{ $restaurant->phone }}" class="hover:text-indigo-600">{{ $restaurant->phone }}</a>
                                </div>
                            @endif
                            @if($restaurant->email)
                                <div class="flex items-center gap-3 text-gray-700">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <a href="mailto:{{ $restaurant->email }}" class="hover:text-indigo-600">{{ $restaurant->email }}</a>
                                </div>
                            @endif
                            @if($restaurant->website)
                                <div class="flex items-center gap-3 text-gray-700">
                                    <x-icons.globe class="text-indigo-600" />
                                    <a href="{{ $restaurant->website }}" target="_blank" class="hover:text-indigo-600">Sitio web</a>
                                </div>
                            @endif
                            @if($restaurant->address)
                                <div class="flex items-start gap-3 text-gray-700">
                                    <x-icons.map-marker class="text-indigo-600 mt-1" />
                                    <div>
                                        {{ $restaurant->address }}
                                        @if($restaurant->city || $restaurant->country)
                                            <br>{{ $restaurant->city }}{{ $restaurant->country ? ', ' . $restaurant->country : '' }}
                                        @endif
                                        @if($restaurant->postal_code)
                                            <br>{{ $restaurant->postal_code }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Social Media -->
                    @if($restaurant->facebook_url || $restaurant->instagram_url || $restaurant->twitter_url)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Redes Sociales</h3>
                            <div class="flex gap-3">
                                @if($restaurant->facebook_url)
                                    <a href="{{ $restaurant->facebook_url }}" target="_blank" class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                @endif
                                @if($restaurant->instagram_url)
                                    <a href="{{ $restaurant->instagram_url }}" target="_blank" class="flex items-center justify-center w-10 h-10 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                    </a>
                                @endif
                                @if($restaurant->twitter_url)
                                    <a href="{{ $restaurant->twitter_url }}" target="_blank" class="flex items-center justify-center w-10 h-10 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if($restaurant->tags && count($restaurant->tags) > 0)
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Categorías</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($restaurant->tags as $tag)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
