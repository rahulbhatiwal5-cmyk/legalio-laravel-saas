<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentPriceSeeder extends Seeder
{
        public function run(): void
        {
            DB::table('settings')->updateOrInsert(
                ['key' => 'document_price'],
                [
                    'name' => 'Document Price',
                    'value' => '35',
                    'type' => 'config',
                    'ftype' => 'text',
                ]
            );
        }
    }
