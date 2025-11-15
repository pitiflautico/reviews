<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    Crear Nueva Red
                </h2>
                <p class="mt-1 text-sm text-gray-600">Crea un espacio privado para compartir reseñas</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('networks.store') }}" class="space-y-6">
                @csrf

                <!-- Network Name -->
                <x-form-section
                    title="Información Básica"
                    description="Dale un nombre y descripción a tu red"
                    gradient="from-indigo-500 to-purple-600"
                >
                    <x-slot:icon>
                        <x-icons.users class="text-2xl text-white" />
                    </x-slot:icon>

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <x-icons.users class="inline mr-1" /> Nombre de la Red
                            </label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder='Ej: "Familia López", "Amigos Foodies"'
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                Descripción (opcional)
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Describe brevemente el propósito de esta red..."
                            >{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>
                </x-form-section>

                <!-- Settings -->
                <x-form-section
                    title="Configuración"
                    description="Ajusta los permisos de tu red"
                    gradient="from-emerald-500 to-teal-600"
                >
                    <x-slot:icon>
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </x-slot:icon>

                    <div class="flex items-start">
                        <div class="flex items-center h-6">
                            <input
                                id="allow_member_invites"
                                name="allow_member_invites"
                                type="checkbox"
                                value="1"
                                {{ old('allow_member_invites') ? 'checked' : '' }}
                                class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                        </div>
                        <div class="ml-4">
                            <label for="allow_member_invites" class="font-semibold text-gray-900">
                                Permitir que los miembros inviten a otros
                            </label>
                            <p class="text-sm text-gray-600 mt-1">
                                Si está desactivado, solo los administradores y propietarios podrán enviar invitaciones.
                            </p>
                        </div>
                    </div>
                </x-form-section>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200">
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
                    >
                        Crear Red
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
