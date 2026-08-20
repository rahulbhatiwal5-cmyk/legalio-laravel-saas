<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricesContent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Precios',
                'file_path' => null,
            ],
            [
                'key' => 'background_image',
                'value' => '172976988630.png',
                'file_path' => 'public/prices/172976988630.png',
            ],
            [
                'key' => 'banner_title',
                'value' => 'Precios',
                'file_path' => null,
            ],
            [
                'key' => 'banner_description',
                'value' => 'Explora la lista completa de documentos legales disponibles junto con sus precios',
                'file_path' => null,
            ],
            [
                'key' => 'banner_image',
                'value' => '172976990126.png',
                'file_path' => 'public/prices/172976990126.png',
            ],
            [
                'key' => 'document_heading',
                'value' => 'Documento',
                'file_path' => null,
            ],
            [
                'key' => 'description_heading',
                'value' => 'Descripción',
                'file_path' => null,
            ],
            [
                'key' => 'price_heading',
                'value' => 'Precio',
                'file_path' => null,
            ],
            [
                'key' => 'btn_text',
                'value' => 'Crear documento',
                'file_path' => null,
            ],
            [
                'key' => 'meta_title',
                'value' => 'Precios',
            ],
            [
                'key' => 'meta_description',
                'value' => null,
            ],
            [
                'key' => 'faq_heading',
                'value' => 'Frequently Asked Questions',
            ],
            [
                'key' => 'faq_description',
                'value' => 'Encuentra respuestas rápidas a las dudas más comunes sobre nuestros documentos y la plataforma.'
            ],
            [
                'key' => 'subscription_title',
                'value' => 'Mejor oferta',
            ],
            [
                'key' => 'subscription_heading',
                'value' => 'Suscripción',
            ],
            [
                'key' => 'recommended_text',
                'value' => 'RECOMENDADO',
            ],
            [
                'key' => 'subscription_description',
                'value' => 'Save with a Suscripción',
            ],
            [
                'key' => 'monthly_text',
                'value' => 'Mensual',
            ],
            [
                'key' => 'yearly_text',
                'value' => 'Anual Upfront',
            ],
            [
                'key' => 'ahorra_text',
                'value' => 'ahorra 19%',
            ],
            [
                'key' => 'subscription_note',
                'value' => 'Las descargas no utilizadas se acumulan',
            ],
            [
                'key' => 'per_month_text',
                'value' => 'al mes',
            ],
            [
                'key' => 'per_year_text',
                'value' => 'al año',
            ],
            [
                'key' => 'subscription_btn_text',
                'value' => 'Suscribirse ahora',
            ],
            [
                'key' => 'one_time_heading',
                'value' => 'Pago único',
            ],
            [
                'key' => 'one_time_description',
                'value' => 'No Suscripción',
            ],
            [
                'key' => 'one_time_price_note',
                'value' => 'Sin cobros recurrentes. Solo pagas por un documento.',
            ],
            [
                'key' => 'one_time_btn_text',
                'value' => 'Crear documento',
            ]
        ];

        foreach ($data as $item) {
            $exists = DB::table('prices_contents')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('prices_contents')->insert($item);
            }
        }
    }
}
