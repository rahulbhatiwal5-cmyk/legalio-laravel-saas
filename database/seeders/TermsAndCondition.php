<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermsAndCondition extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Términos y condiciones',
            ],
            [
                'key' => 'background_image',
                'value' => null,
            ],
            [
                'key' => 'banner_title',
                'value' => 'Términos y condiciones',
            ],
            [
                'key' => 'banner_description',
                'value' => 'Lorem Ipsum es simplemente un texto de relleno de la industria de la impresión y la tipografía. Lorem Ipsum ha sido el texto de relleno estándar de la industria desde el siglo XVI.',
            ],
            [
                'key' => 'banner_image',
                'value' => null,
            ],
            [
                'key' => 'main_heading',
                'value' => 'Términos y condiciones',
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
            $exists = DB::table('terms_and_conditions')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('terms_and_conditions')->insert($item);
            }
        }
    }
}
