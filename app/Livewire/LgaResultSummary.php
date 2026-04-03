<?php

namespace App\Livewire;

use App\Support\BincomElectionRepository;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class LgaResultSummary extends Component
{
    #[Url(as: 'lga')]
    public ?string $selectedLga = null;

    public bool $legacySchemaReady = true;

    public string $legacySchemaMessage = '';

    public function mount(): void
    {
        if (! $this->repository()->legacySchemaIsAvailable()) {
            $this->legacySchemaReady = false;
            $this->legacySchemaMessage = 'Import bincom_test.sql into MySQL and refresh the page to calculate LGA summaries from polling unit results.';

            return;
        }

        $this->synchronizeSelectedLga();
    }

    public function render(): View
    {
        $lgas = collect();
        $selectedLga = null;
        $aggregatedResults = collect();
        $comparisonRows = collect();
        $coverage = [
            'polling_units' => 0,
            'polling_units_with_results' => 0,
            'result_rows' => 0,
        ];

        if ($this->legacySchemaReady) {
            $lgas = $this->repository()->deltaLgas();

            if (filled($this->selectedLga)) {
                $selectedLga = $this->repository()->lgaDetails((int) $this->selectedLga);

                if ($selectedLga) {
                    $aggregatedResults = $this->repository()->lgaAggregatedResults((int) $this->selectedLga);
                    $comparisonRows = $this->buildComparisonRows(
                        $aggregatedResults,
                        $this->repository()->lgaOfficialResults((int) $this->selectedLga)
                    );
                    $coverage = [
                        'polling_units' => $this->repository()->lgaPollingUnitCount((int) $this->selectedLga),
                        'polling_units_with_results' => $this->repository()->lgaPollingUnitsWithResultsCount((int) $this->selectedLga),
                        'result_rows' => $this->repository()->lgaResultRowCount((int) $this->selectedLga),
                    ];
                }
            }
        }

        return view('livewire.lga-result-summary', [
            'lgas' => $lgas,
            'selectedLgaRecord' => $selectedLga,
            'aggregatedResults' => $aggregatedResults,
            'comparisonRows' => $comparisonRows,
            'coverage' => $coverage,
        ])->layout('layouts.app', [
            'title' => 'LGA Result Summary',
        ]);
    }

    private function buildComparisonRows(Collection $aggregatedResults, Collection $officialResults): Collection
    {
        $aggregatedByParty = $aggregatedResults->keyBy('party_abbreviation');
        $officialByParty = $officialResults->keyBy('party_abbreviation');

        return $aggregatedByParty->keys()
            ->merge($officialByParty->keys())
            ->unique()
            ->sort()
            ->values()
            ->map(function (string $party) use ($aggregatedByParty, $officialByParty): object {
                $computed = (int) data_get($aggregatedByParty->get($party), 'total_score', 0);
                $official = (int) data_get($officialByParty->get($party), 'official_total', 0);

                return (object) [
                    'party_abbreviation' => $party,
                    'computed_total' => $computed,
                    'official_total' => $official,
                    'difference' => $computed - $official,
                ];
            })
            ->sortByDesc('computed_total')
            ->values();
    }

    private function repository(): BincomElectionRepository
    {
        return app(BincomElectionRepository::class);
    }

    private function synchronizeSelectedLga(): void
    {
        $lgas = $this->repository()->deltaLgas();

        if ($lgas->isEmpty()) {
            $this->selectedLga = null;

            return;
        }

        if (
            blank($this->selectedLga)
            || ! $lgas->contains(fn ($lga): bool => (string) $lga->lga_id === (string) $this->selectedLga)
        ) {
            $this->selectedLga = (string) $lgas->first()->lga_id;
        }
    }
}
