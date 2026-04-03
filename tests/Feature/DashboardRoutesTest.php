<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
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
            ->assertSee('Polling Unit Result')
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('icons/icon-192.png', false);
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

    public function test_pwa_manifest_contains_required_icons(): void
    {
        $manifestPath = public_path('manifest.webmanifest');

        $this->assertTrue(File::exists($manifestPath));

        $manifest = json_decode(File::get($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/polling-unit-results', $manifest['start_url']);
        $this->assertContains('/icons/icon-192.png', array_column($manifest['icons'], 'src'));
        $this->assertContains('/icons/icon-512.png', array_column($manifest['icons'], 'src'));
    }
}
