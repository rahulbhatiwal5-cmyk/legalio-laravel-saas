<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewHomeContent extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entries = [
            [

                'key' => 'home_text_google',
                'value' => 'Google',
                'type' => null,
                'status' => 1,
            ],
            [

                'key' => 'home_text_register',
                'value' => 'Regístrese con',
                'type' => null,
                'status' => 1,
            ],
            [

                'key' => 'home_text_facebook',
                'value' => 'Facebook',
                'type' => null,
                'status' => 1,
            ],
            [

                'key' => 'home_text_email',
                'value' => 'Email',
                'type' => null,
                'status' => 1,
            ],
            [

                'key' => 'meta_title',
                'value' => 'Home',
                'type' => null,
                'status' => 1,
            ],
        ];

        foreach ($entries as $entry) {
            $exists = DB::table('home_contents')->where('key', $entry['key'])->exists();

            if (!$exists) {
                DB::table('home_contents')->insert(array_merge($entry, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}
