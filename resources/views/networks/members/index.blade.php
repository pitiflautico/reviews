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
                    <p class="mt-1 text-sm text-gray-600">{{ $members->count() }} {{ $members->count() === 1 ? 'miembro' : 'miembros' }} en total</p>
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Miembro
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Fecha de Invitación
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Reviews
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Promedio
                                </th>
                                @if(in_array($memberRole, ['owner', 'admin']))
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($members as $member)
                                @php
                                    $memberReviews = $network->reviews->where('user_id', $member->id);
                                    $reviewCount = $memberReviews->count();
                                    $avgRating = $reviewCount > 0 ? $memberReviews->avg('rating') : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($member->avatar)
                                                <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-14 h-14 rounded-full border-2 border-indigo-200 shadow-sm">
                                            @else
                                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-xl flex-shrink-0 shadow-sm">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-base font-bold text-gray-900">{{ $member->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $member->pivot->joined_at ? $member->pivot->joined_at->format('d/m/Y') : 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $member->pivot->joined_at ? $member->pivot->joined_at->diffForHumans() : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $roleConfig = [
                                                'owner' => [
                                                    'bg' => 'bg-gradient-to-r from-yellow-100 to-amber-100',
                                                    'text' => 'text-yellow-800',
                                                    'border' => 'border-yellow-300',
                                                    'icon' => 'star'
                                                ],
                                                'admin' => [
                                                    'bg' => 'bg-gradient-to-r from-indigo-100 to-purple-100',
                                                    'text' => 'text-indigo-800',
                                                    'border' => 'border-indigo-300',
                                                    'icon' => 'shield'
                                                ],
                                                'member' => [
                                                    'bg' => 'bg-gradient-to-r from-gray-100 to-slate-100',
                                                    'text' => 'text-gray-800',
                                                    'border' => 'border-gray-300',
                                                    'icon' => 'users'
                                                ],
                                            ];
                                            $config = $roleConfig[$member->pivot->role] ?? $roleConfig['member'];
                                        @endphp
                                        <span class="inline-flex items-center px-4 py-2 border {{ $config['border'] }} {{ $config['bg'] }} {{ $config['text'] }} rounded-lg text-xs font-bold uppercase tracking-wide shadow-sm">
                                            @if($config['icon'] === 'star')
                                                <x-icons.star class="mr-1.5 text-sm" />
                                            @elseif($config['icon'] === 'shield')
                                                <x-icons.shield class="mr-1.5 text-sm" />
                                            @else
                                                <x-icons.users class="mr-1.5 text-sm" />
                                            @endif
                                            {{ ucfirst($member->pivot->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $reviewCount > 0 ? 'bg-gradient-to-br from-amber-100 to-orange-100' : 'bg-gray-100' }} mr-3">
                                                <x-icons.star class="{{ $reviewCount > 0 ? 'text-amber-600' : 'text-gray-400' }}" />
                                            </div>
                                            <div>
                                                <div class="text-lg font-bold text-gray-900">{{ $reviewCount }}</div>
                                                <div class="text-xs text-gray-500">{{ $reviewCount === 1 ? 'review' : 'reviews' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reviewCount > 0)
                                            <div class="flex flex-col gap-2">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <x-icons.star class="{{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-gray-300' }} text-sm" />
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-2xl font-bold text-gray-900">{{ number_format($avgRating, 1) }}</span>
                                                    <span class="text-xs text-gray-500">/ 5.0</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Sin reviews</span>
                                        @endif
                                    </td>
                                    @if(in_array($memberRole, ['owner', 'admin']))
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($member->pivot->role !== 'owner')
                                                <div class="flex gap-2 justify-end">
                                                    @if($memberRole === 'owner')
                                                        <form method="POST" action="{{ route('networks.members.updateRole', [$network, $member]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="role" value="{{ $member->pivot->role === 'admin' ? 'member' : 'admin' }}">
                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center px-3 py-2 {{ $member->pivot->role === 'admin' ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-indigo-100 hover:bg-indigo-200 text-indigo-700' }} font-semibold rounded-lg transition text-xs"
                                                                title="{{ $member->pivot->role === 'admin' ? 'Degradar a Miembro' : 'Promover a Admin' }}"
                                                            >
                                                                @if($member->pivot->role === 'admin')
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                                                    </svg>
                                                                @endif
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($memberRole === 'owner' || ($memberRole === 'admin' && $member->pivot->role !== 'admin'))
                                                        <form method="POST" action="{{ route('networks.members.destroy', [$network, $member]) }}" onsubmit="return confirm('¿Estás seguro de eliminar este miembro?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-lg transition text-xs"
                                                                title="Eliminar miembro"
                                                            >
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Propietario</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($members->isEmpty())
                    <div class="text-center py-16 px-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full mb-6">
                            <x-icons.users class="text-4xl text-indigo-600" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No hay miembros todavía</h3>
                        <p class="text-gray-600 mb-8">
                            Invita a tus amigos a unirse a tu red de reviews.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
