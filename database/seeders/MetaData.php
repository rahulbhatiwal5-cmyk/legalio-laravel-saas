<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MetaData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('meta_data')->truncate();
        DB::table('meta_data')->insert([

            [
                'name' => 'Begin of Head',
                'key' => 'begin_of_head',
                'value' => null,
                'type'=>'header',
                'file_path'=>null,
            ],
            [
                'name' => 'End of head',
                'key' => 'end_of_head',
                'value' => null,
                'type'=>'header',
                'file_path'=>null,
            ],

            [
                'name' => 'Header Logo',
                'key' => 'header_logo',
                'value' => '173164998033.svg',
                'type'=>'header',
                'file_path'=>'public/logos/173164998033.svg'
            ],

            [
                'name' => 'User Dash Header Logo',
                'key' => 'user_dash_header_logo',
                'value' => '173164998033.svg',
                'type'=>'header',
                'file_path'=>'public/logos/173164998033.svg'
            ],

            [
                'name' => 'Footer Logo',
                'key' => 'footer_logo',
                'value' => '173209850132.svg',
                'type'=>'footer',

                'file_path'=>'public/logos/173209850132.svg'

            ],
            [
                'name' => 'Beginning of footer',
                'key' => 'beginning_of_footer',
                'value' => null,
                'type'=>'footer',
                'file_path'=>null,
            ],
            [
                'name' => 'End of Footer',
                'key' => 'end_of_footer',
                'value' => null,
                'type'=>'footer',
                'file_path'=>null,
            ],

            [
                'name' => 'Favicon',
                'key' => 'favicon',
                'value' => null,
                'type'=>'header',
                'file_path' => null
            ],
            [
                'name' => 'Header Button 1',
                'key' => 'header_btn_1',
                'value' => 'Crear documento',
                'type'=>'header',
                'file_path' => null
            ],
            [
                'name' => 'Header Button 2',
                'key' => 'header_btn_2',
                'value' => 'Iniciar sesión',
                'type'=>'header',
                'file_path' => null
            ],
            [
                'name' => 'Footer Copyright Text',
                'key' => 'footer_copyright',
                'value' => 'Copyright © 2020-{current_year} Legalio. Todos los derechos reservados.',
                'type'=>'footer',
                'file_path' => null

            ],
            [
                'name' => 'Footer text',
                'key' => 'footer_text',
                'value' => 'Líder en documentos legales por más de 5 años',
                'type'=>'footer',
                'file_path' => null

            ],
        ]);

    }
}
