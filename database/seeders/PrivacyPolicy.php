<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivacyPolicy extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Aviso de Privacidad',
                'status' => 1
            ],
            [
                'key' => 'banner_title',
                'value' => 'Aviso de Privacidad',
                'status' => 1
            ],
            [
                'key' => 'main_heading',
                'value' => 'Aviso de Privacidad',
                'status' => 1
            ],
            [
                'key' => 'meta_title',
                'value' => 'Aviso de Privacidad',
                'status' => 1
            ],
            [
                'key' => 'meta_description',
                'value' => null,
                'status' => 1
            ],
            [
                'key' => 'background_image',
                'value' => '175189058524.png',
                'file_path' => 'public/legal_notices/175189058524.png',
                'status' => 1
            ],
            [
                'key' => 'banner_image',
                'value' => '173096279635.svg',
                'file_path' => 'public/terms_and_conditions/173096279635.svg',
                'status' => 1
            ],
            [
                'key' => 'banner_description',
                'value' => '',
                'status' => 1

            ],
        ];

        foreach ($data as $item) {
            $exists = DB::table('privacy_policies')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('privacy_policies')->insert($item);
            }
        }
    }
}
