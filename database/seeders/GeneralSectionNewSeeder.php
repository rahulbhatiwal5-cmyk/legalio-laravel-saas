<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSectionNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            [

                'key' => 'ultima_revision_text',
                'value' => 'Última revisión',
                'status' => 1,
            ],
            [

                'key' => 'formatos_disponibles_text',
                'value' => 'formatos disponibles',
                'status' => 1,
            ],
            [

                'key' => 'formatos_disponibles_data_text',
                'value' => 'PDF, DOCX, Pages',
                'status' => 1,
            ],
            [

                'key' => 'aplicable_en_text',
                'value' => 'Aplicable en',
                'status' => 1,
            ],
            [

                'key' => 'descargas_text',
                'value' => 'Descargas',
                'status' => 1,
            ],
            [

                'key' => 'open_review_modal_button_text',
                'value' => 'Escribir una opinión',
                'status' => 1,
            ],
            [

                'key' => 'review_modal_publicamente_text',
                'value' => 'Se mostrará públicamente',
                'status' => 1,
            ],
            [

                'key' => 'review_modal_nombre_publico_placeholder',
                'value' => 'Nombre público',
                'status' => 1,
            ],
            [

                'key' => 'review_modal_description_placeholder',
                'value' => 'Comparte tu opinión sobre este documento',
                'status' => 1,
            ],
            [
                'key' => 'review_modal_not_login_message_text',
                'value' => 'Por favor,ingresa,a tu cuenta para opinar sobre este documento.',
                'status' => 1,
            ]
            ,
            [
                'key' => 'review_modal_hace_text',
                'value' => 'Hace',
                'status' => 1,
            ]

        ];

        foreach ($entries as $entry) {
            $exists = DB::table('general_sections')->where('key', $entry['key'])->exists();

            if (!$exists) {
                DB::table('general_sections')->insert(array_merge($entry, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
