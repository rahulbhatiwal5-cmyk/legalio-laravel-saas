<?php

namespace Database\Seeders;

use App\Models\MetaData;
use Illuminate\Database\Seeder;

class MobileLogoSeeder extends Seeder
{
    public function run(): void
    {
        MetaData::updateOrCreate(
            ['key' => 'mobile_header_logo'],
            [
                'name' => 'Mobile Header Logo',
                'value' => 'mobile-logo.svg',
                'type' => 'header',
                'file_path' => '/logos/mobile_header_logo.svg',
            ]
        );
    }
}