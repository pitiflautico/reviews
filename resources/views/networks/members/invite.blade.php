<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('networks.members.index', $network) }}"
               class="text-gray-600 hover:text-gray-900 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                    Invitar Miembro
                </h2>
                <p class="mt-1 text-sm text-gray-600">{{ $network->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('networks.members.store', $network) }}" class="space-y-6">
                @csrf

                <!-- User Info -->
                <x-form-section
                    title="Información del Miembro"
                    description="Ingresa el email del usuario que deseas invitar"
                    gradient="from-indigo-500 to-purple-600"
                >
                    <x-slot:icon>
                        <x-icons.user-plus class="text-2xl text-white" />
                    </x-slot:icon>

                    <div class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email del Usuario
                            </label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="usuario@example.com"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            <p class="mt-2 text-sm text-gray-600">
                                El usuario debe estar registrado en la plataforma para poder ser invitado.
                            </p>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                Rol
                            </label>
                            <select
                                id="role"
                                name="role"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            >
                                <option value="member" {{ old('role') == 'member' ? 'selected' : '' }}>Miembro</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            <p class="mt-2 text-sm text-gray-600">
                                Los administradores pueden gestionar miembros y configurar la red. Solo el propietario puede promover administradores.
                            </p>
                        </div>
                    </div>
                </x-form-section>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('networks.members.index', $network) }}"
                       class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200">
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
                    >
                        Enviar Invitación
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
