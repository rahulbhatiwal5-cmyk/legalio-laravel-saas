<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PrepareContract extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prepare_contracts')->truncate();
        DB::table('prepare_contracts')->insert([
            [
                'key' => 'title',
                'value' => 'Elabora tu Contrato con un Abogado Especializado',
            ],
            [
                'key' => 'short_description',
                'value' => null,
            ],
            [
                'key' => 'description',
                'value' => null,
            ],
            [
                'key' => 'page_sub_heading',
                'value' => 'Para Contratos Altamente Personalizados',
            ],
            [
                'key' => 'work_main_heading',
                'value' => 'Así funciona',
            ],
            [
                'key' => 'economical_main_heading',
                'value' => 'Alternativa para Contratos Estándar',
            ],
            [
                'key' => 'economical_description',
                'value' => null,
            ],
            [
                'key' => 'button_text',
                'value' => 'Comienza ahora',
            ],
            [
                'key' => 'button_link',
                'value' => null,
            ]
        ]);
    }
}
