<?php

it('redirects the root path to the polling unit result page', function () {
    $this->get('/')
        ->assertRedirect(route('polling-unit-results'));
});

it('renders the polling unit result page', function () {
    $this->get(route('polling-unit-results'))
        ->assertOk()
        ->assertSee('Polling Unit Result');
});

it('renders the lga summary page', function () {
    $this->get(route('lga-result-summary'))
        ->assertOk()
        ->assertSee('LGA Result Summary');
});

it('renders the create polling unit result page', function () {
    $this->get(route('create-polling-unit-result'))
        ->assertOk()
        ->assertSee('Add New Polling Unit Result');
});
