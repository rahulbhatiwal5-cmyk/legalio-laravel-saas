<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HowItWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Así funciona',
            ],
            [
                'key' => 'background_image',
                'value' => null,
            ],
            [
                'key' => 'banner_title',
                'value' => 'Así funciona'
            ],
            [
                'key' => 'banner_description',
                'value' => 'Descubre una forma sencilla de generar documentos y contratos en Documentos-Legales.mx. Nuestra plataforma te ofrece una amplia selección de documentos legales para cubrir tus necesidades. ¡Comienza a crear tus contratos personalizados hoy mismo!'
            ],
            [
                'key' => 'banner_image',
                'value' => null,
            ],
            [
                'key' => 'main_heading',
                'value' => 'Crea Documentos en Pocos Pasos',
            ],
            [
                'key' => 'short_description',
                'value' => 'Genera y descarga tus documentos legales en formatos PDF y DOCX (Word) al instante, de manera fácil y rápida.',
            ],
            [
                'key' => 'second_banner_img',
                'value' => null,
            ],
            [
                'key' => 'second_banner_heading',
                'value' => 'Genera tus documentos legales de forma rápida y sencilla',
            ],
            [
                'key' => 'second_banner_sub_heading',
                'value' => 'Nuestro sistema te guía paso a paso, genera tus documentos totalmente personalizados y te permite descargarlos en los formatos PDF y DOCX (Word) de forma inmediata.',
            ],
            [
                'key' => 'button_label',
                'value' => 'Comienza ahora',
            ],
            [
                'key' => 'button_link',
                'value' => 'https://sagmetic.site/2023/laravel/contracts/public/',
            ],
            [

                'key' => 'meta_title',
                'value' => 'How It Work',
                'type' => null,
                'status' => 1,
            ],
            [
                'key' => 'meta_description',
                'value' => null,
                'type' => null,
                'status' => 1,
            ]
        ];

        foreach ($data as $item) {
            $exists = DB::table('how_it_works')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('how_it_works')->insert($item);
            }
        }
    }
}
