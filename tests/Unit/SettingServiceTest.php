<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Services\Settings\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_get_and_default_settings(): void
    {
        Setting::create([
            'key' => 'site_name',
            'value' => 'Legalio',
            'type' => 'general',
        ]);

        $service = app(SettingService::class);

        $this->assertSame('Legalio', $service->get('site_name'));
        $this->assertSame('Default site name', $service->get('missing_key', 'Default site name'));
        $this->assertSame(['site_name' => 'Legalio'], $service->allByType('general'));
    }
}
