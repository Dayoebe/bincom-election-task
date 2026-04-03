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
                ],
                [
                    'label' => 'LGA Result Summary',
                    'route' => 'lga-result-summary',
                ],
                [
                    'label' => 'Add New Polling Unit Result',
                    'route' => 'create-polling-unit-result',
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
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4 py-4">
                        <a href="{{ route('polling-unit-results') }}" class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-sm font-semibold text-white shadow-sm">
                                BE
                            </span>

                            <span class="min-w-0">
                                <span class="block truncate text-base font-semibold tracking-tight text-slate-950 sm:text-lg">
                                    {{ config('app.name') }}
                                </span>
                                <span class="hidden text-sm text-slate-500 sm:block">
                                    Delta State election results dashboard
                                </span>
                            </span>
                        </a>

                        <div class="hidden items-center gap-4 lg:flex">
                            <nav class="flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1">
                                @foreach ($navigation as $item)
                                    <a
                                        href="{{ route($item['route']) }}"
                                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'border border-slate-200 bg-white text-slate-950 shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-950' }}"
                                    >
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </nav>

                            <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Delta State
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="navOpen = !navOpen"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 lg:hidden"
                            :aria-expanded="navOpen"
                            aria-label="Toggle navigation"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" d="M3 6h14M3 10h14M3 14h14" />
                            </svg>
                        </button>
                    </div>

                    <nav x-cloak x-show="navOpen" x-transition.opacity.origin.top class="border-t border-slate-200 pb-4 pt-4 lg:hidden">
                        <div class="grid gap-2">
                            @foreach ($navigation as $item)
                                <a
                                    href="{{ route($item['route']) }}"
                                    @click="closeNav()"
                                    class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}"
                                >
                                    <span>{{ $item['label'] }}</span>
                                    @if (request()->routeIs($item['route']))
                                        <span class="text-xs font-medium text-slate-300">Current</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Delta State dataset
                        </div>
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
