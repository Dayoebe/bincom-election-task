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
    <body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
        @php
            $navigation = [
                ['label' => 'Polling Unit Result', 'route' => 'polling-unit-results'],
                ['label' => 'LGA Result Summary', 'route' => 'lga-result-summary'],
                ['label' => 'Add New Polling Unit Result', 'route' => 'create-polling-unit-result'],
            ];
        @endphp

        <div class="relative min-h-screen">
            <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-[radial-gradient(circle_at_top,_rgba(14,116,144,0.12),_transparent_58%)]"></div>

            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <a href="{{ route('polling-unit-results') }}" class="text-lg font-semibold tracking-tight text-slate-950">
                            {{ config('app.name') }}
                        </a>
                        <p class="mt-1 text-sm text-slate-600">
                            Legacy Delta State election dashboard driven by `bincom_test.sql`.
                        </p>
                    </div>

                    <nav class="flex flex-wrap items-center gap-2">
                        @foreach ($navigation as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium transition {{ request()->routeIs($item['route']) ? 'bg-slate-950 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900' }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200 bg-white/80">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <p>Built with Laravel, Livewire, Tailwind CSS, and MySQL Query Builder for a legacy schema.</p>
                    <p>State scope: Delta (`state_id = 25`).</p>
                </div>
            </footer>
        </div>

        @livewireScripts
    </body>
</html>
