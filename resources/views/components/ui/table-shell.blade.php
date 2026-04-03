@props([
    'tone' => 'white',
])

<x-ui.card :tone="$tone" {{ $attributes }}>
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</x-ui.card>
