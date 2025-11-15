@props(['title', 'description', 'icon', 'gradient' => 'from-indigo-500 to-purple-600'])

<div class="bg-white rounded-xl shadow-md p-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="bg-gradient-to-br {{ $gradient }} rounded-lg p-3">
            {{ $icon }}
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">{{ $title }}</h3>
            <p class="text-sm text-gray-600">{{ $description }}</p>
        </div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
