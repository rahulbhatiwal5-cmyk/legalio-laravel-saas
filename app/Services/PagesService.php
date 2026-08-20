<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PagesService
{
     protected $apiKey;
     protected $baseUrl = "https://api.cloudconvert.com/v2";

     public function __construct()
     {
          $this->apiKey = env('CLOUDCONVERT_API_KEY');
     }

    /**
     * Create a CloudConvert Job for DOCX to PAGES conversion
     */
     public function createJob()
     {
          $response = Http::withHeaders([
               'Authorization' => "Bearer {$this->apiKey}",
               'Content-Type' => 'application/json'
          ])->post("{$this->baseUrl}/jobs", [
               'tasks' => [
                   'import' => ['operation' => 'import/upload', 'name' => 'import'],
                   'convert' => [
                       'operation' => 'convert',
                       'input' => 'import',
                       'input_format' => 'docx',
                       'output_format' => 'pages',
                       'name' => 'convert'
                   ],
                   'export' => ['operation' => 'export/url', 'input' => 'convert', 'name' => 'export']
               ]
          ]);
       
          if (!$response->successful()) {
          // Debug: Log the error details
               \Log::error('CloudConvert job creation failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
               ]);
          
               throw new \Exception("CloudConvert job creation failed: " . $response->body());
          }
       
          return $response->json();
     }

     /**
     * Upload File to CloudConvert
     */

     public function uploadFile($url, $params, $filePath)
     {
          $multipart = [];

          foreach ($params as $name => $value) {
               $multipart[] = [
                    'name'     => $name,
                    'contents' => $value,
               ];
          }

          $multipart[] = [
               'name'     => 'file',
               'contents' => fopen($filePath, 'r'),
               'filename' => basename($filePath),
          ];

          $client = new \GuzzleHttp\Client();

          try {
               $response = $client->request('POST', $url, [
                    'multipart' => $multipart,
                    'headers' => [
                         'User-Agent' => 'Laravel CloudConvert Client',
                    ],
               ]);

               // Return full response for debugging
               return [
                    'status' => $response->getStatusCode(),
                    'body'   => (string) $response->getBody(),
               ];

          } catch (\Exception $e) {
               return [
                    'status' => 'error',
                    'message' => $e->getMessage(),
               ];
          }
     }

    /**
     * Check Job Status
     */
     public function checkJobStatus($jobId)
     {
          $response = Http::withHeaders([
               'Authorization' => "Bearer {$this->apiKey}"
          ])->get("{$this->baseUrl}/jobs/{$jobId}");

          if (!$response->successful()) {
               throw new \Exception("Failed to fetch job status");
          }

          return $response->json();
     }

     /**
     * Download Converted File
     */

     public function downloadFile($url, $path)
     {
          $fileContents = file_get_contents($url);

          if ($fileContents === false) {
               throw new \Exception("Failed to download file");
          }

          file_put_contents($path, $fileContents);
     }
}


?>