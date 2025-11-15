<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $network->name }}
                </h2>
                @if($network->description)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $network->description }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                @if($memberRole === 'owner')
                    <a href="{{ route('networks.edit', $network) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Configurar
                    </a>
                @endif
                <span class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-md font-semibold text-xs uppercase tracking-widest">
                    {{ ucfirst($memberRole) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Miembros
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $network->members->count() }}
                        </dd>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Reseñas
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $network->reviews->count() }}
                        </dd>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                            Restaurantes
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $network->reviews->pluck('restaurant_id')->unique()->count() }}
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="#" class="border-indigo-500 text-indigo-600 dark:text-indigo-400 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Reseñas Recientes
                            </a>
                            <a href="#" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Miembros
                            </a>
                        </nav>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mt-6">
                        @if($network->reviews->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay reseñas todavía</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Comienza añadiendo tu primera reseña de restaurante.
                                </p>
                                <div class="mt-6">
                                    <button type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        + Nueva Reseña
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($network->reviews->take(10) as $review)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                        <div class="flex justify-between">
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $review->restaurant->name }}
                                                </h4>
                                                <div class="flex items-center mt-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="h-5 w-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $review->user->name }}<br>
                                                {{ $review->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="mt-2 text-gray-600 dark:text-gray-300">
                                                {{ $review->comment }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
