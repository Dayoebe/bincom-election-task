<div class="space-y-8" x-data="pollingUnitComposer()" x-init="$nextTick(() => recalc())" @input.debounce.60ms="recalc">
    <x-ui.section-heading
        title="Add New Polling Unit Result"
        description="Create a new polling unit record and save result rows for every party inside a single transaction, using the exact legacy schema from the dump."
        class="animate__animated animate__fadeInDown"
    >
        <x-slot:actions>
            <span class="pattern-badge border-rose-300 bg-rose-100 text-rose-900">
                Transaction-safe insert
            </span>
            <span class="pattern-badge border-emerald-300 bg-emerald-100 text-emerald-900">
                Full-party entry mode
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

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.card tone="yellow" class="animate__animated animate__fadeInUp p-5" style="animation-fill-mode: both; animation-delay: 100ms;">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-yellow-700">Party fields</p>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-950" x-text="partyCount"></p>
                <p class="mt-3 text-sm leading-6 text-slate-700">Each party score is required before the form can be saved.</p>
            </x-ui.card>

            <x-ui.card tone="emerald" class="animate__animated animate__fadeInUp p-5" style="animation-fill-mode: both; animation-delay: 160ms;">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">Live score total</p>
                <p class="mt-4 text-3xl font-semibold tracking-tight text-slate-950" x-text="partyTotal.toLocaleString()"></p>
                <p class="mt-3 text-sm leading-6 text-slate-700">Alpine recalculates the combined party score as you type.</p>
            </x-ui.card>

            <x-ui.card tone="violet" class="animate__animated animate__fadeInUp p-5" style="animation-fill-mode: both; animation-delay: 220ms;">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-violet-700">Entry flow</p>
                <p class="mt-4 text-lg font-semibold tracking-tight text-slate-950">LGA → Ward → Polling Unit → Party Scores</p>
                <p class="mt-3 text-sm leading-6 text-slate-700">The save action writes both tables inside a single transaction.</p>
            </x-ui.card>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr),minmax(0,0.8fr)]">
                <div class="space-y-6">
                    <x-ui.card tone="rose" class="animate__animated animate__fadeInLeft p-6" style="animation-fill-mode: both; animation-delay: 100ms;">
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

                    <x-ui.card tone="teal" class="animate__animated animate__fadeInUp p-6" style="animation-fill-mode: both; animation-delay: 180ms;">
                        <div class="space-y-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-950">Optional Metadata</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">
                                        These values map directly to the nullable legacy polling unit columns.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    @click="showMetadata = !showMetadata"
                                    class="inline-flex items-center rounded-full border-2 border-teal-300 bg-white px-4 py-2 text-sm font-semibold text-teal-900 transition hover:bg-teal-100"
                                    x-text="showMetadata ? 'Hide metadata' : 'Show metadata'"
                                ></button>
                            </div>

                            <div x-cloak x-show="showMetadata" x-transition.opacity.scale.origin.top class="grid gap-5 sm:grid-cols-2">
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

                <x-ui.card tone="amber" class="animate__animated animate__fadeInRight p-6" style="animation-fill-mode: both; animation-delay: 140ms;">
                    <div class="space-y-5">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">Party Scores</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Enter a score for every party derived from existing polling unit result rows.
                                </p>
                            </div>

                            <button
                                type="button"
                                @click="showPartyGrid = !showPartyGrid"
                                class="inline-flex items-center rounded-full border-2 border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-900 transition hover:bg-amber-100"
                                x-text="showPartyGrid ? 'Hide party inputs' : 'Show party inputs'"
                            ></button>
                        </div>

                        <div x-cloak x-show="showPartyGrid" x-transition.opacity.scale.origin.top class="grid gap-4 sm:grid-cols-2">
                            @foreach ($parties as $party)
                                <label class="block space-y-2">
                                    <span class="text-sm font-medium text-slate-700">{{ $party }}</span>
                                    <input
                                        type="number"
                                        min="0"
                                        data-party-score
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

            <div class="animate__animated animate__fadeInUp flex flex-col gap-4 rounded-[2rem] border-2 border-slate-300 bg-white/90 p-5 shadow-[0_18px_45px_rgba(15,23,42,0.08)] sm:flex-row sm:items-center sm:justify-between" style="animation-fill-mode: both; animation-delay: 260ms;">
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
