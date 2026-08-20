<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ArticleSection as ArticleModal;
use Illuminate\Support\Facades\DB;

class ArticleSection extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('article_sections')->truncate();
        $data = [
                [
                    'key' => 'article',
                    'heading' => '¿Qué es un/a',
                    'description' => 'Genera tu documento legal en pocos pasos. Obtén un archivo listo para usar sin complicaciones ni pérdida de tiempo.',
                ],
                [
                    'key' => 'article',
                    'heading' => '¿Para qué sirve este documento?',
                    'description' => 'Nuestra herramienta te guía paso a paso para crear un documento claro y perfectamente adaptado a tus necesidades.',
                ],
                [
                    'key' => 'article',
                    'heading' => '¿Qué incluye nuestro formato?',
                    'description' => 'Obtén un documento legalmente válido que cumple con la normativa vigente, brindándote respaldo en cualquier situación.',
                    
                ],
                [
                    'key' => 'example_section_heading',
                    'heading' => 'Ejemplo de',
                    'description' => null,
                ],
                [
                    'key' => 'example_section_description1',
                    'heading' => null,
                    'description' => 'El formato incluye todos los elementos necesarios para que tu carta tenga <strong>validez jurídica y profesionalismo</strong>. Asegúrate de revisar cada sección y completar la información requerida.',
                ],
                [
                    'key' => 'example_section_description2',
                    'heading' => null,
                    'description' => 'Para proteger el valor legal del documento, solo se presenta una muestra parcial con datos simulados y se omite el texto completo. El contenido final incluirá todos los elementos necesarios para que tu carta tenga <strong>validez jurídica y profesionalismo</strong>',
                ]
            ];
                  
        

        foreach($data as $item ){
            $db_record = new ArticleModal;
            $db_record->key=$item['key'] ?? null;
            $db_record->heading=$item['heading'] ?? null;
            $db_record->description=$item['description'] ?? null;
            $db_record->save();
        }
    }
}
