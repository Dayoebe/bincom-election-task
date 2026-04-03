@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between') }}>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
            <p class="pattern-badge border-slate-900 bg-slate-900 text-white">Bincom Election Dashboard</p>
            <span class="pattern-badge border-slate-200 bg-white text-slate-600">Livewire + Tailwind</span>
        </div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl lg:text-5xl">{{ $title }}</h1>
        @if ($description)
            <p class="max-w-3xl text-sm leading-6 text-slate-700 sm:text-base sm:leading-7">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 lg:justify-end">
            {{ $actions }}
        </div>
    @endisset
</div>
