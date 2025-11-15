<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    {{ $network->name }}
                </h2>
                @if($network->description)
                    <p class="mt-1 text-sm text-gray-600">{{ $network->description }}</p>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('networks.members.index', $network) }}"
                   class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200 shadow-sm">
                    <x-icons.users class="mr-2" />
                    Miembros
                </a>
                @if($memberRole === 'owner')
                    <a href="{{ route('networks.edit', $network) }}"
                       class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Configurar
                    </a>
                @endif
                <a href="{{ route('networks.reviews.create', $network) }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Reseña
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Stats Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-10">
                        <x-icons.users class="text-8xl" />
                    </div>
                    <div class="relative z-10">
                        <p class="text-white/80 text-sm font-medium mb-2">Miembros</p>
                        <p class="text-5xl font-bold mb-1">{{ $network->members->count() }}</p>
                        <div class="flex items-center justify-between mt-3 gap-2">
                            <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold">
                                <x-icons.users class="mr-1 text-sm" />
                                {{ ucfirst($memberRole) }}
                            </span>
                            @if(in_array($memberRole, ['owner', 'admin']) || ($network->allow_member_invites && $memberRole === 'member'))
                                <a href="{{ route('networks.members.invite', $network) }}"
                                   class="inline-flex items-center px-3 py-1 bg-white/90 hover:bg-white text-indigo-600 rounded-lg text-xs font-semibold transition shadow-sm">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Invitar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl shadow-lg p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-10">
                        <x-icons.star class="text-8xl" />
                    </div>
                    <div class="relative z-10">
                        <p class="text-white/80 text-sm font-medium mb-2">Reseñas</p>
                        <p class="text-5xl font-bold mb-1">{{ $network->reviews->count() }}</p>
                        <div class="flex items-center mt-3 text-white/90 text-sm">
                            <x-icons.star class="mr-1" />
                            Promedio: {{ $network->reviews->count() > 0 ? number_format($network->reviews->avg('rating'), 1) : '0.0' }}
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-10">
                        <x-icons.utensils class="text-8xl" />
                    </div>
                    <div class="relative z-10">
                        <p class="text-white/80 text-sm font-medium mb-2">Restaurantes</p>
                        <p class="text-5xl font-bold mb-1">{{ $network->reviews->pluck('restaurant_id')->unique()->count() }}</p>
                        <div class="flex items-center mt-3 text-white/90 text-sm">
                            <x-icons.utensils class="mr-1" />
                            Únicos
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Tabs -->
                <div class="border-b border-gray-200 bg-gray-50">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <button
                            onclick="switchTab('reviews')"
                            id="tab-reviews"
                            class="tab-button active flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                        >
                            <x-icons.star class="inline mr-2" />
                            Reseñas Recientes
                        </button>
                        <button
                            onclick="switchTab('members')"
                            id="tab-members"
                            class="tab-button flex-1 py-4 px-6 text-center border-b-2 font-medium text-sm transition"
                        >
                            <x-icons.users class="inline mr-2" />
                            Miembros
                        </button>
                    </nav>
                </div>

                <!-- Reviews Tab -->
                <div id="content-reviews" class="tab-content p-8">
                    @if($network->reviews->isEmpty())
                        <div class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-6">
                                <x-icons.star class="text-4xl text-indigo-600" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay reseñas todavía</h3>
                            <p class="text-gray-600 mb-8">
                                Comienza añadiendo tu primera reseña de restaurante.
                            </p>
                            <a href="{{ route('networks.reviews.create', $network) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Nueva Reseña
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            @foreach($network->reviews->sortByDesc('created_at')->take(6) as $review)
                                <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-xl font-bold text-gray-900 mb-1">
                                                {{ $review->restaurant->name }}
                                            </h4>
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                @if($review->restaurant->city)
                                                    <span class="flex items-center">
                                                        <x-icons.location class="mr-1 text-xs" />
                                                        {{ $review->restaurant->city }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl px-4 py-2 flex items-center gap-2">
                                            <x-icons.star class="text-yellow-300" />
                                            <span class="text-xl font-bold">{{ $review->rating }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 mb-3 text-sm text-gray-500">
                                        @if($review->user->avatar)
                                            <img src="{{ $review->user->avatar }}" alt="{{ $review->user->name }}" class="w-8 h-8 rounded-full border-2 border-indigo-200">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-xs">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-700">{{ $review->user->name }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span>{{ $review->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if($review->comment)
                                        <p class="text-gray-700 mb-4 line-clamp-2">
                                            {{ $review->comment }}
                                        </p>
                                    @endif

                                    <a href="{{ route('networks.reviews.show', [$network, $review]) }}"
                                       class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm transition">
                                        Ver detalles
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        @if($network->reviews->count() > 6)
                            <div class="text-center pt-6 border-t border-gray-200">
                                <a href="{{ route('networks.reviews.index', $network) }}"
                                   class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200">
                                    Ver todas las reseñas
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Members Tab -->
                <div id="content-members" class="tab-content hidden p-8">
                    @if(in_array($memberRole, ['owner', 'admin']) || ($network->allow_member_invites && $memberRole === 'member'))
                        <div class="flex justify-end mb-6">
                            <a href="{{ route('networks.members.invite', $network) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Invitar Miembro
                            </a>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($network->members as $member)
                            <div class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                                <div class="flex items-center gap-4">
                                    @if($member->avatar)
                                        <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-16 h-16 rounded-full border-4 border-indigo-200">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $member->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $member->email }}</p>
                                        <span class="inline-block mt-2 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                                            {{ ucfirst($member->pivot->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('active', 'border-indigo-500', 'text-indigo-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }

        // Initialize first tab as active
        document.getElementById('tab-reviews').classList.add('border-indigo-500', 'text-indigo-600');
        document.getElementById('tab-reviews').classList.remove('border-transparent', 'text-gray-500');
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
