@props([
    'type' => 'info',
])

@php
    $styles = match ($type) {
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'error' => 'border-rose-200 bg-rose-50 text-rose-900',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        default => 'border-sky-200 bg-sky-50 text-sky-900',
    };
@endphp

<div {{ $attributes->class('rounded-2xl border px-4 py-3 text-sm leading-6 '.$styles) }}>
    {{ $slot }}
</div>
