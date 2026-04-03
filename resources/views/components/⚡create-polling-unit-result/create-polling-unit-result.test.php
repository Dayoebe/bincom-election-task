<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('create-polling-unit-result')
        ->assertStatus(200);
});
