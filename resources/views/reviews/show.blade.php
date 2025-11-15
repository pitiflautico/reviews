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

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Restaurant Header Card -->
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-xl shadow-xl p-8 text-white relative overflow-hidden">
                <!-- Decorative Elements -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-10 right-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
                </div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex-1">
                            <h1 class="text-4xl font-bold mb-3">{{ $review->restaurant->name }}</h1>
                            <div class="flex flex-wrap gap-4 text-white/90">
                                @if($review->restaurant->city)
                                    <span class="flex items-center">
                                        <x-icons.location class="mr-1" />
                                        {{ $review->restaurant->city }}
                                    </span>
                                @endif
                                @if($review->restaurant->category)
                                    <span class="flex items-center">
                                        <x-icons.utensils class="mr-1" />
                                        {{ $review->restaurant->category }}
                                    </span>
                                @endif
                                @if($review->restaurant->address)
                                    <span class="flex items-center">
                                        <x-icons.map-marker class="mr-1" />
                                        {{ $review->restaurant->address }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur rounded-2xl px-6 py-4 text-center">
                            <div class="text-5xl font-bold">{{ $review->rating }}</div>
                            <div class="flex mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <x-icons.star class="{{ $i <= $review->rating ? 'text-yellow-300' : 'text-white/30' }}" />
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Details -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <!-- Author Info -->
                <div class="flex items-center justify-between pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-4">
                        @if($review->user->avatar)
                            <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-16 h-16 rounded-full border-4 border-indigo-200">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $review->user->name }}</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                <span class="flex items-center">
                                    <x-icons.clock class="mr-1" />
                                    {{ $review->created_at->diffForHumans() }}
                                </span>
                                @if($review->date_of_visit)
                                    <span class="text-gray-400">•</span>
                                    <span>Visitado el {{ $review->date_of_visit->format('d/m/Y') }}</span>
                                @endif
                                @if($review->meal_type)
                                    <span class="text-gray-400">•</span>
                                    <span class="capitalize px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold">
                                        {{ $review->meal_type == 'breakfast' ? 'Desayuno' : ($review->meal_type == 'lunch' ? 'Almuerzo' : 'Cena') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($review->created_at != $review->updated_at)
                        <span class="text-sm text-gray-500 italic">
                            Editado {{ $review->updated_at->diffForHumans() }}
                        </span>
                    @endif
                </div>

                <!-- Review Comment -->
                <div class="py-6">
                    <p class="text-lg text-gray-800 leading-relaxed whitespace-pre-line">{{ $review->comment }}</p>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Comentarios
                        <span class="ml-2 text-lg text-gray-500">({{ $review->comments->count() }})</span>
                    </h3>
                </div>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('networks.reviews.comments.store', [$network, $review]) }}" class="mb-8">
                    @csrf
                    <div class="flex gap-4">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full border-2 border-indigo-200">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold flex-shrink-0">
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
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg transition duration-200"
                                >
                                    Comentar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Comments List -->
                <div class="space-y-6">
                    @forelse($review->comments->whereNull('parent_id') as $comment)
                        <div class="border-l-4 border-indigo-200 pl-4">
                            <div class="flex gap-4">
                                @if($comment->user->avatar)
                                    <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-full border-2 border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-400 to-gray-500 flex items-center justify-center text-white font-bold flex-shrink-0">
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

                                    <!-- Replies -->
                                    @if($comment->replies->isNotEmpty())
                                        <div class="mt-4 ml-6 space-y-4">
                                            @foreach($comment->replies as $reply)
                                                <div class="flex gap-3">
                                                    @if($reply->user->avatar)
                                                        <img src="{{ $reply->user->avatar }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-full border-2 border-gray-200">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex-1">
                                                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <span class="font-semibold text-sm text-gray-900">{{ $reply->user->name }}</span>
                                                                <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $reply->comment }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p>No hay comentarios todavía. ¡Sé el primero en comentar!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
