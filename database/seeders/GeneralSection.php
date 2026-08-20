<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralSection as GeneralModal;


class GeneralSection extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('general_sections')->truncate();
        $data = [
            [
                'key' => 'agreement_headline',
                'value' => 'Tu documento legal al instante',
                'description' => null,
                'heading' => null,
            ],
            [
                'key' => 'agreement_short_description',
                'heading' => null,
                'value' => 'Recibe tu Carta de Renuncia Voluntaria inmediatamente, redactada conforme a las leyes vigentes en México, asegurando claridad y profesionalismo en el proceso de renuncia. Nuestro servicio facilita el proceso de tus trámites laborales, ofreciéndote una solución personalizada que cumple con las normativas actuales y promueve un cierre laboral transparente y justo.',
                'description' => null,
            ],
            [
                'key' => 'agreement',
                'heading' => 'Rápido y fácil',
                'description' => 'Genera tu documento legal en pocos pasos. Obtén un archivo listo para usar sin complicaciones ni pérdida de tiempo.',
                'media_id' => 87,
            ],
            [
                'key' => 'agreement',
                'heading' => 'Personalización perfecta',
                'description' => 'Nuestra herramienta te guía paso a paso para crear un documento claro y perfectamente adaptado a tus necesidades.',
                'media_id' => 88,
            ],
            [
                'key' => 'agreement',
                'heading' => 'Seguridad jurídica',
                'description' => 'Obtén un documento legalmente válido que cumple con la normativa vigente, brindándote respaldo en cualquier situación.',
                'media_id' => 89,
            ],
            [
                'key' => 'agreement',
                'heading' => 'Descarga inmediata',
                'description' => 'Descarga tu documento al instante en PDF y DOCX. Listo para imprimir, editar o compartir fácilmente.',
                'media_id' => 90,
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'guide_heading',
                'value' => 'Obtén tu documento en solo 2 pasos',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'guide_button',
                'value' => 'Crear documento ahora',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'valid_in',
                'value' => 'Formato editable para México en Word, PDF y Pages',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'rating_text',
                'value' => 'Promedio Legalio',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'applicable_in',
                'value' => 'Todo México',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'related_heading',
                'value' => 'Documentos relacionados',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'related_description',
                'value' => 'Explora documentos similares, populares entre otros usuarios.',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'contract_heading',
                'value' => 'Introduce los datos aquí:',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'detail_page_letter_now_btn',
                'value' => 'Crea documento ahora',
            ],
            [
                'heading' => null,
                'description' => null,
                'key' => 'detail_page_job_recommend_btn',
                'value' => 'Crea documento ahora',
            ], 
            [
                'heading' => 'Crea tu documento',
                'description' => 'Utiliza nuestro generador paso a paso, responde preguntas simples y obtén un documento perfectamente adaptado a tus necesidades.',
                'key' => 'guide_section',
                'value' => null,
            ],
            [
                'heading' => 'Descarga tu Documento',
                'description' => 'Descarga el documento finalizado en el formato que prefieras. Disponible en PDF o DOCX, está listo para imprimir, editar o usar al instante.',
                'key' => 'guide_section',
                'value' => null,
            ],
            [
                'heading' => 'Formatos disponibles para descarga',
                'description' => '<p>PDF</p><p>Ideal para imprimir, firmar físicamente o compartir como documento cerrado. Es el formato más común para presentaciones oficiales y asegura que el contenido no pueda ser modificado fácilmente.</p><p>DOCX</p><p>Perfecto para quienes desean editar el documento antes de firmarlo. Puedes personalizar detalles, agregar cláusulas adicionales o realizar ajustes específicos directamente desde Word o Google Docs.</p><p>Pages</p><p>Compatible con dispositivos Apple, permite editar el documento en Mac, iPhone o iPad manteniendo el formato profesional y legal del archivo.</p>',
                'key' => 'legal_section_heading',
                'media_id' => 87,
                'value' => null,
            ],
            [
                'heading' => 'FAQ heading',
                'description' => null,
                'key' => 'document_faq_heading',
                'value' => 'Preguntas frecuentes sobre el/la',
            ]
        ];

        foreach( $data as $item ){
            $db_record = new GeneralModal;
            $db_record->heading=$item['heading'] ?? null;
            $db_record->description=$item['description'] ?? null;
            $db_record->key=$item['key'] ?? null;
            $db_record->value=$item['value'] ?? null;
            $db_record->media_id=$item['media_id'] ?? null;
            $db_record->save();
        }
    }
}
