<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
    </head>
    <body x-data="appShell()" class="min-h-screen text-slate-900 antialiased">
        @php
            $navigation = [
                [
                    'label' => 'Polling Unit Result',
                    'route' => 'polling-unit-results',
                    'active' => 'border-cyan-500 bg-cyan-500 text-white shadow-cyan-200',
                    'idle' => 'border-cyan-200 bg-cyan-100 text-cyan-900 hover:bg-cyan-200',
                ],
                [
                    'label' => 'LGA Result Summary',
                    'route' => 'lga-result-summary',
                    'active' => 'border-violet-500 bg-violet-500 text-white shadow-violet-200',
                    'idle' => 'border-violet-200 bg-violet-100 text-violet-900 hover:bg-violet-200',
                ],
                [
                    'label' => 'Add New Polling Unit Result',
                    'route' => 'create-polling-unit-result',
                    'active' => 'border-rose-500 bg-rose-500 text-white shadow-rose-200',
                    'idle' => 'border-rose-200 bg-rose-100 text-rose-900 hover:bg-rose-200',
                ],
            ];

        
        @endphp

        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute left-[-2rem] top-24 h-28 w-28 rotate-6 rounded-[2rem] bg-red-200/90 animate__animated animate__fadeInLeft"></div>
            <div class="absolute left-20 top-44 h-16 w-16 rounded-[1.4rem] bg-orange-200/90 animate__animated animate__fadeInDown animate__fast"></div>
            <div class="absolute right-16 top-24 h-20 w-20 rotate-12 rounded-[1.5rem] bg-amber-200/90 animate__animated animate__fadeInRight"></div>
            <div class="absolute right-32 top-52 h-14 w-14 rounded-full bg-yellow-200/90 animate__animated animate__zoomIn animate__fast"></div>
            <div class="absolute left-[12%] bottom-24 h-20 w-20 rounded-[1.6rem] bg-teal-200/90 animate__animated animate__fadeInUp"></div>
            <div class="absolute left-[22%] bottom-10 h-12 w-12 rotate-12 rounded-[1rem] bg-cyan-200/90 animate__animated animate__fadeInUp animate__fast"></div>
            <div class="absolute right-[18%] bottom-20 h-24 w-24 -rotate-6 rounded-[2rem] bg-violet-200/90 animate__animated animate__fadeInUp"></div>
            <div class="absolute right-[8%] bottom-12 h-16 w-16 rounded-full bg-rose-200/90 animate__animated animate__zoomIn animate__fast"></div>
            <div class="absolute inset-x-0 top-0 h-2 bg-slate-950"></div>
        </div>

        <div class="relative min-h-screen">
            <header class="sticky top-0 z-30 border-b-2 border-slate-300/80 bg-[var(--surface-paper)]/95 shadow-sm shadow-slate-300/60 backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="grid shrink-0 grid-cols-2 gap-1 pt-1">
                                <span class="h-4 w-4 rounded-md bg-red-400"></span>
                                <span class="h-4 w-4 rounded-md bg-amber-400"></span>
                                <span class="h-4 w-4 rounded-md bg-emerald-400"></span>
                                <span class="h-4 w-4 rounded-md bg-violet-400"></span>
                            </div>

                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="pattern-badge border-slate-900 bg-slate-900 text-white animate__animated animate__fadeInDown animate__fast">Live dashboard</span>
                                    <span class="pattern-badge border-slate-300 bg-[var(--tone-mist)] text-slate-800 animate__animated animate__fadeInDown animate__fast">Alpine + animate.css</span>
                                </div>

                                <div>
                                    <a href="{{ route('polling-unit-results') }}" class="text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">
                                        {{ config('app.name') }}
                                    </a>
                                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700 sm:text-base">
                                        A vivid internal dashboard for the Delta State legacy election dataset, rebuilt with color blocks, motion, and real-time interface polish.
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 text-sm">
                                    <span class="pattern-badge border-emerald-300 bg-emerald-100 text-emerald-900">State scope: Delta</span>
                                    <span class="pattern-badge border-orange-300 bg-orange-100 text-orange-900">Legacy MySQL schema</span>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="navOpen = !navOpen"
                            class="inline-flex items-center rounded-full border-2 border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:border-slate-900 hover:bg-slate-900 hover:text-white lg:hidden"
                            :aria-expanded="navOpen"
                        >
                            Menu
                        </button>
                    </div>


                    <nav class="mt-5 hidden flex-wrap items-center gap-3 lg:flex">
                        @foreach ($navigation as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="inline-flex items-center rounded-full border-2 px-5 py-3 text-sm font-semibold shadow-sm transition {{ request()->routeIs($item['route']) ? $item['active'] : $item['idle'] }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <nav x-cloak x-show="navOpen" x-transition.opacity.scale.origin.top class="mt-5 grid gap-2 lg:hidden">
                        @foreach ($navigation as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                @click="closeNav()"
                                class="inline-flex items-center rounded-[1.25rem] border-2 px-4 py-3 text-sm font-semibold shadow-sm transition {{ request()->routeIs($item['route']) ? $item['active'] : $item['idle'] }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
                <div class="animate__animated animate__fadeInUp" style="animation-fill-mode: both;">
                    {{ $slot }}
                </div>
            </main>

            <footer class="border-t-2 border-slate-300 bg-[var(--surface-paper)]/90">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="h-3.5 w-3.5 rounded-full bg-red-400"></span>
                        <span class="h-3.5 w-3.5 rounded-full bg-amber-400"></span>
                        <span class="h-3.5 w-3.5 rounded-full bg-lime-400"></span>
                        <span class="h-3.5 w-3.5 rounded-full bg-cyan-400"></span>
                        <span class="h-3.5 w-3.5 rounded-full bg-violet-400"></span>
                        <span class="h-3.5 w-3.5 rounded-full bg-rose-400"></span>
                        <p>Built with Laravel, Livewire, Alpine, animate.css, Tailwind CSS, and MySQL Query Builder.</p>
                    </div>
                    <p>State scope: Delta (`state_id = 25`). No gradient coloring used.</p>
                </div>
            </footer>
        </div>

        @livewireScripts
    </body>
</html>
