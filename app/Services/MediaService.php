<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function uploadMedia( UploadedFile $file , string $directory = "" , $public=false ){

        if( $public){
            $filePath = $file->move(public_path($directory),$file->getClientOriginalName());
            $fileName = $file->getClientOriginalName();
            $storedPath = $directory . '/' . $fileName;
            return Media::create([
                'directory_name' => $directory,
                'file_name' => $fileName,
                'file_format' => $file->getClientMimeType(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'status' => 1,
            ]);
        }else {
            $filePath = $file->store($directory,'public');
            $fileName = basename($filePath);
            return Media::create([
                'directory_name' => $directory,
                'file_name' => $fileName,
                'file_format' => $file->getClientMimeType(),
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'status' => 1,
            ]);
        }

    }

    public function getMediaById(int $id): ?Media
    {
        return Media::find($id);
    }

    public function deleteMedia(int $id): bool
    {
        $media = Media::find($id);
            if ($media) {
                // Delete file from storage
                Storage::disk('public')->delete($media->directory_name . '/' . $media->file_name);

                // Delete media record from database
                $media->delete();

                return true;
            }
         return false;
    }
    public function getMediaUrl(Media $media): string
    {
        // return Storage::disk('public')->url($media->directory_name . '/' . $media->file_name);
        return asset('storage/' . $media->directory_name . '/' . $media->file_name);
    }
}

?>