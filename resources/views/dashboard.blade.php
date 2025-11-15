<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                Mis Redes de Reseñas
            </h2>
            <a href="{{ route('networks.create') }}"
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Nueva Red
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($networks->isEmpty())
                <!-- Empty state -->
                <div class="bg-white rounded-xl shadow-md p-16 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-6">
                        <x-icons.users class="text-5xl text-indigo-600" />
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-3">No tienes redes aún</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Comienza creando tu primera red privada de reseñas gastronómicas y comparte tus experiencias con amigos y familia.
                    </p>
                    <a href="{{ route('networks.create') }}"
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Mi Primera Red
                    </a>
                </div>
            @else
                <!-- Networks grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($networks as $network)
                        <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
                            <!-- Header with gradient -->
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                                <div class="flex items-start justify-between mb-3">
                                    <h3 class="text-2xl font-bold flex-1">
                                        {{ $network->name }}
                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold">
                                        {{ ucfirst($network->pivot->role ?? 'member') }}
                                    </span>
                                </div>
                                @if($network->description)
                                    <p class="text-white/90 text-sm line-clamp-2">
                                        {{ $network->description }}
                                    </p>
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center gap-2 text-gray-600 text-xs mb-1">
                                            <x-icons.users class="text-sm" />
                                            Miembros
                                        </div>
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ $network->members_count ?? $network->members->count() }}
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center gap-2 text-gray-600 text-xs mb-1">
                                            <x-icons.star class="text-sm" />
                                            Reseñas
                                        </div>
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ $network->reviews_count ?? $network->reviews->count() }}
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('networks.show', $network) }}"
                                   class="block w-full text-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-lg transition duration-200">
                                    Ver Red
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
