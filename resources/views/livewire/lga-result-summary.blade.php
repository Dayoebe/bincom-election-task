@php
    $computedTotalVotes = $aggregatedResults->sum('total_score');
    $leadingParty = $aggregatedResults->first();
@endphp

<div class="space-y-8">
    <x-ui.section-heading
        title="LGA Result Summary"
        description="Compute each LGA total directly from polling unit result rows in announced_pu_results. The summary below does not rely on announced_lga_results for its main totals."
    >
        <x-slot:actions>
            <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">
                Aggregated from polling units
            </span>
        </x-slot:actions>
    </x-ui.section-heading>

    @if (! $legacySchemaReady)
        <x-ui.alert type="warning">
            {{ $legacySchemaMessage }}
        </x-ui.alert>
    @else
        <x-ui.alert>
            Main totals on this page are calculated with
            <span class="font-medium">polling_unit.uniqueid = announced_pu_results.polling_unit_uniqueid</span>
            and grouped by party under the selected legacy LGA ID.
        </x-ui.alert>

        <x-ui.card class="p-6">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,20rem),1fr] lg:items-end">
                <label class="block space-y-2">
                    <span class="text-sm font-medium text-slate-700">Select LGA</span>
                    <select
                        wire:model.live="selectedLga"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                    >
                        @foreach ($lgas as $lga)
                            <option value="{{ $lga->lga_id }}">{{ $lga->lga_name }} ({{ $lga->lga_id }})</option>
                        @endforeach
                    </select>
                </label>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm leading-6 text-slate-600">
                    `announced_lga_results` is shown below only as an optional comparison snapshot. The primary summary is calculated from polling units for the selected LGA.
                </div>
            </div>
        </x-ui.card>

        @if (! $selectedLgaRecord)
            <x-ui.card class="flex min-h-56 items-center justify-center p-8 text-center">
                <div class="max-w-md space-y-3">
                    <h2 class="text-xl font-semibold text-slate-950">No LGA selected</h2>
                    <p class="text-sm leading-6 text-slate-600">
                        Choose an LGA to display the summed party totals calculated from its polling units.
                    </p>
                </div>
            </x-ui.card>
        @else
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <x-ui.stat label="Computed Total Votes" :value="number_format($computedTotalVotes)">
                    Summed from all matching `announced_pu_results` rows.
                </x-ui.stat>
                <x-ui.stat label="Leading Party" :value="$leadingParty?->party_abbreviation ?? 'N/A'">
                    {{ $leadingParty ? number_format($leadingParty->total_score) . ' votes' : 'No result rows found' }}
                </x-ui.stat>
                <x-ui.stat label="Polling Units With Results" :value="number_format($coverage['polling_units_with_results'])">
                    Out of {{ number_format($coverage['polling_units']) }} polling unit record{{ $coverage['polling_units'] === 1 ? '' : 's' }} in this LGA.
                </x-ui.stat>
                <x-ui.stat label="Result Rows" :value="number_format($coverage['result_rows'])">
                    Raw party rows contributing to the aggregation.
                </x-ui.stat>
            </div>

            <x-ui.card class="p-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">LGA Name</p>
                        <p class="mt-2 text-sm font-medium text-slate-950">{{ $selectedLgaRecord->lga_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Legacy LGA ID</p>
                        <p class="mt-2 text-sm font-medium text-slate-950">{{ $selectedLgaRecord->lga_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">State ID</p>
                        <p class="mt-2 text-sm font-medium text-slate-950">{{ $selectedLgaRecord->state_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Description</p>
                        <p class="mt-2 text-sm font-medium text-slate-950">{{ $selectedLgaRecord->lga_description ?: 'Not recorded' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.table-shell>
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Party</th>
                            <th class="px-6 py-4">Computed Total</th>
                            <th class="px-6 py-4">Result Rows</th>
                            <th class="px-6 py-4">Contributing Polling Units</th>
                            <th class="px-6 py-4">Share of Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($aggregatedResults as $row)
                            <tr class="text-slate-700">
                                <td class="px-6 py-4 font-semibold text-slate-950">{{ $row->party_abbreviation }}</td>
                                <td class="px-6 py-4">{{ number_format($row->total_score) }}</td>
                                <td class="px-6 py-4">{{ number_format($row->result_rows) }}</td>
                                <td class="px-6 py-4">{{ number_format($row->contributing_polling_units) }}</td>
                                <td class="px-6 py-4">
                                    {{ $computedTotalVotes > 0 ? number_format(($row->total_score / $computedTotalVotes) * 100, 1) : '0.0' }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    No polling unit results were found for this LGA.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-ui.table-shell>

            @if ($comparisonRows->isNotEmpty())
                <x-ui.card class="p-6">
                    <div class="space-y-2">
                        <h2 class="text-xl font-semibold text-slate-950">Optional Comparison Against announced_lga_results</h2>
                        <p class="text-sm leading-6 text-slate-600">
                            The legacy `announced_lga_results.lga_name` column stores numeric LGA IDs as strings. This table compares that snapshot against the polling-unit aggregation above.
                        </p>
                    </div>
                </x-ui.card>

                <x-ui.table-shell>
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Party</th>
                                <th class="px-6 py-4">Computed Total</th>
                                <th class="px-6 py-4">announced_lga_results</th>
                                <th class="px-6 py-4">Difference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($comparisonRows as $row)
                                <tr class="text-slate-700">
                                    <td class="px-6 py-4 font-semibold text-slate-950">{{ $row->party_abbreviation }}</td>
                                    <td class="px-6 py-4">{{ number_format($row->computed_total) }}</td>
                                    <td class="px-6 py-4">{{ number_format($row->official_total) }}</td>
                                    <td class="px-6 py-4 {{ $row->difference === 0 ? 'text-slate-700' : ($row->difference > 0 ? 'text-emerald-700' : 'text-rose-700') }}">
                                        {{ number_format($row->difference) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-ui.table-shell>
            @endif
        @endif
    @endif
</div>
