<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Quiénes Somos',
            ],
            [
                'key' => 'background_image',
                'value' => null,
            ],
            [
                'key' => 'banner_title',
                'value' => 'Quiénes Somos',
            ],
            [
                'key' => 'banner_description',
                'value' => null,
            ],
            [
                'key' => 'banner_image',
                'value' => null,
            ],
            [
                'key' => 'image',
                'value' => null,
            ],
            [
                'key' => 'heading',
                'value' => 'Acerca de los Documentos Legales',
            ],
            [
                'key' => 'description',
                'value' => null,
            ],
            [
                'key' => 'offer_image',
                'value' => null,
            ],
            [
                'key' => 'offer_heading',
                'value' => 'Lo que ofrecemos',
            ],
            [
                'key' => 'offer_description',
                'value' => null,
            ],
            [
                'key' => 'meta_title',
                'value' => 'Sobre nosotros',
            ],
            [
                'key' => 'meta_description',
                'value' => null,
            ],
        ];

        foreach ($data as $item) {
            $exists = DB::table('who_we_ares')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('who_we_ares')->insert($item);
            }
        }
    }
}
