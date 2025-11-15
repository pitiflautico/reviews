@props(['review', 'network'])

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
    <!-- Restaurant Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="text-2xl font-bold mb-2">{{ $review->restaurant->name }}</h3>
                <div class="flex items-center gap-4 text-sm text-white/90">
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
                </div>
            </div>
            <!-- Rating Badge -->
            <div class="bg-white/20 backdrop-blur rounded-xl px-4 py-2 flex items-center gap-2">
                <x-icons.star class="text-yellow-300" />
                <span class="text-2xl font-bold">{{ $review->rating }}</span>
            </div>
        </div>
    </div>

    <!-- Review Content -->
    <div class="p-6">
        <!-- Meta Information -->
        <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
            <div class="flex items-center">
                @if($review->user->avatar)
                    <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-8 h-8 rounded-full mr-2 border-2 border-indigo-200">
                @else
                    <div class="w-8 h-8 rounded-full mr-2 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                @endif
                <span class="font-medium text-gray-700">{{ $review->user->name }}</span>
            </div>
            <span class="text-gray-400">•</span>
            <span class="flex items-center">
                <x-icons.clock class="mr-1" />
                {{ $review->created_at->diffForHumans() }}
            </span>
            @if($review->date_of_visit)
                <span class="text-gray-400">•</span>
                <span>Visitado: {{ $review->date_of_visit->format('d/m/Y') }}</span>
            @endif
            @if($review->meal_type)
                <span class="text-gray-400">•</span>
                <span class="capitalize">{{ ucfirst($review->meal_type) }}</span>
            @endif
        </div>

        <!-- Star Rating -->
        <div class="flex items-center mb-3">
            @for($i = 1; $i <= 5; $i++)
                <x-icons.star class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" />
            @endfor
        </div>

        <!-- Comment -->
        @if($review->comment)
            <p class="text-gray-700 leading-relaxed mb-4">
                {{ Str::limit($review->comment, 200) }}
            </p>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('networks.reviews.show', [$network, $review]) }}"
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition">
                Ver detalles
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            @if($review->user_id === Auth::id())
                <div class="flex gap-2">
                    <a href="{{ route('networks.reviews.edit', [$network, $review]) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
