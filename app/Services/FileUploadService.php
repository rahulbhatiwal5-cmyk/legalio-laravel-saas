<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Media;

class FileUploadService
{
     public function upload(UploadedFile $file, string $directory){
          try{
               $extension = $file->extension();
               $setting = web_setting('file_types');
               $value = json_decode($setting->value);

               if(in_array($extension,$value)){
                    $filename = generateFileName($file);
                    $path = $file->storeAs($directory, $filename);
                    $fullPath = storage_path("app/" . $path);

                    $media = new Media;
                    $media->file_name = $filename;
                    $media->file_path = $path;
                    $media->directory_name = $directory;
                    $media->file_format = $extension;
                    $media->save();

                    return response()->json([
                         'status' => '200',
                         'id' => $media->id,
                    ]);
               }else{
                    return response()->json([
                         'status' => '400',
                         'error' => 'This is not valid file format'
                    ]);
               }

          }catch(Exception $e){
               saveLog("Error:","FileUploadService", $e->getMessage());
          }
          
     }
}
