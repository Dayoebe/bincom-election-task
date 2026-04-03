@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between') }}>
    <div class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Bincom Election Dashboard</p>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">{{ $title }}</h1>
        @if ($description)
            <p class="max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
