@props(['name' => 'rating', 'value' => null, 'required' => true])

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-3">Puntuación</label>
    <div class="flex gap-2">
        @for($i = 1; $i <= 5; $i++)
            <label class="cursor-pointer group">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $i }}"
                    {{ $required ? 'required' : '' }}
                    class="sr-only peer"
                    {{ $value == $i ? 'checked' : '' }}
                />
                <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-indigo-300 transition">
                    <x-icons.star class="text-3xl peer-checked:text-yellow-400 text-gray-300 group-hover:text-yellow-300" />
                    <span class="mt-2 font-bold text-gray-700">{{ $i }}</span>
                </div>
            </label>
        @endfor
    </div>
</div>
