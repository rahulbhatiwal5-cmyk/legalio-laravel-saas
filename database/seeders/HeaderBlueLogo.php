<?php

namespace Database\Seeders;

use App\Models\MetaData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeaderBlueLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MetaData::updateOrCreate(
            ['key' => 'header_blue_logo'],
            [
                'name' => 'Header Blue Logo',
                'value' => 'header_blue_logo.svg',
                'type' => 'header',
                'file_path' => '/logos/header_blue_logo.svg',
            ]
        );
    }
}
