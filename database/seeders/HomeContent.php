<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomeContent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('home_contents')->truncate();
        DB::table('home_contents')->insert([
            [
                'key' => 'title',
                'value' => 'Home',
                'file_path' => null,
            ],
            [
                'key' => 'background_image',
                'value' => '173095946240.svg',
                'file_path' => 'public/home_images/173095946240.svg',
            ],
            [
                'key' => 'banner_title',
                'value' => 'Crea Contratos y documentos legales en minutos',
                'file_path' => null,
            ],
            [
                'key' => 'banner_description',
                'value' => null,
                'file_path' => null,
            ],
            [
                'key' => 'banner_image',
                'value' => '173227059447.svg',
                'file_path' => 'public/home_images/173227059447.svg',
            ],
            [
                'key' => 'banner_placeholder',
                'value' => '¿Qué documento necesitas?',
                'file_path' => null,
            ],
            [
                'key' => 'button_name',
                'value' => 'Empezar',
                'file_path' => null,
            ],
            [
                'key' => 'most_popular_title',
                'value' => 'Documentos más populares',
                'file_path' => null,
            ],
            [
                'key' => 'most_popular_btn_text',
                'value' => 'Crear ahora',
                'file_path' => null,
            ],
            [
                'key' => 'bottom_heading',
                'value' => 'Comienza a crear Documentos Legales Personalizados',
                'file_path' => null,
            ],
            [
                'key' => 'bottom_subheading',
                'value' => 'Genera y descarga tus documentos legales en formatos PDF y DOCX (Word) al instante, de manera fácil y rápida.',
                'file_path' => null,
            ],
            [
                'key' => 'bottom_button_label',
                'value' => 'Comienza ahora',
                'file_path' => null,
            ],
            [
                'key' => 'bottom_button_link',
                'value' => null,
                'file_path' => null,
            ],
            [
                'key' => 'bottom_banner_image',
                'value' => '173227061518.svg',
                'file_path' => 'public/home_images/173227061518.svg',
            ],
            [
                'key' => 'category_title',
                'value' => 'Categorías principales',
                'file_path' => null,
            ],
            [
                'key' => 'join_us_text',
                'value' => 'Únete y crea tus documentos en minutos',
                'file_path' => null,
            ],
            [
                'key' => 'reviews_heading',
                'value' => 'Lo que dicen nuestros clientes',
                'file_path' => null,
            ],
            [
                'key' => 'reviews_sub_heading',
                'value' => 'Valoramos tu opinión - Así nos califican nuestros clientes.',
                'file_path' => null,
            ],
            [
                'key' => 'review_left_arrow',
                'value' => '172863312539.png',
                'file_path' => 'public/home_images/20_1728633125/172863312539.png',
            ],
            [
                'key' => 'review_right_arrow',
                'value' => '17286332664.png',
                'file_path' => 'public/home_images/21_1728633266/17286332664.png',
            ],
            [
                'key' => 'meta_title',
                'value' => null,
                'file_path' => null,
            ],
            [
                'key' => 'meta_description',
                'value' => null,
                'file_path' => null,
            ],
            [
                'key' => 'most_popular_ryt_doc_text',
                'value' => 'Ver más',
                'file_path' => null,
            ]
        ]);

    }
}
