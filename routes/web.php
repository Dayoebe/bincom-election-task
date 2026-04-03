<?php

use App\Livewire\CreatePollingUnitResult;
use App\Livewire\LgaResultSummary;
use App\Livewire\PollingUnitResults;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/polling-unit-results');

Route::get('/polling-unit-results', PollingUnitResults::class)
    ->name('polling-unit-results');

Route::get('/lga-result-summary', LgaResultSummary::class)
    ->name('lga-result-summary');

Route::get('/polling-unit-results/create', CreatePollingUnitResult::class)
    ->name('create-polling-unit-result');
