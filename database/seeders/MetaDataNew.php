<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MetaDataNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            [
                'name' => 'Así funciona',
                'key' => 'asi_funciona_header',
                'value' => 'Así funciona',
                'type' => 'header',
                'status' => 1,
            ],
            [
                'name' => 'Ayuda',
                'key' => 'ayuda_header',
                'value' => 'Ayuda',
                'type' => 'header',
                'status' => 1,
            ],
            [
                'name' => '¿Qué documento estás buscando?',
                'key' => 'header_search_placeholder',
                'value' => '¿Qué documento estás buscando?',
                'type' => 'header',
                'status' => 1,
            ],
            [
                'name' => 'Nombre del documento',
                'key' => 'header_document_search_placeholder',
                'value' => 'Nombre del documento',
                'type' => 'header',
                'status' => 1,
            ],
            [
                'name' => 'No se encontraron documentos.',
                'key' => 'header_document_search_message',
                'value' => 'No se encontraron documentos.',
                'type' => 'header',
                'status' => 1,
            ],

        ];

        $footerItems = [
            'Documentos',
            'Negocios y Comercio',
            'Vida Personal',
            'Laboral y Cumplimiento',
            'Tecnología y Consumo',
            'Información',
            'Sobre nosotros',
            'Precios',
            'Contacto',
            'Facturación',
            'Ayuda',
            'Centro de Ayuda',
            'Así funciona',
            'Preguntas Frecuentes',
            'Legal',
            'Términos y Condiciones',
            'Aviso de Privacidad',
            'Aviso Legal',
        ];

        // Add footer items
        foreach ($footerItems as $name) {
            $entries[] = [
                'name' => $name,
                'key' => Str::slug($name, '_') . '_footer',
                'value' => $name,
                'type' => 'footer',
                'status' => 1,
            ];
        }

        // Insert only if key doesn't exist
        foreach ($entries as $entry) {
            $exists = DB::table('meta_data')->where('key', $entry['key'])->exists();

            if (!$exists) {
                DB::table('meta_data')->insert(array_merge($entry, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
