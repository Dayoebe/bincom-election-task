@props([
    'tone' => 'white',
])

@php
    $tones = [
        'white' => 'border-white/90 bg-white/88 shadow-slate-300/45',
        'amber' => 'border-amber-300 bg-amber-50/90 shadow-amber-200/55',
        'orange' => 'border-orange-300 bg-orange-50/90 shadow-orange-200/55',
        'lime' => 'border-lime-300 bg-lime-50/90 shadow-lime-200/55',
        'emerald' => 'border-emerald-300 bg-emerald-50/90 shadow-emerald-200/55',
        'teal' => 'border-teal-300 bg-teal-50/90 shadow-teal-200/55',
        'cyan' => 'border-cyan-300 bg-cyan-50/90 shadow-cyan-200/55',
        'sky' => 'border-sky-300 bg-sky-50/90 shadow-sky-200/55',
        'blue' => 'border-blue-300 bg-blue-50/90 shadow-blue-200/55',
        'indigo' => 'border-indigo-300 bg-indigo-50/90 shadow-indigo-200/55',
        'violet' => 'border-violet-300 bg-violet-50/90 shadow-violet-200/55',
        'pink' => 'border-pink-300 bg-pink-50/90 shadow-pink-200/55',
        'rose' => 'border-rose-300 bg-rose-50/90 shadow-rose-200/55',
        'stone' => 'border-stone-300 bg-stone-50/92 shadow-stone-200/55',
        'slate' => 'border-slate-300 bg-slate-50/92 shadow-slate-300/45',
    ];
@endphp

<div {{ $attributes->class('rounded-[2rem] border-2 backdrop-blur-sm shadow-[0_18px_45px_rgba(15,23,42,0.08)] '.($tones[$tone] ?? $tones['white'])) }}>
    {{ $slot }}
</div>
