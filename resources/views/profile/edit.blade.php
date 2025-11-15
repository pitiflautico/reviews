<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                Mi Perfil
            </h2>
            <p class="mt-1 text-sm text-gray-600">Administra tu información personal y configuración de cuenta</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white rounded-xl shadow-md p-8">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
