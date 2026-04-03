<?php

namespace App\Livewire;

use App\Support\BincomElectionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

class CreatePollingUnitResult extends Component
{
    public ?string $lgaId = null;

    public ?string $wardUniqueId = null;

    public string $pollingUnitId = '';

    public string $pollingUnitNumber = '';

    public string $pollingUnitName = '';

    public string $pollingUnitDescription = '';

    public string $latitude = '';

    public string $longitude = '';

    public string $enteredByUser = '';

    /**
     * @var array<string, int|string>
     */
    public array $partyScores = [];

    public ?int $createdPollingUnitUniqueId = null;

    public bool $legacySchemaReady = true;

    public string $legacySchemaMessage = '';

    public function mount(): void
    {
        if (! $this->repository()->legacySchemaIsAvailable()) {
            $this->legacySchemaReady = false;
            $this->legacySchemaMessage = 'Import bincom_test.sql into MySQL and refresh the page before entering a new polling unit result.';

            return;
        }

        $this->resetPartyScores();
    }

    public function updatedLgaId(): void
    {
        $this->wardUniqueId = null;
        $this->resetValidation('wardUniqueId');
    }

    public function save(): void
    {
        if (! $this->legacySchemaReady) {
            return;
        }

        $validated = $this->validate();
        $ward = $this->repository()->wardDetails((int) $validated['wardUniqueId']);

        if (! $ward || (int) $ward->lga_id !== (int) $validated['lgaId']) {
            $this->addError('wardUniqueId', 'The selected ward does not belong to the chosen LGA.');

            return;
        }

        $timestamp = now();
        $userIpAddress = request()->ip() ?? '127.0.0.1';

        try {
            $createdUniqueId = DB::transaction(function () use ($validated, $ward, $timestamp, $userIpAddress): int {
                $pollingUnitNumber = trim($validated['pollingUnitNumber']);
                $pollingUnitName = trim($validated['pollingUnitName']);
                $enteredByUser = trim($validated['enteredByUser']);

                $pollingUnitUniqueId = (int) DB::table('polling_unit')->insertGetId([
                    'polling_unit_id' => (int) $validated['pollingUnitId'],
                    'ward_id' => (int) $ward->ward_id,
                    'lga_id' => (int) $ward->lga_id,
                    'uniquewardid' => (int) $ward->uniqueid,
                    'polling_unit_number' => $pollingUnitNumber,
                    'polling_unit_name' => $pollingUnitName,
                    'polling_unit_description' => $this->nullableValue($validated['pollingUnitDescription']),
                    'lat' => $this->nullableValue($validated['latitude']),
                    'long' => $this->nullableValue($validated['longitude']),
                    'entered_by_user' => $enteredByUser,
                    'date_entered' => $timestamp,
                    'user_ip_address' => $userIpAddress,
                ], 'uniqueid');

                $resultRows = collect($validated['partyScores'])
                    ->map(function (mixed $score, string $party) use ($pollingUnitUniqueId, $enteredByUser, $timestamp, $userIpAddress): array {
                        return [
                            'polling_unit_uniqueid' => (string) $pollingUnitUniqueId,
                            'party_abbreviation' => $party,
                            'party_score' => (int) $score,
                            'entered_by_user' => $enteredByUser,
                            'date_entered' => $timestamp,
                            'user_ip_address' => $userIpAddress,
                        ];
                    })
                    ->values()
                    ->all();

                DB::table('announced_pu_results')->insert($resultRows);

                return $pollingUnitUniqueId;
            });

            $this->createdPollingUnitUniqueId = $createdUniqueId;
            $this->resetForm();

            session()->flash(
                'success',
                'Polling unit '.$createdUniqueId.' was created and party results were saved successfully.'
            );
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('save', 'The polling unit result could not be saved. Please review the form and try again.');
        }
    }

    public function render(): View
    {
        $lgas = collect();
        $wards = collect();
        $parties = collect();

        if ($this->legacySchemaReady) {
            $lgas = $this->repository()->deltaLgas();
            $wards = filled($this->lgaId)
                ? $this->repository()->wardsForLga((int) $this->lgaId)
                : collect();
            $parties = $this->repository()->parties();
        }

        return view('livewire.create-polling-unit-result', [
            'lgas' => $lgas,
            'wards' => $wards,
            'parties' => $parties,
        ])->layout('layouts.app', [
            'title' => 'Add New Polling Unit Result',
        ]);
    }

    protected function rules(): array
    {
        $rules = [
            'lgaId' => [
                'required',
                'integer',
                Rule::exists('lga', 'lga_id')->where(fn ($query) => $query->where('state_id', 25)),
            ],
            'wardUniqueId' => [
                'required',
                'integer',
                Rule::exists('ward', 'uniqueid')->where(
                    fn ($query) => $query->where('lga_id', (int) $this->lgaId)
                ),
            ],
            'pollingUnitId' => ['required', 'integer', 'min:0'],
            'pollingUnitNumber' => ['required', 'string', 'max:50'],
            'pollingUnitName' => ['required', 'string', 'max:50'],
            'pollingUnitDescription' => ['nullable', 'string'],
            'latitude' => ['nullable', 'string', 'max:255'],
            'longitude' => ['nullable', 'string', 'max:255'],
            'enteredByUser' => ['required', 'string', 'max:50'],
        ];

        foreach (array_keys($this->partyScores) as $party) {
            $rules['partyScores.'.$party] = ['required', 'integer', 'min:0'];
        }

        return $rules;
    }

    private function resetPartyScores(): void
    {
        $this->partyScores = $this->repository()->parties()
            ->mapWithKeys(fn (string $party): array => [$party => 0])
            ->all();
    }

    private function resetForm(): void
    {
        $this->pollingUnitId = '';
        $this->pollingUnitNumber = '';
        $this->pollingUnitName = '';
        $this->pollingUnitDescription = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->resetPartyScores();
        $this->resetValidation();
    }

    private function nullableValue(?string $value): ?string
    {
        return filled($value) ? trim($value) : null;
    }

    private function repository(): BincomElectionRepository
    {
        return app(BincomElectionRepository::class);
    }
}
