@props([
    'tone' => 'white',
])

<x-ui.card :tone="$tone" {{ $attributes }}>
    <div class="overflow-hidden">
        {{ $slot }}
    </div>
</x-ui.card>
