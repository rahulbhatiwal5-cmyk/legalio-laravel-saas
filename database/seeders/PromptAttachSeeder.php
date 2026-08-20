<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromptAttachSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prompt_attaches')->truncate();

        DB::table('prompt_attaches')->insert([
            [
               'resource_id'=>1000,
               'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_1.png',
               'backend_img_path'=>null,
               'page_type'=>'document_front_page',
            ],
            [
                'resource_id'=>1001,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_2.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],
            [
                'resource_id'=>1002,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_3.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],      
            [
                'resource_id'=>1003,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_4.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],       
            [
                'resource_id'=>1004,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_5.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],       
            [
                'resource_id'=>1005,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_6.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],  
            [
                'resource_id'=>1006,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_7.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],  
            [
                'resource_id'=>1007,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_8.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],  
            [
                'resource_id'=>1008,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_9.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],
            [
                'resource_id'=>1009,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_10.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ],
            [
                'resource_id'=>1010,
                'frontend_img_path'=>'/assets/img/promt-box-images/prompt_box_11.png',
                'backend_img_path'=>null,
                'page_type'=>'document_front_page',
            ]
        ]);
    }
}
