<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class BincomElectionRepository
{
    /**
     * The tables required for the interview task pages.
     *
     * @var array<int, string>
     */
    private array $requiredTables = [
        'lga',
        'ward',
        'polling_unit',
        'announced_pu_results',
    ];

    public function legacySchemaIsAvailable(): bool
    {
        try {
            foreach ($this->requiredTables as $table) {
                if (! Schema::hasTable($table)) {
                    return false;
                }
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function deltaLgas(): Collection
    {
        return DB::table('lga')
            ->where('state_id', 25)
            ->orderBy('lga_name')
            ->get([
                'uniqueid',
                'lga_id',
                'lga_name',
                'lga_description',
                'state_id',
            ]);
    }

    public function wardsForLga(int $lgaId): Collection
    {
        return DB::table('ward')
            ->where('lga_id', $lgaId)
            ->orderBy('ward_name')
            ->get([
                'uniqueid',
                'ward_id',
                'ward_name',
                'ward_description',
                'lga_id',
            ]);
    }

    public function wardDetails(int $uniqueWardId): ?object
    {
        return DB::table('ward')
            ->where('uniqueid', $uniqueWardId)
            ->first([
                'uniqueid',
                'ward_id',
                'ward_name',
                'ward_description',
                'lga_id',
            ]);
    }

    public function parties(): Collection
    {
        return DB::table('announced_pu_results')
            ->distinct()
            ->orderBy('party_abbreviation')
            ->pluck('party_abbreviation')
            ->values();
    }

    public function searchablePollingUnits(?string $search = null): Collection
    {
        $query = DB::table('polling_unit as pu')
            ->join('lga as l', 'l.lga_id', '=', 'pu.lga_id')
            ->leftJoin('ward as w', 'w.uniqueid', '=', 'pu.uniquewardid')
            ->leftJoin('announced_pu_results as apr', 'apr.polling_unit_uniqueid', '=', 'pu.uniqueid')
            ->where('l.state_id', 25)
            ->select([
                'pu.uniqueid',
                'pu.polling_unit_id',
                'pu.polling_unit_number',
                'pu.polling_unit_name',
                'l.lga_id',
                'l.lga_name',
                'w.ward_name',
                DB::raw('COUNT(apr.result_id) as result_row_count'),
            ])
            ->groupBy(
                'pu.uniqueid',
                'pu.polling_unit_id',
                'pu.polling_unit_number',
                'pu.polling_unit_name',
                'l.lga_id',
                'l.lga_name',
                'w.ward_name'
            )
            ->orderByDesc('result_row_count')
            ->orderBy('l.lga_name')
            ->orderBy('w.ward_name')
            ->orderBy('pu.polling_unit_name')
            ->orderBy('pu.uniqueid');

        if (filled($search)) {
            $term = trim($search);
            $likeTerm = '%'.$term.'%';

            $query->where(function ($builder) use ($term, $likeTerm): void {
                if (is_numeric($term)) {
                    $builder->where('pu.uniqueid', (int) $term);
                    $builder->orWhere('pu.polling_unit_number', 'like', $likeTerm);
                } else {
                    $builder->where('pu.polling_unit_number', 'like', $likeTerm);
                }

                $builder
                    ->orWhere('pu.polling_unit_name', 'like', $likeTerm)
                    ->orWhere('l.lga_name', 'like', $likeTerm)
                    ->orWhere('w.ward_name', 'like', $likeTerm);
            });
        }

        return $query->get();
    }

    public function pollingUnitDetails(int $uniqueId): ?object
    {
        return DB::table('polling_unit as pu')
            ->join('lga as l', 'l.lga_id', '=', 'pu.lga_id')
            ->leftJoin('ward as w', 'w.uniqueid', '=', 'pu.uniquewardid')
            ->where('pu.uniqueid', $uniqueId)
            ->where('l.state_id', 25)
            ->first([
                'pu.uniqueid',
                'pu.polling_unit_id',
                'pu.polling_unit_number',
                'pu.polling_unit_name',
                'pu.polling_unit_description',
                'pu.lat as latitude',
                'pu.long as longitude',
                'pu.entered_by_user',
                'pu.date_entered',
                'l.lga_id',
                'l.lga_name',
                'w.uniqueid as ward_uniqueid',
                'w.ward_id',
                'w.ward_name',
            ]);
    }

    public function pollingUnitPartyTotals(int $uniqueId): Collection
    {
        return DB::table('announced_pu_results')
            ->where('polling_unit_uniqueid', (string) $uniqueId)
            ->selectRaw('party_abbreviation, SUM(party_score) as total_score, COUNT(*) as result_rows, MAX(date_entered) as latest_entry_at')
            ->groupBy('party_abbreviation')
            ->orderByDesc('total_score')
            ->orderBy('party_abbreviation')
            ->get();
    }

    public function pollingUnitEntryRows(int $uniqueId): Collection
    {
        return DB::table('announced_pu_results')
            ->where('polling_unit_uniqueid', (string) $uniqueId)
            ->orderByDesc('date_entered')
            ->orderByDesc('result_id')
            ->get([
                'result_id',
                'party_abbreviation',
                'party_score',
                'entered_by_user',
                'date_entered',
                'user_ip_address',
            ]);
    }

    public function lgaDetails(int $lgaId): ?object
    {
        return DB::table('lga')
            ->where('lga_id', $lgaId)
            ->where('state_id', 25)
            ->first([
                'uniqueid',
                'lga_id',
                'lga_name',
                'lga_description',
                'state_id',
            ]);
    }

    public function lgaAggregatedResults(int $lgaId): Collection
    {
        return DB::table('announced_pu_results as apr')
            ->join('polling_unit as pu', 'pu.uniqueid', '=', 'apr.polling_unit_uniqueid')
            ->where('pu.lga_id', $lgaId)
            ->selectRaw('apr.party_abbreviation, SUM(apr.party_score) as total_score, COUNT(*) as result_rows, COUNT(DISTINCT apr.polling_unit_uniqueid) as contributing_polling_units')
            ->groupBy('apr.party_abbreviation')
            ->orderByDesc('total_score')
            ->orderBy('apr.party_abbreviation')
            ->get();
    }

    public function lgaOfficialResults(int $lgaId): Collection
    {
        return DB::table('announced_lga_results')
            ->where('lga_name', (string) $lgaId)
            ->selectRaw('party_abbreviation, SUM(party_score) as official_total')
            ->groupBy('party_abbreviation')
            ->orderByDesc('official_total')
            ->orderBy('party_abbreviation')
            ->get();
    }

    public function lgaPollingUnitCount(int $lgaId): int
    {
        return DB::table('polling_unit')
            ->where('lga_id', $lgaId)
            ->count();
    }

    public function lgaPollingUnitsWithResultsCount(int $lgaId): int
    {
        return (int) DB::table('announced_pu_results as apr')
            ->join('polling_unit as pu', 'pu.uniqueid', '=', 'apr.polling_unit_uniqueid')
            ->where('pu.lga_id', $lgaId)
            ->selectRaw('COUNT(DISTINCT apr.polling_unit_uniqueid) as polling_units_with_results')
            ->value('polling_units_with_results');
    }

    public function lgaResultRowCount(int $lgaId): int
    {
        return DB::table('announced_pu_results as apr')
            ->join('polling_unit as pu', 'pu.uniqueid', '=', 'apr.polling_unit_uniqueid')
            ->where('pu.lga_id', $lgaId)
            ->count();
    }
}
