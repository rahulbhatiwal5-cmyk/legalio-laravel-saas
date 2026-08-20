<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key' => 'title',
                'value' => 'Preguntas Frecuentes',
            ],
            [
                'key' => 'background_image',
                'value' => null,
            ],
            [
                'key' => 'banner_title',
                'value' => null,
            ],
            [
                'key' => 'banner_description',
                'value' => null,
            ],
            [
                'key' => 'banner_image',
                'value' => null
            ],
            [

                'key' => 'meta_title',
                'value' => 'FAQ',
            ],
            [
                'key' => 'meta_description',
                'value' => null
            ]
        ];

        foreach ($data as $item) {
            $exists = DB::table('question_answers')->where('key', $item['key'])->exists();

            if (!$exists) {
                DB::table('question_answers')->insert($item);
            }
        }
    }
}
