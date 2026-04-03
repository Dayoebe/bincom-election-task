@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between') }}>
    <div class="space-y-2">
        <div class="flex flex-wrap items-center gap-3">
            <p class="pattern-badge border-slate-900 bg-slate-900 text-white">Bincom Election Dashboard</p>
            <span class="pattern-badge border-amber-300 bg-amber-100 text-amber-900">Color-forward redesign</span>
        </div>
        <h1 class="text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">{{ $title }}</h1>
        @if ($description)
            <p class="max-w-3xl text-base leading-7 text-slate-700">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
