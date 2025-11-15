<section class="space-y-6">
    <header>
        <h2 class="text-2xl font-bold text-red-600 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Eliminar Cuenta
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            Una vez eliminada tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition duration-200"
    >
        Eliminar Cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                ¿Estás seguro de que deseas eliminar tu cuenta?
            </h2>

            <p class="text-sm text-gray-600 mb-6">
                Una vez eliminada tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Introduce tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.
            </p>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Contraseña
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Contraseña"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-transparent transition"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border border-gray-300 transition duration-200"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition duration-200"
                >
                    Eliminar Cuenta
                </button>
            </div>
        </form>
    </x-modal>
</section>
