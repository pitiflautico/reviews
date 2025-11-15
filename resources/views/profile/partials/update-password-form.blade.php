<section>
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Actualizar Contraseña
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                Contraseña Actual
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <!-- New Password -->
        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700 mb-2">
                Nueva Contraseña
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                Confirmar Contraseña
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-4">
            <button
                type="submit"
                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
            >
                Guardar Contraseña
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-medium"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
