<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('networks.show', $network) }}"
                   class="text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
                        Miembros de {{ $network->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">Gestiona los miembros de tu red</p>
                </div>
            </div>

            @if(in_array($memberRole, ['owner', 'admin']) || ($network->allow_member_invites && $memberRole === 'member'))
                <a href="{{ route('networks.members.invite', $network) }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Invitar Miembro
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($members as $member)
                            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl hover:shadow-lg transition">
                                <div class="flex items-center gap-4">
                                    @if($member->avatar)
                                        <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-16 h-16 rounded-full border-4 border-indigo-200">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-2xl">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900">{{ $member->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $member->email }}</p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="inline-flex items-center px-3 py-1 {{ $member->pivot->role === 'owner' ? 'bg-yellow-100 text-yellow-800' : ($member->pivot->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800') }} rounded-full text-xs font-semibold">
                                                @if($member->pivot->role === 'owner')
                                                    <x-icons.star class="mr-1 text-xs" />
                                                @elseif($member->pivot->role === 'admin')
                                                    <x-icons.shield class="mr-1 text-xs" />
                                                @else
                                                    <x-icons.users class="mr-1 text-xs" />
                                                @endif
                                                {{ ucfirst($member->pivot->role) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Miembro desde {{ $member->pivot->joined_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if($member->pivot->role !== 'owner' && in_array($memberRole, ['owner', 'admin']))
                                    <div class="flex gap-2">
                                        @if($memberRole === 'owner')
                                            <form method="POST" action="{{ route('networks.members.updateRole', [$network, $member]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="role" value="{{ $member->pivot->role === 'admin' ? 'member' : 'admin' }}">
                                                <button
                                                    type="submit"
                                                    class="px-4 py-2 {{ $member->pivot->role === 'admin' ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700' }} font-medium rounded-lg transition text-sm"
                                                >
                                                    {{ $member->pivot->role === 'admin' ? 'Degradar a Miembro' : 'Promover a Admin' }}
                                                </button>
                                            </form>
                                        @endif

                                        @if($memberRole === 'owner' || ($memberRole === 'admin' && $member->pivot->role !== 'admin'))
                                            <form method="POST" action="{{ route('networks.members.destroy', [$network, $member]) }}" onsubmit="return confirm('¿Estás seguro de eliminar este miembro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition text-sm"
                                                >
                                                    Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
