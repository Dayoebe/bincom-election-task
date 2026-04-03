<div class="space-y-8">
    <x-ui.section-heading
        title="Add New Polling Unit Result"
        description="Create a new polling unit record and save result rows for every party inside a single transaction, using the exact legacy schema from the dump."
    >
        <x-slot:actions>
            <span class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600">
                Transaction-safe insert
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
                @if ($createdPollingUnitUniqueId)
                    <span class="ml-1">
                        <a href="{{ route('polling-unit-results', ['polling_unit' => $createdPollingUnitUniqueId]) }}" class="font-semibold underline decoration-slate-400 underline-offset-4">
                            Review the new polling unit result
                        </a>
                    </span>
                @endif
            </x-ui.alert>
        @endif

        <x-ui.alert>
            Party options are derived from distinct `announced_pu_results.party_abbreviation` values so the form matches the live result data in the dump, including the legacy `LABO` abbreviation.
        </x-ui.alert>

        @error('save')
            <x-ui.alert type="error">
                {{ $message }}
            </x-ui.alert>
        @enderror

        <form wire:submit="save" class="space-y-6">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr),minmax(0,0.8fr)]">
                <div class="space-y-6">
                    <x-ui.card class="p-6">
                        <div class="space-y-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">Polling Unit Details</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Choose the LGA and ward first so the correct legacy `lga_id`, `ward_id`, and `uniquewardid` values can be stored.
                                </p>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">LGA</span>
                                    <select
                                        wire:model.live="lgaId"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                        <option value="">Select an LGA</option>
                                        @foreach ($lgas as $lga)
                                            <option value="{{ $lga->lga_id }}">{{ $lga->lga_name }} ({{ $lga->lga_id }})</option>
                                        @endforeach
                                    </select>
                                    @error('lgaId')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">Ward</span>
                                    <select
                                        wire:model.live="wardUniqueId"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                        @disabled(blank($lgaId))
                                    >
                                        <option value="">Select a ward</option>
                                        @foreach ($wards as $ward)
                                            <option value="{{ $ward->uniqueid }}">{{ $ward->ward_name }} (ward_id {{ $ward->ward_id }})</option>
                                        @endforeach
                                    </select>
                                    @error('wardUniqueId')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">Legacy polling_unit_id</span>
                                    <input
                                        type="number"
                                        min="0"
                                        wire:model.blur="pollingUnitId"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('pollingUnitId')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">Polling unit number</span>
                                    <input
                                        type="text"
                                        wire:model.blur="pollingUnitNumber"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('pollingUnitNumber')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2 sm:col-span-2">
                                    <span class="text-sm font-medium text-slate-700">Polling unit name</span>
                                    <input
                                        type="text"
                                        wire:model.blur="pollingUnitName"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('pollingUnitName')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2 sm:col-span-2">
                                    <span class="text-sm font-medium text-slate-700">Entered by user</span>
                                    <input
                                        type="text"
                                        wire:model.blur="enteredByUser"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('enteredByUser')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="p-6">
                        <div class="space-y-5">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">Optional Metadata</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    These values map directly to the nullable legacy polling unit columns.
                                </p>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <label class="block space-y-2 sm:col-span-2">
                                    <span class="text-sm font-medium text-slate-700">Description</span>
                                    <textarea
                                        rows="4"
                                        wire:model.blur="pollingUnitDescription"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    ></textarea>
                                    @error('pollingUnitDescription')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">Latitude</span>
                                    <input
                                        type="text"
                                        wire:model.blur="latitude"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('latitude')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">Longitude</span>
                                    <input
                                        type="text"
                                        wire:model.blur="longitude"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('longitude')
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>
                        </div>
                    </x-ui.card>
                </div>

                <x-ui.card class="p-6">
                    <div class="space-y-5">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">Party Scores</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Enter a score for every party derived from existing polling unit result rows.
                            </p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($parties as $party)
                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">{{ $party }}</span>
                                    <input
                                        type="number"
                                        min="0"
                                        wire:model.blur="partyScores.{{ $party }}"
                                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                    >
                                    @error('partyScores.'.$party)
                                        <span class="text-sm text-rose-600">{{ $message }}</span>
                                    @enderror
                                </label>
                            @endforeach
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/70 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm leading-6 text-slate-600">
                    Saving creates one row in <span class="font-medium text-slate-900">polling_unit</span> and one row per party in <span class="font-medium text-slate-900">announced_pu_results</span>.
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">Save Polling Unit Result</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>
    @endif
</div>
