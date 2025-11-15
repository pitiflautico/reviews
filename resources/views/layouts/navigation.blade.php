<nav x-data="{ open: false }" class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="bg-white/20 backdrop-blur rounded-xl p-2 group-hover:bg-white/30 transition">
                            <x-icons.utensils class="text-3xl text-white" />
                        </div>
                        <span class="text-white font-bold text-2xl hidden sm:block">GastroReviews</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-4 py-2 {{ request()->routeIs('dashboard') ? 'bg-white/30 backdrop-blur' : 'hover:bg-white/20' }} text-white font-semibold rounded-lg transition">
                        <x-icons.users class="mr-2" />
                        Mis Redes
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 px-4 py-2 bg-white/20 backdrop-blur hover:bg-white/30 text-white font-semibold rounded-xl transition">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full border-2 border-white/50">
                            @else
                                <div class="w-8 h-8 rounded-full bg-white/30 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="hidden md:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-3 border-b border-gray-200">
                            <div class="font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-sm text-gray-600">{{ Auth::user()->email }}</div>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-white hover:bg-white/20 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white/10 backdrop-blur">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-white/30' : 'hover:bg-white/20' }} text-white font-semibold rounded-lg transition">
                <x-icons.users class="mr-2 inline" />
                Mis Redes
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-white/20">
            <div class="px-4 mb-3">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-12 h-12 rounded-full border-2 border-white/50">
                    @else
                        <div class="w-12 h-12 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-semibold text-white">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-white/80">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="space-y-1 px-4">
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-3 hover:bg-white/20 text-white font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Perfil
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                            class="block w-full text-left px-4 py-3 hover:bg-white/20 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
