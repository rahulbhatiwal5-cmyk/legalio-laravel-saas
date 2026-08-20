<?php

namespace App\Services;

use ConvertApi\ConvertApi;
use ConvertApi\FileUpload;
use Exception;
use Illuminate\Support\Facades\Storage;
// use PDF;

use App\Models\ContractContent;
use App\Models\Order;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\File;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class DocxToPagesService
{
    // public function __construct()
    // {
    //     ConvertApi::setApiCredentials(env('CONVERTAPI_SECRET')); // Set your API secret
    // }

    public function __construct()
{
    $apiSecret = config('services.convertapi.secret');
 
    Log::info('Loaded ConvertAPI Secret', ['secret' => $apiSecret]);

    //   dd(config('services.convertapi.secret'));
 
    ConvertApi::setApiCredentials($apiSecret); // Set your API secret
}


    public function generateDOCX($orderId): ?string
    {
        $order = Order::find($orderId);

        if (!$order) {
            return null;
        }

        $userId = $order->user_id;
        $documentId = $order->document_id;

        $contractContent = ContractContent::where([
            ['user_id', $userId],
            ['document_id', $documentId]
        ])->first();

        if (!$contractContent) {
            return null;
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText(strip_tags($contractContent->html));

        // Save file to public storage path
        $directory = public_path('storage/generated-docx');

        // Ensure directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = "invoice_{$documentId}.docx";
        $filePath = $directory . '/' . $fileName;

        // Create the file
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filePath);

        // Set permissions
        chmod($filePath, 0777);

        // Validate the file exists and is not empty
        if (!file_exists($filePath)) {
            throw new \Exception("DOCX was not created at: $filePath");
        }

        if (filesize($filePath) === 0) {
            throw new \Exception("DOCX is empty at: $filePath");
        }

        Log::info("DOCX generated successfully", [
            'path' => $filePath,
            'size' => filesize($filePath),
        ]);

        // If you're calling an API that expects a public URL, return the public URL
        // return asset("storage/generated-docx/{$fileName}");


        return $filePath;
        // return asset($filePath);
    }

    public function generatePDF(string $view, array $data = [], string $filename = null, bool $download = true)
    {
        $pdf = Pdf::loadView($view, $data);
        
        $filename = $filename ?? 'document.pdf';

        return $download
            ? $pdf->download($filename)
            : $pdf->stream($filename);
    }



  /**
     * Convert DOCX to PAGES format using ConvertAPI
     *
     * @param string $filePath (
     * @return string (path to saved .pages file)
     * @throws Exception
     */
    // old code 
    // public function convert($filePath)
    // { 
        
    //     try {
         

    //         // $customFilePath="https://legalio.mx/storage/generated-docx/invoice_5.docx";
    //         $filename = basename($filePath); // invoice_5.docx
    //         $publicUrl = config('app.url') . "/storage/generated-docx/{$filename}";
    //         // dd($publicUrl) ;
          
           
    //         $result = ConvertApi::convert('pages', [
    //             'File' =>  $publicUrl
    //         ], 'docx');
    
    //         // dd($result);
    
    //         // Prepare converted directory
    //         $convertedDir = public_path('storage/converted');
    //         Log::debug("Ensuring converted directory exists.", ['convertedDir' => $convertedDir]);
    
    //         if (!file_exists($convertedDir)) {
    //             mkdir($convertedDir, 0777, true);
    //             Log::debug("Converted directory created.", ['convertedDir' => $convertedDir]);
    //         }
    
    //         chmod($convertedDir, 0777); // Ensure it's writable
    //         Log::debug("Permissions set for converted directory.");
    
    //         // Save converted files
    //         $convertedFiles = $result->saveFiles($convertedDir);
    //         Log::debug("Converted files saved.", ['convertedFiles' => $convertedFiles]);
    
    //         if (empty($convertedFiles)) {
    //             Log::error("No files were returned from the conversion.");
    //             throw new \Exception("Conversion did not return any files.");
    //         }
    
    //         // Rename the converted file
    //         $originalPath = $convertedFiles[0];
    //         $docId = basename($filePath, '.docx');
    //         $newName = "{$docId}.pages";
    //         $newPath = $convertedDir . '/' . $newName;
    
    //         Log::debug("Renaming file.", [
    //             'originalPath' => $originalPath,
    //             'newPath' => $newPath,
    //         ]);
    
    //         rename($originalPath, $newPath);
    //         Log::info("File converted and renamed successfully.", ['finalPath' => $newPath]);
    
    //         return $newPath;
    
    //     } catch (Exception $e) {
    //         Log::error("DOCX to PAGES conversion failed.", [
    //             'error' => $e->getMessage(),
    //             'filePath' => $filePath
    //         ]);
    //         throw new Exception("DOCX to PAGES conversion failed: " . $e->getMessage());
    //     }
    // }
    

    public function convert($filePath)
    { 
        try {
            // Step 1: Prepare the file for direct upload instead of using a Public URL
            $fileUpload = new \ConvertApi\FileUpload($filePath);
        
            // Step 2: Run conversion using the uploaded resource
            $result = \ConvertApi\ConvertApi::convert('pages', [
                'File' => $fileUpload
            ], 'docx');

            // Prepare converted directory
            $convertedDir = public_path('storage/converted');
            Log::debug("Ensuring converted directory exists.", ['convertedDir' => $convertedDir]);

            if (!file_exists($convertedDir)) {
                mkdir($convertedDir, 0777, true);
                Log::debug("Converted directory created.", ['convertedDir' => $convertedDir]);
            }

            chmod($convertedDir, 0777); // Ensure it's writable
            Log::debug("Permissions set for converted directory.");

            // Save converted files
            $convertedFiles = $result->saveFiles($convertedDir);
            Log::debug("Converted files saved.", ['convertedFiles' => $convertedFiles]);

            if (empty($convertedFiles)) {
                Log::error("No files were returned from the conversion.");
                throw new \Exception("Conversion did not return any files.");
            }

            // Rename the converted file
            $originalPath = $convertedFiles[0];
            $docId = basename($filePath, '.docx');
            $newName = "{$docId}.pages";
            $newPath = $convertedDir . '/' . $newName;

            Log::debug("Renaming file.", [
                'originalPath' => $originalPath,
                'newPath' => $newPath,
            ]);

            rename($originalPath, $newPath);
            Log::info("File converted and renamed successfully.", ['finalPath' => $newPath]);

            return $newPath;

        } catch (\Exception $e) { // Added backslash to Exception for global namespace
            Log::error("DOCX to PAGES conversion failed.", [
                'error' => $e->getMessage(),
                'filePath' => $filePath
            ]);
            throw new \Exception("DOCX to PAGES conversion failed: " . $e->getMessage());
        }
    }
   
    
    
    
}