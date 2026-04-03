<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardRoutesTest extends TestCase
{
    public function test_root_redirects_to_the_polling_unit_result_page(): void
    {
        $this->get('/')
            ->assertRedirect(route('polling-unit-results'));
    }

    public function test_polling_unit_result_page_renders(): void
    {
        $this->get(route('polling-unit-results'))
            ->assertOk()
            ->assertSee('Polling Unit Result');
    }

    public function test_lga_summary_page_renders(): void
    {
        $this->get(route('lga-result-summary'))
            ->assertOk()
            ->assertSee('LGA Result Summary');
    }

    public function test_create_polling_unit_result_page_renders(): void
    {
        $this->get(route('create-polling-unit-result'))
            ->assertOk()
            ->assertSee('Add New Polling Unit Result');
    }
}
