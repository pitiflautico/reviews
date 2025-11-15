<section>
    <header class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Información del Perfil
        </h2>

        <p class="mt-2 text-sm text-gray-600">
            Actualiza la información de tu cuenta y dirección de correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                Nombre
            </label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <p class="text-sm text-gray-800">
                        Tu dirección de correo electrónico no está verificada.

                        <button form="send-verification" class="underline text-indigo-600 hover:text-indigo-800 font-medium">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Avatar Display -->
        @if($user->avatar)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Avatar de Google
                </label>
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full border-4 border-indigo-200">
            </div>
        @endif

        <!-- Save Button -->
        <div class="flex items-center gap-4 pt-4">
            <button
                type="submit"
                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg"
            >
                Guardar Cambios
            </button>

            @if (session('status') === 'profile-updated')
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
