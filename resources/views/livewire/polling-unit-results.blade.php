@php
    $totalVotes = $partyTotals->sum('total_score');
    $latestEntry = $entryRows->max('date_entered');
    $hasDuplicateRows = $entryRows->count() > $partyTotals->count();
@endphp

<div class="space-y-8">
    <x-ui.section-heading
        title="Polling Unit Result"
        description="Select any Delta State polling unit and review its profile, summed party totals, and underlying announced rows from the legacy dump."
    >
        <x-slot:actions>
            <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">
                Delta State source data
            </span>
        </x-slot:actions>
    </x-ui.section-heading>

    @if (! $legacySchemaReady)
        <x-ui.alert type="warning">
            {{ $legacySchemaMessage }}
        </x-ui.alert>
    @else
        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <div class="grid gap-6 xl:grid-cols-[22rem,minmax(0,1fr)]">
            <x-ui.card class="p-6">
                <div class="space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Find a Polling Unit</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">
                            Search by polling unit number, name, LGA, ward, or the legacy `uniqueid`. Units without result rows are still selectable.
                        </p>
                    </div>

                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-700">Search</span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search polling units"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                        >
                    </label>

                    <label class="block space-y-2">
                        <span class="text-sm font-medium text-slate-700">Polling unit</span>
                        <select
                            wire:model.live="selectedPollingUnit"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                        >
                            @forelse ($pollingUnits as $option)
                                @php
                                    $name = trim((string) $option->polling_unit_name) !== '' ? $option->polling_unit_name : 'Unnamed polling unit';
                                    $number = trim((string) $option->polling_unit_number) !== '' ? $option->polling_unit_number : 'No number';
                                    $resultLabel = (int) $option->result_row_count > 0
                                        ? number_format($option->result_row_count).' result row'.((int) $option->result_row_count === 1 ? '' : 's')
                                        : 'No results yet';
                                @endphp
                                <option value="{{ $option->uniqueid }}">
                                    {{ $number }} • {{ $name }} • {{ $option->lga_name }} • {{ $resultLabel }}
                                </option>
                            @empty
                                <option value="">No matching polling units</option>
                            @endforelse
                        </select>
                    </label>

                    <div wire:loading.delay wire:target="search,selectedPollingUnit" class="text-sm text-slate-500">
                        Loading polling unit data...
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-6 text-slate-600">
                        Result lookup uses the verified legacy relationship:
                        <span class="font-medium text-slate-900">polling_unit.uniqueid</span>
                        →
                        <span class="font-medium text-slate-900">announced_pu_results.polling_unit_uniqueid</span>.
                    </div>
                </div>
            </x-ui.card>

            <div class="space-y-6">
                @if (! $pollingUnit)
                    <x-ui.card class="flex min-h-64 items-center justify-center p-8 text-center">
                        <div class="max-w-md space-y-3">
                            <h2 class="text-xl font-semibold text-slate-950">No polling unit selected</h2>
                            <p class="text-sm leading-6 text-slate-600">
                                Choose a polling unit from the list to display its details and all announced party scores.
                            </p>
                        </div>
                    </x-ui.card>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <x-ui.stat label="Total Votes" :value="number_format($totalVotes)">
                            Summed from {{ number_format($entryRows->count()) }} announced row{{ $entryRows->count() === 1 ? '' : 's' }}.
                        </x-ui.stat>
                        <x-ui.stat label="Parties" :value="$partyTotals->count()">
                            Party groups found in the selected polling unit.
                        </x-ui.stat>
                        <x-ui.stat label="LGA" :value="$pollingUnit->lga_name">
                            Legacy LGA ID: {{ $pollingUnit->lga_id }}
                        </x-ui.stat>
                        <x-ui.stat label="Latest Entry" :value="$latestEntry ? \Illuminate\Support\Carbon::parse($latestEntry)->format('d M Y, H:i') : 'Not available'">
                            Polling unit uniqueid: {{ $pollingUnit->uniqueid }}
                        </x-ui.stat>
                    </div>

                    <x-ui.card class="p-6">
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Polling Unit</p>
                                    <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">
                                        {{ $pollingUnit->polling_unit_name ?: 'Unnamed polling unit' }}
                                    </h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        {{ $pollingUnit->polling_unit_description ?: 'No description recorded in the legacy dump.' }}
                                    </p>
                                </div>

                                <dl class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Polling Unit Number</dt>
                                        <dd class="mt-2 text-sm font-medium text-slate-900">{{ $pollingUnit->polling_unit_number ?: 'Not recorded' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Ward</dt>
                                        <dd class="mt-2 text-sm font-medium text-slate-900">{{ $pollingUnit->ward_name ?: 'Not recorded' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Latitude</dt>
                                        <dd class="mt-2 text-sm font-medium text-slate-900">{{ $pollingUnit->latitude ?: 'Not recorded' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Longitude</dt>
                                        <dd class="mt-2 text-sm font-medium text-slate-900">{{ $pollingUnit->longitude ?: 'Not recorded' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Record Snapshot</p>
                                <dl class="mt-4 space-y-4 text-sm text-slate-700">
                                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-3">
                                        <dt>Legacy `uniqueid`</dt>
                                        <dd class="font-medium text-slate-950">{{ $pollingUnit->uniqueid }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-3">
                                        <dt>Legacy `polling_unit_id`</dt>
                                        <dd class="font-medium text-slate-950">{{ $pollingUnit->polling_unit_id }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-3">
                                        <dt>Legacy ward `uniqueid`</dt>
                                        <dd class="font-medium text-slate-950">{{ $pollingUnit->ward_uniqueid ?: 'Not recorded' }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-4">
                                        <dt>Legacy reporter</dt>
                                        <dd class="font-medium text-slate-950">{{ $pollingUnit->entered_by_user ?: 'Not recorded' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </x-ui.card>

                    @if ($hasDuplicateRows)
                        <x-ui.alert type="info">
                            This polling unit has repeated party rows in `announced_pu_results`. The summary table below intentionally sums all matching rows per party from the dump.
                        </x-ui.alert>
                    @elseif ($entryRows->isEmpty())
                        <x-ui.alert type="warning">
                            This polling unit exists in the legacy polling unit table, but it does not have any announced party result rows yet.
                        </x-ui.alert>
                    @endif

                    <x-ui.table-shell>
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Party</th>
                                    <th class="px-6 py-4">Summed Score</th>
                                    <th class="px-6 py-4">Result Rows</th>
                                    <th class="px-6 py-4">Share of Total</th>
                                    <th class="px-6 py-4">Latest Entry</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($partyTotals as $row)
                                    <tr class="text-slate-700">
                                        <td class="px-6 py-4 font-semibold text-slate-950">{{ $row->party_abbreviation }}</td>
                                        <td class="px-6 py-4">{{ number_format($row->total_score) }}</td>
                                        <td class="px-6 py-4">{{ number_format($row->result_rows) }}</td>
                                        <td class="px-6 py-4">
                                            {{ $totalVotes > 0 ? number_format(($row->total_score / $totalVotes) * 100, 1) : '0.0' }}%
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $row->latest_entry_at ? \Illuminate\Support\Carbon::parse($row->latest_entry_at)->format('d M Y, H:i') : 'Not recorded' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                            No announced results were found for this polling unit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-ui.table-shell>

                    <x-ui.table-shell>
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Result ID</th>
                                    <th class="px-6 py-4">Party</th>
                                    <th class="px-6 py-4">Score</th>
                                    <th class="px-6 py-4">Entered By</th>
                                    <th class="px-6 py-4">Date Entered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($entryRows as $row)
                                    <tr class="text-slate-700">
                                        <td class="px-6 py-4 font-medium text-slate-950">{{ $row->result_id }}</td>
                                        <td class="px-6 py-4">{{ $row->party_abbreviation }}</td>
                                        <td class="px-6 py-4">{{ number_format($row->party_score) }}</td>
                                        <td class="px-6 py-4">{{ $row->entered_by_user ?: 'Not recorded' }}</td>
                                        <td class="px-6 py-4">
                                            {{ $row->date_entered ? \Illuminate\Support\Carbon::parse($row->date_entered)->format('d M Y, H:i') : 'Not recorded' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                            There are no raw announced rows to display.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </x-ui.table-shell>
                @endif
            </div>
        </div>
    @endif
</div>
