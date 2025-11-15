<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('networks.reviews.index', $network) }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    {{ $review->restaurant->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Reseña en {{ $network->name }}</p>
            </div>
            @if($review->user_id === Auth::id())
                <a href="{{ route('networks.reviews.edit', [$network, $review]) }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section with Photos -->
            @php
                $photos = $review->photos ?? collect();
                $mainPhoto = $photos->first();
                $gridPhotos = $photos->skip(1)->take(4);
            @endphp

            @if($photos->isNotEmpty())
                <div class="grid grid-cols-4 gap-2 h-[400px] mb-6 rounded-2xl overflow-hidden">
                    <!-- Main large photo -->
                    <div class="col-span-2 row-span-2 relative group cursor-pointer" onclick="openPhotoModal(0)">
                        <img src="{{ $mainPhoto->photo_url }}" alt="Foto principal" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                    </div>

                    <!-- Grid of 4 smaller photos -->
                    @foreach($gridPhotos as $index => $photo)
                        <div class="relative group cursor-pointer {{ $loop->last && $photos->count() > 5 ? 'overflow-hidden' : '' }}" onclick="openPhotoModal({{ $index + 1 }})">
                            <img src="{{ $photo->photo_url }}" alt="Foto {{ $index + 2 }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>

                            @if($loop->last && $photos->count() > 5)
                                <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                                    <div class="text-white text-center">
                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-2xl font-bold">+{{ $photos->count() - 5 }}</span>
                                        <p class="text-sm">fotos más</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Fill empty slots if less than 5 photos -->
                    @for($i = $gridPhotos->count(); $i < 4; $i++)
                        <div class="bg-gray-100"></div>
                    @endfor
                </div>
            @else
                <!-- Fallback gradient header if no photos -->
                <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-2xl h-64 mb-6 flex items-center justify-center">
                    <div class="text-white text-center">
                        <x-icons.image class="text-6xl mx-auto mb-4 opacity-50" />
                        <p class="text-xl">Sin fotos todavía</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Quick Info Card -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $review->restaurant->name }}</h1>
                                <div class="flex flex-wrap items-center gap-3 text-gray-600">
                                    @if($review->restaurant->cuisine_type)
                                        <span class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-semibold">
                                            <x-icons.utensils class="mr-1 text-xs" />
                                            {{ $review->restaurant->cuisine_type }}
                                        </span>
                                    @endif
                                    @if($review->restaurant->city)
                                        <span class="flex items-center text-sm">
                                            <x-icons.location class="mr-1" />
                                            {{ $review->restaurant->city }}
                                        </span>
                                    @endif
                                    @if($review->meal_type)
                                        <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                                            {{ $review->meal_type == 'breakfast' ? '🌅 Desayuno' : ($review->meal_type == 'lunch' ? '🍽️ Almuerzo' : '🌙 Cena') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Overall Rating Badge -->
                            <div class="bg-gradient-to-br from-amber-400 to-orange-500 text-white rounded-2xl px-6 py-4 text-center shadow-lg">
                                <div class="text-5xl font-black">{{ $review->rating }}</div>
                                <div class="flex mt-1 justify-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-white' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <div class="text-xs mt-1 text-white/90">Excelente</div>
                            </div>
                        </div>

                        <!-- Detailed Ratings Bar -->
                        @if($review->quality_rating || $review->hospitality_rating || $review->service_rating || $review->pricing_rating)
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @if($review->quality_rating)
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900">{{ $review->quality_rating }}<span class="text-sm text-gray-500">/5</span></div>
                                            <div class="text-xs text-gray-600 mt-1">Calidad</div>
                                            <div class="flex justify-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $review->quality_rating ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif

                                    @if($review->hospitality_rating)
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900">{{ $review->hospitality_rating }}<span class="text-sm text-gray-500">/5</span></div>
                                            <div class="text-xs text-gray-600 mt-1">Hospitalidad</div>
                                            <div class="flex justify-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $review->hospitality_rating ? 'text-blue-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif

                                    @if($review->service_rating)
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900">{{ $review->service_rating }}<span class="text-sm text-gray-500">/5</span></div>
                                            <div class="text-xs text-gray-600 mt-1">Servicio</div>
                                            <div class="flex justify-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $review->service_rating ? 'text-green-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif

                                    @if($review->pricing_rating)
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-gray-900">{{ $review->pricing_rating }}<span class="text-sm text-gray-500">/5</span></div>
                                            <div class="text-xs text-gray-600 mt-1">Precio</div>
                                            <div class="flex justify-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $review->pricing_rating ? 'text-purple-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="bg-white rounded-t-2xl shadow-md">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px">
                                <button onclick="switchTab('overview')" class="tab-button active px-8 py-4 text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 transition">
                                    Descripción
                                </button>
                                @if($photos->isNotEmpty())
                                    <button onclick="switchTab('photos')" class="tab-button px-8 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition">
                                        Fotos ({{ $photos->count() }})
                                    </button>
                                @endif
                                <button onclick="switchTab('comments')" class="tab-button px-8 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 transition">
                                    Comentarios ({{ $review->comments->count() }})
                                </button>
                            </nav>
                        </div>

                        <!-- Tab Contents -->
                        <div class="p-8">
                            <!-- Overview Tab -->
                            <div id="tab-overview" class="tab-content">
                                <!-- Author Info -->
                                <div class="flex items-center gap-4 pb-6 border-b border-gray-200 mb-6">
                                    @if($review->user->avatar)
                                        <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-16 h-16 rounded-full border-4 border-indigo-100 shadow-sm">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-sm">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900">{{ $review->user->name }}</h3>
                                        <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $review->created_at->diffForHumans() }}
                                            </span>
                                            @if($review->date_of_visit)
                                                <span class="text-gray-400">•</span>
                                                <span>Visitado el {{ $review->date_of_visit->format('d/m/Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Review Text -->
                                <div class="prose prose-lg max-w-none">
                                    <p class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $review->comment }}</p>
                                </div>
                            </div>

                            <!-- Photos Tab -->
                            @if($photos->isNotEmpty())
                                <div id="tab-photos" class="tab-content hidden">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        @foreach($photos as $index => $photo)
                                            <div class="relative group cursor-pointer aspect-square rounded-xl overflow-hidden" onclick="openPhotoModal({{ $index }})">
                                                <img src="{{ $photo->photo_url }}" alt="Foto {{ $index + 1 }}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
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

                            <!-- Comments Tab -->
                            <div id="tab-comments" class="tab-content hidden">
                                <!-- Add Comment Form -->
                                <form method="POST" action="{{ route('networks.reviews.comments.store', [$network, $review]) }}" class="mb-8">
                                    @csrf
                                    <div class="flex gap-4">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-full border-2 border-indigo-200">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <textarea
                                                name="comment"
                                                rows="3"
                                                required
                                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                                placeholder="Escribe un comentario..."
                                            ></textarea>
                                            <div class="flex justify-end mt-2">
                                                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg transition shadow-md">
                                                    Comentar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Comments List -->
                                <div class="space-y-6">
                                    @forelse($review->comments->whereNull('parent_id') as $comment)
                                        <div class="border-l-4 border-indigo-200 pl-6 py-2">
                                            <div class="flex gap-4">
                                                @if($comment->user->avatar)
                                                    <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-12 h-12 rounded-full border-2 border-gray-200">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold">
                                                        {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="flex-1">
                                                    <div class="bg-gray-50 rounded-xl p-4">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <span class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                                            <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <p class="text-gray-700 whitespace-pre-line">{{ $comment->comment }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-12 text-gray-500">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <p class="text-lg font-medium">No hay comentarios todavía</p>
                                            <p class="text-sm">¡Sé el primero en comentar!</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Author Card -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="text-center">
                            @if($review->user->avatar)
                                <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-24 h-24 rounded-full border-4 border-indigo-100 mx-auto mb-4 shadow-sm">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-4xl mx-auto mb-4 shadow-sm">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $review->user->name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">Miembro de {{ $network->name }}</p>

                            @php
                                $userReviewsCount = $network->reviews->where('user_id', $review->user_id)->count();
                                $userAvgRating = $network->reviews->where('user_id', $review->user_id)->avg('rating');
                            @endphp

                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-indigo-600">{{ $userReviewsCount }}</div>
                                    <div class="text-xs text-gray-600">Reviews</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-amber-500">{{ number_format($userAvgRating, 1) }}</div>
                                    <div class="text-xs text-gray-600">Promedio</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Info -->
                    @if($review->prices && $review->prices->isNotEmpty())
                        @foreach($review->prices as $price)
                            <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl shadow-md p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-bold text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Precio Pagado
                                    </h4>
                                    <div class="text-3xl font-black text-green-700">{{ $price->formatted_price }}</div>
                                </div>

                                @if($price->notes)
                                    <p class="text-sm text-gray-700 mb-3">{{ $price->notes }}</p>
                                @endif

                                @if($price->ticket_photo_url)
                                    <a href="{{ $price->ticket_photo_url }}" target="_blank" class="block">
                                        <div class="bg-white rounded-xl p-3 hover:shadow-lg transition group">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-600">Ver ticket/cuenta</span>
                                                <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    @endif

                    <!-- Restaurant Info -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información
                        </h4>

                        <div class="space-y-3 text-sm">
                            @if($review->restaurant->address)
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $review->restaurant->address }}</span>
                                </div>
                            @endif

                            @if($review->restaurant->phone)
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:{{ $review->restaurant->phone }}" class="text-indigo-600 hover:text-indigo-800">{{ $review->restaurant->phone }}</a>
                                </div>
                            @endif

                            @if($review->restaurant->website)
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                    <a href="{{ $review->restaurant->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 truncate">Sitio web</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- View Restaurant Button -->
                    <a href="{{ route('networks.restaurants.show', [$network, $review->restaurant]) }}" class="block w-full py-3 px-6 text-center bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition shadow-lg">
                        Ver Restaurante Completo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photoModal" class="hidden fixed inset-0 bg-black/95 z-50 flex items-center justify-center p-4" onclick="closePhotoModal()">
        <button onclick="closePhotoModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <button onclick="event.stopPropagation(); prevPhoto()" class="absolute left-4 text-white hover:text-gray-300">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <img id="modalImage" src="" alt="Photo" class="max-w-full max-h-[90vh] object-contain" onclick="event.stopPropagation()">

        <button onclick="event.stopPropagation(); nextPhoto()" class="absolute right-4 text-white hover:text-gray-300">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
            <span id="photoCounter"></span>
        </div>
    </div>

    <script>
        // Tab Switching
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            // Add active class to clicked button
            event.target.classList.add('active', 'border-indigo-600', 'text-indigo-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }

        // Photo Modal
        const photos = @json($photos->pluck('photo_url'));
        let currentPhotoIndex = 0;

        function openPhotoModal(index) {
            currentPhotoIndex = index;
            document.getElementById('photoModal').classList.remove('hidden');
            updateModalPhoto();
        }

        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
        }

        function nextPhoto() {
            currentPhotoIndex = (currentPhotoIndex + 1) % photos.length;
            updateModalPhoto();
        }

        function prevPhoto() {
            currentPhotoIndex = (currentPhotoIndex - 1 + photos.length) % photos.length;
            updateModalPhoto();
        }

        function updateModalPhoto() {
            document.getElementById('modalImage').src = photos[currentPhotoIndex];
            document.getElementById('photoCounter').textContent = `${currentPhotoIndex + 1} / ${photos.length}`;
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('photoModal').classList.contains('hidden')) {
                if (e.key === 'ArrowRight') nextPhoto();
                if (e.key === 'ArrowLeft') prevPhoto();
                if (e.key === 'Escape') closePhotoModal();
            }
        });
    </script>
</x-app-layout>
