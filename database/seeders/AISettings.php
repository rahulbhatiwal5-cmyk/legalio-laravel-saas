<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class AISettings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // DB::table('settings')->truncate();
        DB::table('settings')->insert([
            [
                'name' => 'Service Account File Path ',
                'key'=>'server_account_file_path',
                'value' => '/goolge/credentials.json',
                'type' => 'ai',
            ],[
                'name' => 'Project ID ',
                'key'=>'project_id',
                'value' => 'legalio-435913',
                'type' => 'ai',
            ],[
                'name' => 'API Endpoint ',
                'key'=>'api_endpoint',
                'value' => 'us-central1-aiplatform.googleapis.com',
                'type' => 'ai',
            ],[
                'name' => 'Model ID',
                'key'=>'model_id',
                'value' => 'gemini-2.0-flash-001',
                'type' => 'ai',
            ],[
                'name' => 'Generate Content API ',
                'key'=>'generate_content_api',
                'value' => 'GENERATE_CONTENT_API',
                'type' => 'ai',
            ],[
                'name' => 'Location ID ',
                'key'=>'location_id',
                'value' => 'us-central1',
                'type' => 'ai',
            ],
        ]);

    }
}


// "https://${API_ENDPOINT}/v1/projects/${PROJECT_ID}/locations/${LOCATION_ID}/publishers/google/models/${MODEL_ID}:${GENERATE_CONTENT_API}" -d '@request.json'
