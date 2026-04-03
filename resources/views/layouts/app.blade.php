<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Installable Delta State election results dashboard built with Laravel, Livewire, Tailwind CSS, and MySQL.">
        <meta name="theme-color" content="#0f172a">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
        <meta name="application-name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $title ?? config('app.name') }}">
        <meta property="og:description" content="Delta State election results dashboard built on the verified legacy Bincom schema.">
        <meta property="og:image" content="{{ asset('icons/icon-512.png') }}">
        <meta name="twitter:card" content="summary">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
    </head>
    <body x-data="appShell()" class="min-h-screen overflow-x-hidden text-slate-900 antialiased">
        @php
            $navigation = [
                [
                    'label' => 'Polling Unit Result',
                    'short' => 'Polling Unit',
                    'route' => 'polling-unit-results',
                    'icon' => 'polling',
                ],
                [
                    'label' => 'LGA Result Summary',
                    'short' => 'LGA Summary',
                    'route' => 'lga-result-summary',
                    'icon' => 'summary',
                ],
                [
                    'label' => 'Add New Polling Unit Result',
                    'short' => 'New Result',
                    'route' => 'create-polling-unit-result',
                    'icon' => 'create',
                ],
            ];
        @endphp

        <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-24 bg-[var(--surface-paper)]"></div>
            <div class="absolute inset-x-0 top-0 h-1 bg-slate-950"></div>
            <div class="absolute right-[-3rem] top-16 hidden h-28 w-28 rounded-full bg-amber-200/70 blur-2xl sm:block"></div>
            <div class="absolute left-[-2rem] top-40 hidden h-32 w-32 rounded-full bg-cyan-200/60 blur-2xl md:block"></div>
            <div class="absolute bottom-24 right-[10%] hidden h-36 w-36 rounded-full bg-rose-200/55 blur-3xl lg:block"></div>
        </div>

        <div class="relative min-h-screen">
            <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/92 shadow-sm backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-3 py-3 sm:py-4">
                        <a href="{{ route('polling-unit-results') }}" class="flex min-w-0 items-center gap-3">
                            <img
                                src="{{ asset('icons/icon-192.png') }}"
                                alt="{{ config('app.name') }} icon"
                                class="h-11 w-11 shrink-0 rounded-2xl border border-slate-200 bg-white object-cover shadow-sm"
                            >

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold tracking-tight text-slate-950 sm:text-lg">
                                    {{ config('app.name') }}
                                </span>
                                <span class="block truncate text-xs text-slate-500 sm:text-sm">
                                    Delta State election dashboard
                                </span>
                            </span>
                        </a>

                        <div class="flex items-center gap-2 lg:gap-4">
                            <button
                                x-cloak
                                x-show="canInstall"
                                type="button"
                                @click="install()"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-900 bg-slate-900 px-3 py-2 text-xs font-medium text-white shadow-sm transition hover:bg-slate-800 sm:px-4 sm:text-sm"
                            >
                                <img
                                    src="{{ asset('icons/favicon-32x32.png') }}"
                                    alt=""
                                    class="h-4 w-4 rounded object-cover"
                                >
                                <span>Install</span>
                            </button>

                            <div class="hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 lg:inline-flex">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Delta State
                            </div>
                        </div>
                    </div>

                    <div class="hidden items-center justify-between gap-4 pb-4 lg:flex">
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

                        <p class="text-sm text-slate-500">Installable internal dashboard powered by Livewire and MySQL Query Builder.</p>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-6 pb-28 sm:px-6 sm:py-8 lg:px-8 lg:py-10 lg:pb-10">
                <div class="animate__animated animate__fadeInUp" style="animation-fill-mode: both;">
                    {{ $slot }}
                </div>
            </main>

            <footer class="hidden border-t border-slate-200 bg-[var(--surface-paper)]/90 md:block">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <img
                            src="{{ asset('icons/favicon-32x32.png') }}"
                            alt="{{ config('app.name') }} icon"
                            class="h-6 w-6 rounded-md border border-slate-200 bg-white object-cover"
                        >
                        <p>Built with Laravel, Livewire, Alpine, animate.css, Tailwind CSS, and MySQL Query Builder.</p>
                    </div>

                    <p>
                        <a href="https://dayoebe.github.io" target="_blank" rel="noopener noreferrer" class="text-slate-600 hover:text-slate-900">
                            Oyetoke Adedayo Ebenezer
                        </a>
                    </p>
                </div>
            </footer>
        </div>

        <nav class="mobile-tabbar lg:hidden">
            <div class="mx-auto grid max-w-md grid-cols-3 gap-2 px-4 py-3">
                @foreach ($navigation as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="mobile-tab-item {{ request()->routeIs($item['route']) ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500' }}"
                    >
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl {{ request()->routeIs($item['route']) ? 'bg-white/12 text-white' : 'bg-slate-100 text-slate-700' }}">
                            @switch($item['icon'])
                                @case('polling')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h11M8 12h11M8 18h11M4.5 6h.01M4.5 12h.01M4.5 18h.01" />
                                    </svg>
                                    @break
                                @case('summary')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19V10M12 19V5M19 19v-7" />
                                    </svg>
                                    @break
                                @case('create')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                    </svg>
                                    @break
                            @endswitch
                        </span>

                        <span class="mt-1 text-[11px] font-medium tracking-tight">{{ $item['short'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        @livewireScripts
    </body>
</html>
