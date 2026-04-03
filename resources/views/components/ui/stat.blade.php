@props([
    'label',
    'value',
    'tone' => 'white',
])

@php
    $accents = [
        'white' => ['dot' => 'bg-slate-500', 'label' => 'text-slate-600'],
        'amber' => ['dot' => 'bg-amber-500', 'label' => 'text-amber-700'],
        'orange' => ['dot' => 'bg-orange-500', 'label' => 'text-orange-700'],
        'lime' => ['dot' => 'bg-lime-500', 'label' => 'text-lime-700'],
        'emerald' => ['dot' => 'bg-emerald-500', 'label' => 'text-emerald-700'],
        'teal' => ['dot' => 'bg-teal-500', 'label' => 'text-teal-700'],
        'cyan' => ['dot' => 'bg-cyan-500', 'label' => 'text-cyan-700'],
        'sky' => ['dot' => 'bg-sky-500', 'label' => 'text-sky-700'],
        'blue' => ['dot' => 'bg-blue-500', 'label' => 'text-blue-700'],
        'indigo' => ['dot' => 'bg-indigo-500', 'label' => 'text-indigo-700'],
        'violet' => ['dot' => 'bg-violet-500', 'label' => 'text-violet-700'],
        'pink' => ['dot' => 'bg-pink-500', 'label' => 'text-pink-700'],
        'rose' => ['dot' => 'bg-rose-500', 'label' => 'text-rose-700'],
        'stone' => ['dot' => 'bg-stone-500', 'label' => 'text-stone-700'],
        'slate' => ['dot' => 'bg-slate-600', 'label' => 'text-slate-700'],
    ];

    $accent = $accents[$tone] ?? $accents['white'];
@endphp

<x-ui.card :tone="$tone" {{ $attributes->class('p-5') }}>
    <div class="flex items-center gap-3">
        <span class="inline-flex h-3.5 w-3.5 rounded-full {{ $accent['dot'] }}"></span>
        <p class="text-xs font-semibold uppercase tracking-[0.24em] {{ $accent['label'] }}">{{ $label }}</p>
    </div>
    <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-950">{{ $value }}</p>
    @if (trim((string) $slot) !== '')
        <div class="mt-3 text-sm leading-6 text-slate-700">
            {{ $slot }}
        </div>
    @endif
</x-ui.card>
