<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('lga-result-summary')
        ->assertStatus(200);
});
