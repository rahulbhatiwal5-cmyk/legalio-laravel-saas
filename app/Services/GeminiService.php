<?php

namespace App\Services;

use Google\Auth\CredentialsLoader;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public $apiEndpoint;
    public $projectId;
    public $locationId;
    public $model;
    public $apiKey;
    public $modelRef;
    public $settings;

    public function __construct($modelRef = 'Gemini 2.5 pro')
    {
        $this->setModelRef($modelRef);
        saveLog('AI Service initialized with model: ' . $modelRef);
    }

    public function setModelRef($modelRef)
    {
        $settings = web_setting(null, false, 'ai', $modelRef);
        $this->settings = $settings;
        $this->apiEndpoint = $settings['api_endpoint'] ?? null;
        $this->projectId = $settings['project_id'] ?? null;
        $this->locationId = $settings['location_id'] ?? null;
        $this->model = $settings['model_id'] ?? null;
        $this->apiKey = $settings['generate_content_api'] ?? null;
        $this->modelRef = $modelRef;
    }

    private function getAccessToken()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/gcloud/service_account/credentials.json'));

        $credentials = CredentialsLoader::makeCredentials(
            ['https://www.googleapis.com/auth/cloud-platform'],
            json_decode(file_get_contents(storage_path('app/gcloud/service_account/credentials.json')), true)
        );

        $token = $credentials->fetchAuthToken();
        return $token['access_token'] ?? null;
    }


    /**
     * Generate content using Gemini 2.5 Pro (NON-STREAMING)
     */
    public function generateWithGemini(string $prompt, array $options = []): string
    {
        // dd($prompt); 
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return 'Error: Unable to retrieve access token.';
        }

        $temperature = $options['temperature'] ?? 0.2;     
        $maxTokens   = $options['max_tokens'] ?? 12000;   
        $topP        = $options['top_p'] ?? 0.9;

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:generateContent";

        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => $temperature,
                'maxOutputTokens' => $maxTokens,
                'topP'            => $topP,
                'responseModalities' => ['TEXT'],
            ],
            'safetySettings' => []
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json',
        ])
            ->timeout(240)
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return 'Error: AI service request failed.';
        }

        $data = $response->json();

        $textParts = [];

        if (!isset($data['candidates'])) {
            return 'Error: Invalid AI response format.';
        }

        foreach ($data['candidates'] as $candidate) {
            if (!isset($candidate['content']['parts'])) {
                continue;
            }

            foreach ($candidate['content']['parts'] as $part) {
                if (isset($part['text'])) {
                    $textParts[] = $part['text'];
                }
            }
        }

        $rawText = trim(implode('', $textParts));

        $cleanedText = $this->cleanText($rawText);

        return $cleanedText;
    }

    /**
     * Clean markdown & formatting safely
     */
    protected function cleanText(string $text): string
    {
        $text = preg_replace([
            '/```json/i',
            '/```/',
            '/\*\*(.*?)\*\*/s',
            '/\*(.*?)\*/s'
        ], [
            '',
            '',
            '$1',
            '$1'
        ], $text);

        // Remove trailing commas before closing braces
        $text = preg_replace('/,(\s*[}\]])/', '$1', $text);

        return trim($text);
    }
}
