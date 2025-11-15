<div class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">
            ¿Listo para Empezar?
        </h2>
        <p class="text-xl md:text-2xl mb-8 text-indigo-100">
            Únete a miles de gastrónomos que ya comparten sus experiencias
        </p>

        @auth
            <a href="{{ route('dashboard') }}" class="inline-block bg-white text-indigo-600 font-semibold px-10 py-4 rounded-lg text-lg hover:bg-indigo-50 transition duration-200 shadow-xl">
                Ir a Mi Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-block bg-white text-indigo-600 font-semibold px-10 py-4 rounded-lg text-lg hover:bg-indigo-50 transition duration-200 shadow-xl">
                Crear Cuenta Gratis
            </a>
            <p class="mt-4 text-indigo-100">
                ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="underline font-semibold hover:text-white">Inicia sesión aquí</a>
            </p>
        @endauth
    </div>
</div>
