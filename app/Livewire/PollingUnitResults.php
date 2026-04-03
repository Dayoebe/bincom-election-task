<?php

namespace App\Livewire;

use App\Support\BincomElectionRepository;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class PollingUnitResults extends Component
{
    #[Url(as: 'polling_unit')]
    public ?string $selectedPollingUnit = null;

    #[Url(as: 'q')]
    public string $search = '';

    public bool $legacySchemaReady = true;

    public string $legacySchemaMessage = '';

    public function mount(): void
    {
        if (! $this->repository()->legacySchemaIsAvailable()) {
            $this->legacySchemaReady = false;
            $this->legacySchemaMessage = 'Import bincom_test.sql into MySQL and refresh the page to load the polling unit records.';
        }
    }

    public function render(): View
    {
        $pollingUnits = collect();
        $pollingUnit = null;
        $partyTotals = collect();
        $entryRows = collect();

        if ($this->legacySchemaReady) {
            $pollingUnits = $this->repository()->searchablePollingUnits($this->search);

            if (blank($this->selectedPollingUnit) && $pollingUnits->isNotEmpty()) {
                $this->selectedPollingUnit = (string) $pollingUnits->first()->uniqueid;
            }

            if (filled($this->selectedPollingUnit)) {
                $pollingUnit = $this->repository()->pollingUnitDetails((int) $this->selectedPollingUnit);

                if ($pollingUnit) {
                    $partyTotals = $this->repository()->pollingUnitPartyTotals((int) $this->selectedPollingUnit);
                    $entryRows = $this->repository()->pollingUnitEntryRows((int) $this->selectedPollingUnit);
                }
            }
        }

        return view('livewire.polling-unit-results', [
            'pollingUnits' => $pollingUnits,
            'pollingUnit' => $pollingUnit,
            'partyTotals' => $partyTotals,
            'entryRows' => $entryRows,
        ])->layout('layouts.app', [
            'title' => 'Polling Unit Result',
        ]);
    }

    private function repository(): BincomElectionRepository
    {
        return app(BincomElectionRepository::class);
    }
}
