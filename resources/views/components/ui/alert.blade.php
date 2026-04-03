@props([
    'type' => 'info',
    'dismissible' => true,
])

@php
    $styles = match ($type) {
        'success' => 'border-emerald-300 bg-emerald-100/90 text-emerald-950',
        'error' => 'border-rose-300 bg-rose-100/90 text-rose-950',
        'warning' => 'border-amber-300 bg-amber-100/90 text-amber-950',
        default => 'border-cyan-300 bg-cyan-100/90 text-cyan-950',
    };

    $iconStyles = match ($type) {
        'success' => 'bg-emerald-500 text-white',
        'error' => 'bg-rose-500 text-white',
        'warning' => 'bg-amber-500 text-slate-950',
        default => 'bg-cyan-500 text-white',
    };
@endphp

<div
    x-data="{
        open: true,
        leaving: false,
        close() {
            this.leaving = true;
            setTimeout(() => this.open = false, 260);
        }
    }"
    x-show="open"
    x-cloak
    :class="leaving ? 'animate__fadeOutUp' : 'animate__fadeInDown'"
    {{ $attributes->class('animate__animated rounded-[1.6rem] border-2 px-4 py-4 text-sm leading-6 shadow-sm shadow-slate-200/60 '.$styles) }}
>
    <div class="flex items-start gap-3">
        <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-black {{ $iconStyles }}">
            !
        </span>

        <div class="min-w-0 flex-1">
            {{ $slot }}
        </div>

        @if ($dismissible)
            <button
                type="button"
                @click="close()"
                class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/70 bg-white/60 text-slate-600 transition hover:bg-white hover:text-slate-950"
                aria-label="Dismiss alert"
            >
                ×
            </button>
        @endif
    </div>
</div>
