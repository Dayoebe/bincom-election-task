<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('polling-unit-results')
        ->assertStatus(200);
});
