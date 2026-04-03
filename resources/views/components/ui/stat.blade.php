@props([
    'label',
    'value',
])

<x-ui.card {{ $attributes->class('p-5') }}>
    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">{{ $label }}</p>
    <p class="mt-3 text-2xl font-semibold tracking-tight text-slate-950">{{ $value }}</p>
    @if (trim((string) $slot) !== '')
        <div class="mt-2 text-sm leading-6 text-slate-600">
            {{ $slot }}
        </div>
    @endif
</x-ui.card>
