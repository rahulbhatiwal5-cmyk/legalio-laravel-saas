<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HelpCenter extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Centro de Ayuda',
            ],
            [
                'key' => 'background_image',
                'value' => null,
            ],
            [
                'key' => 'banner_title',
                'value' => null,
            ],
            [
                'key' => 'banner_placeholder',
                'value' => null,
            ],
            [
                'key' => 'banner_image',
                'value' => null,
            ],
            [
                'key' => 'main_title',
                'value' => null,
            ],
            [
                'key' => 'sub_title',
                'value' => null,
            ],
            [
                'key' => 'faq_heading',
                'value' => null,
            ],
            [
                'key' => 'faq_description',
                'value' => null,
            ],
            [
                'key' => 'bottom_banner_image',
                'value' => null,
            ],
            [
                'key' => 'banner_heading',
                'value' => null,
            ],
            [
                'key' => 'banner_description',
                'value' => null,
            ],
            [
                'key' => 'button_text',
                'value' => null,
            ],
            [
                'key' => 'meta_title',
                'value' => 'Home',
            ],
            [
                'key' => 'meta_description',
                'value' => null,
            ]
        ];

        foreach ($data as $item) {
            $exists = DB::table('help_centers')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('help_centers')->insert($item);
            }
        }
    }
}
