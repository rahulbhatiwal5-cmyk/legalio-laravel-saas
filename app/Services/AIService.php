<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Auth\CredentialsLoader;
use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\FetchAuthTokenInterface;
use Illuminate\Support\Facades\Log;
use App\Models\AiFaq;
use App\Models\Question;
use App\Models\DocumentRightSection;
use App\Models\RightSectionCondition;
use App\Models\QuestionCondition;
use App\Models\SubCondition;
use App\Models\QuestionData;
use App\Models\MultipleChoiceQuestionOption;
use App\Models\Tag;
use App\Models\StandardDocument;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class AIService
{
    public $contactPageUrl = 'https://legalio.us/contact';
    protected $apiEndpoint;
    protected $projectId;
    protected $locationId;
    protected $model;
    protected $modelRef;
    protected $settings;
    protected $apiKey;
    // public function __construct($modelRef = 'Gemini 2.0')
    // {
    //     $settings = web_setting(null, false, 'ai', $modelRef);
    //     $this->apiEndpoint = $settings['api_endpoint'];
    //     $this->projectId = $settings['project_id'];
    //     $this->locationId = $settings['location_id'];
    //     $this->model = $settings['model_id'];
    //     $this->modelRef = $modelRef; 

    // }

    // public function __construct($modelRef = 'Gemini 2.0')
    // {
    //     $this->setModelRef($modelRef); 
    // }

    public function __construct($modelRef = 'chatgpt')
    {
        $this->setModelRef($modelRef); // use setter to dynamically assign
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

    public function generateText($prompt)
    {
        return match ($this->modelRef) {
            'chatgpt' => $this->generateWithOpenAI($prompt),
            'Gemini 2.0' => $this->generateWithGemini($prompt),
            'Gemini 2.5 pro' => $this->generateWithGemini($prompt),
            default => "Error: Unsupported model reference: {$this->modelRef}"
        };
    }

    protected function generateWithOpenAI($prompt)
    {
        $apiKey = $this->settings['generate_content_api'] ?? null;

        if (!$apiKey) {
            return "Error: OpenAI API key not configured.";
        }

        $model = $this->settings['model_id'] ?? 'gpt-3.5-turbo';

        $url = rtrim($this->settings['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if (!$response->successful()) {
            Log::error("OpenAI API error: " . $response->body());
            return 'Error: ' . $response->status() . ' - ' . $response->body();
        }

        // Get raw content
        $rawText = $response['choices'][0]['message']['content'] ?? 'No response received';

        // Clean markdown-like formatting
        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',     // bold **text**
            '/\*(.*?)\*/s',         // italic *text*
            '/^\s*[\*\-+]\s+/m',    // unordered list markers
            '/`{1,3}(.*?)`{1,3}/s'  // inline code `text` or ```text```
        ], [
            '$1',
            '$1',
            '',
            '$1'
        ], $rawText);

        // Normalize extra line breaks
        $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));

        return $cleanText;
    }

    protected function generateImageWithOpenAI(string $prompt, string $size = '1024x1024')
    {
        // Retrieve settings grouped by 'key' for model_ref = 'chatgpt' and type = 'api'
        $settings = web_setting(null, false, 'ai', 'chatgpt');
        $apiKey = $settings['generate_content_api'] ?? null;
        $endpoint = $settings['api_endpoint'] ?? null;
        // dd($apiKey,$endpoint);
        if (!$apiKey) {
            Log::error('OpenAI API key missing.');
            return "Error: OpenAI API key not configured.";
        }

        // Validate and fallback to default OpenAI endpoint
        $baseEndpoint = trim($endpoint ?? '', '/');
        if (empty($baseEndpoint) || !str_contains($baseEndpoint, 'openai.com')) {
            $baseEndpoint = 'https://api.openai.com/v1';
        }

        $url = $baseEndpoint . '/images/generations';

        Log::info("Calling OpenAI DALL·E with prompt: {$prompt}");
        Log::debug("Using OpenAI API endpoint: {$url}");

        $payload = [
            'prompt' => $prompt,
            'n' => 1,
            'size' => $size,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->post($url, $payload);

        if (!$response->successful()) {
            Log::error("OpenAI DALL·E API error [{$response->status()}]: " . $response->body());
            return 'Error: ' . $response->status() . ' - ' . $response->body();
        }

        Log::info("OpenAI DALL·E response: " . json_encode($response->json()));

        return $response['data'][0]['url'] ?? null;
    }


    public function generateAndStoreImageWithOpenAI(string $prompt, string $directory = 'document_images')
    {
        $imageUrl = $this->generateImageWithOpenAI($prompt);

        if (!$imageUrl || str_contains($imageUrl, 'Error')) {
            Log::warning("Image URL not generated for prompt: " . $prompt);
            return null;
        }

        Log::info("Downloading image from URL: " . $imageUrl);

        try {
            $tempImageContent = Http::get($imageUrl)->body();
            $tempPath = storage_path('app/temp_' . Str::random(10) . '.png');
            file_put_contents($tempPath, $tempImageContent);

            $uploadedFile = new UploadedFile(
                $tempPath,
                basename($tempPath),
                'image/png',
                null,
                true
            );

            $media = app(\App\Services\MediaService::class)->uploadMedia($uploadedFile, $directory, false);

            Log::info("Image uploaded to media ID: " . ($media->id ?? 'null'));

            unlink($tempPath);

            return $media;
        } catch (\Exception $e) {
            Log::error("Error in downloading/uploading OpenAI image: " . $e->getMessage());
            return null;
        }
    }

    protected function generateWithGemini($prompt)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return "Error: Unable to retrieve access token.";
        }

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseModalities" => ["TEXT"],
                "temperature" => 1,
                "maxOutputTokens" => 8192,
                "topP" => 0.95
            ],
            "safetySettings" => []
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json'
        ])
            ->timeout(120)
            ->post($url, $requestPayload);

        if ($response === false) {
            return "Error: Unable to connect to AI service.";
        }

        $responseDecoded = json_decode($response, true);
        $jsonParts = [];
        foreach ($responseDecoded as $candidateGroup) {
            foreach ($candidateGroup['candidates'] as $candidate) {
                foreach ($candidate['content']['parts'] as $part) {
                    $jsonParts[] = $part['text'];
                }
            }
        }

        $rawText = implode('', $jsonParts);

        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',
            '/\*(.*?)\*/s',
            '/^\s*[\*\-+]\s+/m',
            '/`{1,3}(.*?)`{1,3}/s'
        ], [
            '$1',
            '$1',
            '',
            '$1'
        ], $rawText);

        $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));

        return $cleanText;
    }

    // public function generateFromFAQContext($userQuestion , $tags)
    // {
    //     if (empty(trim($userQuestion))) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Please ask a question so I can help you.",
    //             'link_status' => true,
    //             // 'link' => "https://legalio.mx/contacto",
    //             'link' => $this->contactPageUrl,
    //             'link_name' => "Help Center"
    //         ]);
    //     }

    //     if(empty($tags)){
    //         return response()->json([
    //             'status' => false,
    //             'message' => "No relevent Tag for query",
    //             'link_status' => true,
    //             // 'link' => "https://legalio.mx/contacto",
    //             'link' => $this->contactPageUrl,
    //             'link_name' => "Help Center"
    //         ]);
    //     }

    //     try {
    //         // Get all active FAQs with tags
    //         $faqs = AiFaq::where('status', 1)
    //         ->whereHas('tags', function ($query) use ($tags) {
    //             $query->whereIn('tags.id', $tags);
    //         })
    //         ->get();

    //         if ($faqs->isEmpty()) {
    //             return [
    //                 'status' => false,
    //                 'message' => "I'm sorry, I don't have that information. Please check our Support Center.",
    //                 'link_status' => true,
    //                 // 'link' => "https://legalio.mx/contacto",
    //                 'link' => $this->contactPageUrl,
    //                 'link_name' => "Support Center"
    //             ];
    //         }

    //         $faqText = $faqs->map(function ($faq) {
    //             return "Q: {$faq->question}\nA: {$faq->answer}";
    //         })->implode("\n\n");

    //         // Useful links (customize or fetch from DB as needed)
    //         $linksText = implode("\n", [
    //             "- Help Center: https://legalio.us/contact",
    //             "- Terms of Service: https://legalio.us/terms-conditions",

    //         ]);            

    //         // Set your Gemini API credentials
    //         $accessToken = $this->getAccessToken();
    //         // $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";

    //         $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:streamGenerateContent";


    //         // Prepare the system prompt for the Gemini model
    //         $systemPrompt = "You are a support assistant for Legalio.\n\n"
    //             . "Answer user questions **ONLY** using the provided FAQs.\n"
    //             . "You may paraphrase user questions to find the closest matching FAQ if the intent is clearly the same (e.g., 'how's it going' ≈ 'how are you').\n"
    //             . "If the user's message is unclear, ask them to clarify instead of sending a random response. But follow the message format.\n"
    //             . "Strictly follow this: If the answer is not in the FAQs, respond with the following **valid JSON** format:\n"
    //             . "Strictly follow this: If you have more then one FAQs paraphrase the answer to give single most valid answer\n"
    //             . "Strictly follow this: Do not just paste the same FAQs answer.\n"
    //             . "Strictly follow this: Add some related content to make it user friendly.\n"
    //             . "Strictly follow this: If user greet you just give normal greeting, introduce yourself as Legalio.\n"
    //             . "{\n"
    //             . "  \"status\": false,\n"
    //             . "  \"message\": \"I'm sorry, I don't have that information. Please check our Support Center.\",\n"
    //             . "  \"link_status\": true,\n"
    //             . "  \"link\": \"https://legalio.us/contact\",\n"
    //             . "  \"link_name\": \"Support Center\"\n"
    //             . "}\n\n"
    //             . "Always format your response in **valid JSON** with double quotes and **use null (not 'null' as a string) where applicable**:\n"
    //             . "{\n"
    //             . "  \"status\": true/false,\n"
    //             . "  \"message\": \"Your answer here\",\n"
    //             . "  \"link_status\": true/false,\n"
    //             . "  \"link\": null OR \"URL if applicable\",\n"
    //             . "  \"link_name\": null OR \"Name if applicable\"\n"
    //             . "}\n\n"
    //             . "**Important:** Always respond in the same language as the user's question.\n\n"
    //             . "**FAQs:**\n{$faqText}\n"
    //             . "**Useful Links:**\n{$linksText}";

    //         // Prepare the request payload for Gemini
    //         $requestPayload = [
    //             "contents" => [
    //                 [
    //                     "role" => "user",
    //                     "parts" => [
    //                         [
    //                             "text" => $systemPrompt . "\n\nUser question: {$userQuestion}"
    //                         ]
    //                     ]
    //                 ]
    //             ],
    //             "generationConfig" => [
    //                 "responseModalities" => ["TEXT"],
    //                 "temperature" => 0.2,
    //                 "maxOutputTokens" => 999,
    //                 "topP" => 0.8,
    //                 "responseMimeType" => "application/json",
    //                 "thinkingConfig" => [
    //                     "thinkingBudget" => 250
    //                 ]
    //             ],
    //             "systemInstruction" => [
    //                 "parts" => [
    //                     [
    //                         "text" => "You must respond with ONLY valid JSON. Do not include explanations, markdown, comments, or text outside the JSON object. The response must always be valid JSON."
    //                     ]
    //                 ]
    //             ],
    //             "safetySettings" => []
    //         ];




    //         // "generationConfig" => [
    //         //     "responseModalities" => ["TEXT"],
    //         //     "temperature" => 0.2,
    //         //     "maxOutputTokens" => 999,
    //         //     "topP" => 0.8,
    //         //     "responseMimeType" => "application/json",
    //         //     "thinkingConfig" => [
    //         //         "thinkingBudget" => 250
    //         //     ]
    //         // ],



    //         // Send request to Gemini API
    //         $response = Http::withHeaders([
    //             'Authorization' => "Bearer {$accessToken}",
    //             'Content-Type' => 'application/json'
    //         ])->timeout(15)->post($url, $requestPayload);

    //         if (!$response->successful()) {
    //             Log::error('Gemini API error: ' . $response->body());
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => "I'm sorry, I'm having trouble processing your request right now.",
    //                 'link_status' => true,
    //                 'link' => $this->contactPageUrl,
    //                 'link_name' => "Support Center"
    //             ]);
    //         }

    //         // Process the AI response
    //         $responseDecoded = $response->json();

    //         dump('responseDecoded 2');
    //         dd($responseDecoded);

    //         $generatedText = '';
    //         foreach ($responseDecoded as $candidateGroup) {
    //             foreach ($candidateGroup['candidates'] ?? [] as $candidate) {
    //                 foreach ($candidate['content']['parts'] ?? [] as $part) {
    //                     $generatedText .= $part['text'];
    //                 }
    //             }
    //         }

    //         // If no AI-generated text, return fallback
    //         if (empty(trim($generatedText))) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => "Sorry, I couldn't find a relevant answer in the FAQs.",
    //                 'link_status' => true,
    //                 'link' => $this->contactPageUrl,
    //                 'link_name' => "Support Center"
    //             ]);
    //         }

    //         $generatedText = trim($generatedText);
    //         $generatedText = preg_replace('/^```json|```$/m', '', $generatedText);
    //         $generatedText = trim($generatedText, "\" \n\r\t");
    //         $decodedJson = json_decode($generatedText, true);

    //         if(!empty($decodedJson) && $decodedJson['status'] == true ){
    //             // Return a structured response
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => $decodedJson['message'],
    //                 'data' => [
    //                     'question' => $userQuestion,
    //                     'answer' => trim($generatedText),
    //                 ],
    //                 'aiResponse' => $decodedJson,
    //                 'link_status' => false,
    //                 'link' => null,
    //                 'link_name' => null,
    //                 'tags' => $tags,
    //             ]);

    //         }

    //         return response()->json([
    //             'status' => false,
    //             'message' => "Sorry, I couldn't find a relevant answer in the FAQs.",
    //             'link_status' => true,
    //             'link' => $this->contactPageUrl,
    //             'link_name' => "Help Center",
    //             'tags' => $tags,
    //             'aiResponse' => $decodedJson
    //         ]);

    //     } 
    //     catch (\Exception $e) {

    //         Log::error('Error in FAQ assistant: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Something went wrong while answering your question.",
    //             'error' => $e->getMessage(),
    //             'link_status' => true,
    //             'link' => $this->contactPageUrl,
    //             'link_name' => "Help Center"
    //         ]);

    //     }
    // }

    public function generateFromFAQContext($userQuestion, $tags)
    {
        if (empty(trim($userQuestion))) {
            return response()->json([
                'status' => false,
                'message' => "Please ask a question so I can help you.",
                'link_status' => true,
                'link' => $this->contactPageUrl,
                'link_name' => "Help Center"
            ]);
        }

        if (empty($tags)) {
            return response()->json([
                'status' => false,
                'message' => "No relevant Tag for query",
                'link_status' => true,
                'link' => $this->contactPageUrl,
                'link_name' => "Help Center"
            ]);
        }

        try {
            // Get all active FAQs with tags
            $faqs = AiFaq::where('status', 1)
                ->whereHas('tags', function ($query) use ($tags) {
                    $query->whereIn('tags.id', $tags);
                })
                ->get();

            if ($faqs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => "I'm sorry, I don't have that information. Please check our Support Center.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center"
                ]);
            }
            if ($faqs->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => "I'm sorry, I don't have that information. Please check our Support Center.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center"
                ]);
            }

            if ($faqs->isEmpty()) {
                return $this->generateGeneralAnswer($userQuestion);
            }

            $faqText = $faqs->map(function ($faq) {
                return "Q: {$faq->question}\nA: {$faq->answer}";
            })->implode("\n\n");

            $linksText = implode("\n", [
                "- Help Center: https://legalio.us/contact",
                "- Terms of Service: https://legalio.us/terms-conditions",
            ]);

            $accessToken = $this->getAccessToken();

            $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:generateContent";

            $systemPrompt = "You are a support assistant for Legalio.\n\n"
                . "Answer user questions **ONLY** using the provided FAQs.\n"
                . "You may paraphrase user questions to find the closest matching FAQ if the intent is clearly the same.\n"
                . "If the user's message is unclear, ask them to clarify.\n"
                . "If you have more than one FAQ that matches, paraphrase and combine them into a single, coherent answer.\n"
                . "Add friendly, helpful language to make responses user-friendly.\n"
                . "If user greets you, give a normal greeting and introduce yourself as Legalio support assistant.\n\n"
                . "If the answer is NOT in the FAQs, respond with:\n"
                . "{\n"
                . "  \"status\": false,\n"
                . "  \"message\": \"I'm sorry, I don't have that information. Please check our Support Center.\",\n"
                . "  \"link_status\": true,\n"
                . "  \"link\": \"https://legalio.us/contact\",\n"
                . "  \"link_name\": \"Support Center\"\n"
                . "}\n\n"
                . "If the answer IS in the FAQs, respond with:\n"
                . "{\n"
                . "  \"status\": true,\n"
                . "  \"message\": \"Your helpful answer here\",\n"
                . "  \"link_status\": false,\n"
                . "  \"link\": null,\n"
                . "  \"link_name\": null\n"
                . "}\n\n"
                . "**CRITICAL:** Always respond in the same language as the user's question.\n"
                . "**CRITICAL:** Output ONLY the JSON object. No explanations, no markdown, no extra text.\n\n"
                . "**FAQs:**\n{$faqText}\n"
                . "**Useful Links:**\n{$linksText}";

            $requestPayload = [
                "contents" => [
                    [
                        "role" => "user",
                        "parts" => [
                            [
                                "text" => $systemPrompt . "\n\nUser question: {$userQuestion}"
                            ]
                        ]
                    ]
                ],
                "generationConfig" => [
                    "temperature" => 0.2,
                    "maxOutputTokens" => 1000,
                    "topP" => 0.8,
                    "topK" => 40,
                    "responseMimeType" => "application/json"
                ],
                "systemInstruction" => [
                    "parts" => [
                        [
                            "text" => "You MUST respond with ONLY valid JSON. No markdown code blocks, no explanations, no text outside the JSON object. Your entire response must be parseable as JSON."
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ])->timeout(20)->post($url, $requestPayload);

            if (!$response->successful()) {
                Log::error('Gemini API error: ' . $response->body());
                return response()->json([
                    'status' => false,
                    'message' => "I'm sorry, I'm having trouble processing your request right now.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center"
                ]);
            }

            $responseDecoded = $response->json();

            Log::info('Gemini API Response', ['response' => $responseDecoded]);

            $generatedText = '';

            if (isset($responseDecoded['candidates'][0]['content']['parts'])) {
                foreach ($responseDecoded['candidates'][0]['content']['parts'] as $part) {
                    if (isset($part['text'])) {
                        $generatedText .= $part['text'];
                    }
                }
            } elseif (is_array($responseDecoded)) {
                foreach ($responseDecoded as $chunk) {
                    if (isset($chunk['candidates'])) {
                        foreach ($chunk['candidates'] as $candidate) {
                            if (isset($candidate['content']['parts'])) {
                                foreach ($candidate['content']['parts'] as $part) {
                                    if (isset($part['text'])) {
                                        $generatedText .= $part['text'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $generatedText = trim($generatedText);

            if (empty($generatedText)) {
                return response()->json([
                    'status' => false,
                    'message' => "Sorry, I couldn't generate a response.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center"
                ]);
            }

            $generatedText = preg_replace('/^```json\s*/m', '', $generatedText);
            $generatedText = preg_replace('/\s*```$/m', '', $generatedText);
            $generatedText = trim($generatedText);

            $decodedJson = json_decode($generatedText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'raw_text' => $generatedText
                ]);

                return response()->json([
                    'status' => false,
                    'message' => "Sorry, I couldn't process the response properly.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center",
                    'debug' => [
                        'raw_response' => $generatedText,
                        'json_error' => json_last_error_msg()
                    ]
                ]);
            }

            if (!isset($decodedJson['status']) || !isset($decodedJson['message'])) {
                Log::error('Invalid JSON structure', ['decoded' => $decodedJson]);

                return response()->json([
                    'status' => false,
                    'message' => "Sorry, I received an invalid response format.",
                    'link_status' => true,
                    'link' => $this->contactPageUrl,
                    'link_name' => "Support Center"
                ]);
            }

            if ($decodedJson['status'] === true) {
                return response()->json([
                    'status' => true,
                    'message' => $decodedJson['message'],
                    'data' => [
                        'question' => $userQuestion,
                        'answer' => $decodedJson['message'],
                    ],
                    'aiResponse' => $decodedJson,
                    'link_status' => $decodedJson['link_status'] ?? false,
                    'link' => $decodedJson['link'] ?? null,
                    'link_name' => $decodedJson['link_name'] ?? null,
                    'tags' => $tags,
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $decodedJson['message'],
                'link_status' => $decodedJson['link_status'] ?? true,
                'link' => $decodedJson['link'] ?? $this->contactPageUrl,
                'link_name' => $decodedJson['link_name'] ?? "Help Center",
                'tags' => $tags,
                'aiResponse' => $decodedJson
            ]);
        } catch (\Exception $e) {
            Log::error('Error in FAQ assistant', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => "Something went wrong while answering your question.",
                'error' => config('app.debug') ? $e->getMessage() : null,
                'link_status' => true,
                'link' => $this->contactPageUrl,
                'link_name' => "Help Center"
            ]);
        }
    }

    public function generateFromFAQandTagContext($userQuestion)
    {
        if (empty(trim($userQuestion))) {
            return response()->json([
                'status'      => false,
                'message'     => "Please ask a question so I can help you.",
                'link_status' => true,
                'link'        => $this->contactPageUrl,
                'link_name'   => "Help Center"
            ]);
        }
     
        $greetings = [
            'hi',
            'hello',
            'hey',
            'how are you',
            'good morning',
            'good evening',
            'good afternoon',
            'good night',
            'hola',
            'buenos días',
            'buenas tardes'
        ];

        if (preg_match('/\b(' . implode('|', $greetings) . ')\b/i', $userQuestion)) {
            return response()->json([
                'status'      => true,
                'message'     => "Hello! I'm the Legalio.us support assistant. How can I help you today?",
                'link_status' => false,
                'link'        => null,
                'link_name'   => null,
            ]);
        }

        try {
            $tags   = Tag::all();
            $tagMap = $tags->map(fn($tag) => "{$tag->id}: {$tag->name}")->implode(", ");

            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                // No token? Still try to answer from general knowledge
                return $this->generateGeneralAnswer($userQuestion);
            }

            $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:streamGenerateContent";

            $systemPrompt = <<<EOT
            You are a support assistant for Legalio.
            Your task is to select the most relevant TAG IDs for the user's question.
            Return ONLY valid JSON:

            {
            "status": boolean,
            "tag_ids": number[],
            "message": string
            }

            Rules:
            - Use only provided TAGs
            - If match → status=true
            - If none → status=false and empty array
            - No extra text, no markdown
            - Same language as user

            TAGs:
            {$tagMap}
            EOT;

            $requestPayload = [
                "contents" => [[
                    "role"  => "user",
                    "parts" => [[
                        "text" => $systemPrompt . "\n\nUser question: {$userQuestion}"
                    ]]
                ]],
                "generationConfig" => [
                    "responseModalities" => ["TEXT"],
                    "temperature"        => 0,
                    "maxOutputTokens"    => 700,
                    "topP"               => 1,
                    "responseMimeType"   => "application/json",
                    "thinkingConfig"     => ["thinkingBudget" => 400]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type'  => 'application/json'
            ])->timeout(20)->post($url, $requestPayload);

            if (!$response->successful()) {
                Log::error('Gemini TAG API error: ' . $response->body());
                return $this->generateGeneralAnswer($userQuestion);
            }

            $responseDecoded = $response->json();
            $generatedText   = '';

            if (is_array($responseDecoded)) {
                foreach ($responseDecoded as $chunk) {
                    foreach ($chunk['candidates'] ?? [] as $candidate) {
                        foreach ($candidate['content']['parts'] ?? [] as $part) {
                            $generatedText .= $part['text'] ?? '';
                        }
                    }
                }
            }

            $generatedText = trim($generatedText);
            $generatedText = preg_replace('/^```json\s*/m', '', $generatedText);
            $generatedText = preg_replace('/\s*```$/m', '', $generatedText);

            Log::info('Gemini TAG raw response', ['text' => $generatedText]);
            if (empty($generatedText)) {
                return $this->generateGeneralAnswer($userQuestion);
            }

            $decodedJson = json_decode($generatedText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON parse failed', [
                    'error' => json_last_error_msg(),
                    'raw'   => $generatedText
                ]);
                if (preg_match('/\{.*\}/s', $generatedText, $matches)) {
                    $decodedJson = json_decode($matches[0], true);
                }
            }

            if (!is_array($decodedJson)) {
                return $this->generateGeneralAnswer($userQuestion);
            }

            $tagStatus = $decodedJson['status'] ?? false;
            $tagIds    = $decodedJson['tag_ids'] ?? [];

            if ($tagStatus === true && !empty($tagIds)) {
                // Try FAQ first, but if it fails, fall to general answer
                $faqResponse = $this->generateFromFAQContext($userQuestion, $tagIds);

                if ($faqResponse instanceof \Illuminate\Http\JsonResponse) {
                    $faqData = $faqResponse->getData(true);
                    // If FAQ returned a real answer, use it
                    if (($faqData['status'] ?? false) === true) {
                        return $faqResponse;
                    }
                }

                // FAQ didn't have a good answer, use general knowledge
                return $this->generateGeneralAnswer($userQuestion);
            }

            // No matching tags at all — always use general answer
            return $this->generateGeneralAnswer($userQuestion);
        } catch (\Exception $e) {
            Log::error('Error in TAG assistant', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            // Even on exception, try general answer before giving up
            return $this->generateGeneralAnswer($userQuestion);
        }
    }

    //     private function generateGeneralAnswer($userQuestion)
    // {
    //     $accessToken = $this->getAccessToken();
    //     $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:generateContent";

    //     $prompt = "You are a helpful support assistant for Legalio, a legal document platform. "
    //             . "Answer the following user question helpfully and concisely. "
    //             . "If you don't know the answer, suggest they visit https://legalio.us/contact\n\n"
    //             . "User question: {$userQuestion}";

    //     $requestPayload = [
    //         "contents" => [[
    //             "role"  => "user",
    //             "parts" => [["text" => $prompt]]
    //         ]],
    //         "generationConfig" => [
    //             "temperature"     => 0.5,
    //             "maxOutputTokens" => 500,
    //         ]
    //     ];

    //     $response = Http::withHeaders([
    //         'Authorization' => "Bearer {$accessToken}",
    //         'Content-Type'  => 'application/json'
    //     ])->timeout(20)->post($url, $requestPayload);

    private function generateGeneralAnswer($userQuestion)
    {
        $accessToken = $this->getAccessToken();
        $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:generateContent";

        $prompt = "You are a knowledgeable support assistant for Legalio.us, a U.S.-only legal document platform. "
            . "You have complete knowledge of the platform and MUST answer any question related to it. "
            . "NEVER say you don't have information about the platform — you always do.\n\n"

            . "=== COMPLETE PLATFORM KNOWLEDGE ===\n\n"

            . "WEBSITE: https://legalio.us/\n\n"

            . "DOCUMENT CATEGORIES AND TYPES:\n"
            . "1. Business & Commerce:\n"
            . "   - Partnership Agreements, LLC Operating Agreements, Non-Disclosure Agreements (NDA),\n"
            . "   - Business Sale Agreements, Shareholder Agreements, Franchise Agreements,\n"
            . "   - Service Contracts, Vendor Agreements, Independent Contractor Agreements,\n"
            . "   - Joint Venture Agreements, Loan Agreements, Promissory Notes\n\n"
            . "2. Personal Life:\n"
            . "   - Wills and Testaments, Power of Attorney, Living Wills,\n"
            . "   - Prenuptial Agreements, Divorce Settlement Agreements, Child Custody Agreements,\n"
            . "   - Personal Loan Agreements, Gift Deeds, Affidavits\n\n"
            . "3. Employment & Compliance:\n"
            . "   - Employment Contracts, Employee Offer Letters, Non-Compete Agreements,\n"
            . "   - Severance Agreements, Freelancer Contracts, Consulting Agreements,\n"
            . "   - HR Policies, Employee Handbooks, Termination Letters\n\n"
            . "4. Technology & Consumer:\n"
            . "   - Terms of Service, Privacy Policies, Cookie Policies,\n"
            . "   - Software License Agreements, SaaS Agreements, Website Development Contracts,\n"
            . "   - App Development Agreements, End User License Agreements (EULA)\n\n"
            . "5. Legal & Compliance:\n"
            . "   - Cease and Desist Letters, Demand Letters, Settlement Agreements,\n"
            . "   - Release of Liability Forms, Indemnity Agreements, Hold Harmless Agreements,\n"
            . "   - Arbitration Agreements, Confidentiality Agreements\n\n"
            . "6. Real Estate & Property:\n"
            . "   - Lease Agreements (residential & commercial), Rental Agreements,\n"
            . "   - Property Sale Agreements, Sublease Agreements, Roommate Agreements,\n"
            . "   - Month-to-Month Rental Agreements, Eviction Notices, Property Management Agreements\n\n"

            . "HOW TO CREATE A DOCUMENT:\n"
            . "1. Sign up or log in to your account at https://legalio.us/\n"
            . "2. Use the search bar or browse categories to find your document\n"
            . "3. Click the 'Create' button on the document page\n"
            . "4. Fill in the required fields on the left side of the page\n"
            . "5. Proceed to payment when all fields are complete\n"
            . "6. After payment is confirmed, your document is ready to download\n\n"

            . "HOW TO SIGN UP:\n"
            . "Click 'Sign Up', enter your first name, last name, email address, and password, then submit.\n\n"

            . "ACCOUNT MANAGEMENT:\n"
            . "- My Profile: Update personal information and address details\n"
            . "- Receipts & Invoices: View and manage all purchase records\n"
            . "- Reviews: Leave reviews for documents you've created\n"
            . "- Change Password: Update your login password from account settings\n"
            . "- Delete Account: Permanently delete your account from My Profile settings\n\n"

            . "PAYMENT & DOWNLOAD:\n"
            . "- Documents require a one-time payment before download\n"
            . "- After successful payment, documents are instantly available for download\n"
            . "- Purchase receipts are saved under Receipts & Invoices in your account\n\n"

            . "=== RESPONSE RULES ===\n"
            . "- Always respond in plain text with no markdown, no asterisks, no bullet symbols, no bold formatting\n"
            . "- Write in clear, complete sentences and paragraphs only\n"
            . "- Always give a complete answer — never cut off mid-sentence\n"
            . "- Keep answers between 2 to 6 sentences — concise but thorough\n"
            . "- Respond in the same language as the user's question\n"
            . "- If the question is about documents available on Legalio, list the relevant ones clearly\n"
            . "- ONLY suggest visiting https://legalio.us/contact if the question has nothing to do with Legalio or legal documents\n\n"

            . "User question: {$userQuestion}"
            . "if someone asked for joke then send them a funny joke.";

        $requestPayload = [
            "contents" => [[
                "role"  => "user",
                "parts" => [["text" => $prompt]]
            ]],
            "generationConfig" => [
                "temperature"     => 0.3,
                "maxOutputTokens" => 1024,
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json'
        ])->timeout(25)->post($url, $requestPayload);

        if (!$response->successful()) {
            Log::error('Gemini generateGeneralAnswer error: ' . $response->body());
            return response()->json([
                'status'      => false,
                'message'     => "I'm sorry, I'm having trouble connecting right now. Please try again or visit https://legalio.us/contact for help.",
                'link_status' => true,
                'link'        => $this->contactPageUrl,
                'link_name'   => 'Support Center',
            ]);
        }

        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (empty(trim($text ?? ''))) {
            return response()->json([
                'status'      => false,
                'message'     => "I'm sorry, I'm having trouble connecting right now. Please visit https://legalio.us/contact for help.",
                'link_status' => true,
                'link'        => $this->contactPageUrl,
                'link_name'   => 'Support Center',
            ]);
        }

        // Clean any markdown the model may have used
        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',
            '/\*(.*?)\*/s',
            '/^\s*[\*\-+]\s+/m',
            '/`{1,3}(.*?)`{1,3}/s',
            '/#{1,6}\s+/m',
        ], ['$1', '$1', '', '$1', ''], $text);

        $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));

        return response()->json([
            'status'      => true,
            'message'     => trim($cleanText),
            'link_status' => false,
            'link'        => null,
            'link_name'   => null,
        ]);
    }

    // private function generateGeneralAnswer($userQuestion)
    // {
    //     $accessToken = $this->getAccessToken();
    //     $url = "https://us-central1-aiplatform.googleapis.com/v1/projects/legalio-435913/locations/us-central1/publishers/google/models/gemini-2.5-pro:generateContent";

    //     $prompt = "You are a chat support assistant for Legalio.us, a U.S.-only platform where users can create a wide range of legal documents. Your role is to assist visitors by providing guidance on the platform, answering their queries, and directing them through various features and processes. Always respond in a friendly, professional, and helpful tone.\n\n"
    //         . "Key Information About the Website:\n"
    //         . "* Main Domain: https://legalio.us/\n"
    //         . "* Categories of Documents:\n"
    //         . "   * Business & Commerce\n"
    //         . "   * Personal Life\n"
    //         . "   * Employment & Compliance\n"
    //         . "   * Technology & Consumer\n"
    //         . "   * Legal & Compliance\n"
    //         . "   * Real Estate & Property\n\n"
    //         . "User Flow:\n"
    //         . "1. Signing Up / Logging In:\n"
    //         . "   * Users can create an account by signing up with their first name, last name, email, and password.\n"
    //         . "   * After signing in, users can access their dashboard to manage their documents and account settings.\n"
    //         . "2. Creating Documents:\n"
    //         . "   * Users can search for the type of document they need and click the Create button.\n"
    //         . "   * After selecting a document, users will fill out the necessary fields and proceed to payment.\n"
    //         . "   * After payment, the document will be ready for download.\n"
    //         . "3. Account Management:\n"
    //         . "   * My Profile: Users can update personal information and address details.\n"
    //         . "   * Receipts & Invoices: Users can manage their purchase records.\n"
    //         . "   * Reviews: Users can leave reviews for the documents they created.\n"
    //         . "   * Change Password: Users can change their login password.\n"
    //         . "   * Delete Account: Users can delete their account if they wish.\n\n"
    //         . "Response Style:\n"
    //         . "* Always maintain a helpful tone.\n"
    //         . "* Provide direct, easy-to-understand responses in plain text only.\n"
    //         . "* Do NOT use markdown formatting, bullet points, asterisks, bold, or any special characters in your response.\n"
    //         . "* Write in plain sentences and paragraphs only.\n"
    //         . "* Always provide a COMPLETE and FULL explanation. Never cut off your response mid-sentence.\n"
    //         . "* Keep responses concise but complete, between 2 to 5 sentences.\n"
    //         . "* When a user starts with a greeting, introduce yourself as the Legalio.us chat support and offer assistance.\n"
    //         . "* IMPORTANT: You MUST answer questions about how to use the Legalio platform using the information provided above.\n"
    //         . "* Only if the question is completely unrelated to Legalio, suggest they visit https://legalio.us/contact\n\n"
    //         . "User question: {$userQuestion}";

    //     $requestPayload = [
    //         "contents" => [[
    //             "role"  => "user",
    //             "parts" => [["text" => $prompt]]
    //         ]],
    //         "generationConfig" => [
    //             "temperature"     => 0.4,
    //             "maxOutputTokens" => 1024,
    //         ]
    //     ];

    //     $response = Http::withHeaders([
    //         'Authorization' => "Bearer {$accessToken}",
    //         'Content-Type'  => 'application/json'
    //     ])->timeout(25)->post($url, $requestPayload);

    //     if (!$response->successful()) {
    //         Log::error('Gemini generateGeneralAnswer error: ' . $response->body());
    //         return response()->json([
    //             'status'      => false,
    //             'message'     => "I'm sorry, I don't have that information. Please contact our support team.",
    //             'link_status' => true,
    //             'link'        => $this->contactPageUrl,
    //             'link_name'   => 'Support Center',
    //         ]);
    //     }

    //     $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;

    //     if (empty(trim($text ?? ''))) {
    //         return response()->json([
    //             'status'      => false,
    //             'message'     => "I'm sorry, I don't have that information. Please contact our support team.",
    //             'link_status' => true,
    //             'link'        => $this->contactPageUrl,
    //             'link_name'   => 'Support Center',
    //         ]);
    //     }

    //     // Strip any markdown formatting the model may have used despite instructions
    //     $cleanText = preg_replace([
    //         '/\*\*(.*?)\*\*/s',
    //         '/\*(.*?)\*/s',
    //         '/^\s*[\*\-+]\s+/m',
    //         '/`{1,3}(.*?)`{1,3}/s',
    //         '/#{1,6}\s+/m',
    //     ], [
    //         '$1',
    //         '$1',
    //         '',
    //         '$1',
    //         '',
    //     ], $text);

    //     $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));

    //     return response()->json([
    //         'status'      => true,
    //         'message'     => trim($cleanText),
    //         'link_status' => false,
    //         'link'        => null,
    //         'link_name'   => null,
    //     ]);
    // }


    public function aiVerificationPrompt($instructionPrompt, $firstresponse, $model)
    {
        $combinedPrompt = $instructionPrompt . "\n\n" . $firstresponse;

        if (strtolower($model) === 'gemini' || strtolower($model) === 'gemini 2.0' || strtolower($model) === 'gemini 2.5 pro') {
            return $this->generateWithGemini($combinedPrompt);
        }

        if (strtolower($model) === 'chatgpt' || strtolower($model) === 'gpt-3.5-turbo' || strtolower($model) === 'gpt-4') {
            return $this->generateWithOpenAI($combinedPrompt);
        }

        return "Error: Unsupported AI model selected.";
    }

    public function generateUniqueQID($usedIds, $type)
    {
        $usedIds = array_map('intval', $usedIds);
        $maxId = !empty($usedIds) ? max($usedIds) : 10000;
        $newId = $maxId + 1;

        if ($type == 'question') {
            $newUniqueID = 'QID' . $newId;
        } elseif ($type == 'content') {
            $newUniqueID = 'TID' . $newId;
        }

        return $newUniqueID;
    }

    // public function generateDocumentQuestionAndText($prompt){
    //     $accessToken = $this->getAccessToken();

    //     if (!$accessToken) {
    //         return "Error: Unable to retrieve access token.";
    //     }

    //     $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";

    //     // $imagePath = public_path($filepath);

    //     // if (!file_exists($imagePath)) {
    //     //     throw new \Exception("Image not found at $imagePath");
    //     // }

    //     // $imageData = base64_encode(file_get_contents($imagePath));

    //     $parts = [
    //         // [
    //         //     "inline_data" => [
    //         //         "mime_type" => "image/png",
    //         //         "data" => $imageData
    //         //     ]
    //         // ],
    //         [
    //             "text" => $prompt
    //         ]
    //     ];


    //     $requestPayload = [
    //         "contents" => [
    //             [
    //                 "role" => "user",
    //                 "parts" => $parts
    //             ]
    //         ],
    //         "generationConfig" => [
    //             "responseModalities" => ["TEXT"],
    //             "temperature" => 1,
    //             "maxOutputTokens" => 65536,
    //             "topP" => 0.95
    //         ],
    //         "safetySettings" => []
    //     ];

    //     $response = Http::withHeaders([
    //         'Authorization' => "Bearer {$accessToken}",
    //         'Content-Type' => 'application/json'
    //     ])->timeout(300)->post($url, $requestPayload);
    //     // return $response;

    //     if($response === false ){
    //         return "Error: Unable to connect to AI service.";
    //     }


    //     $responseDecoded = json_decode($response, true);
    //     // return $responseDecoded;

    //     $jsonParts = [];
    //     foreach ($responseDecoded as $candidateGroup) {
    //         foreach ($candidateGroup['candidates'] as $candidate) {
    //             foreach ($candidate['content']['parts'] as $part) {
    //                 $jsonParts[] = $part['text'];
    //             }
    //         }
    //     }

    //     $rawText = implode('', $jsonParts);

    //     $cleanText = preg_replace([
    //         '/\*\*(.*?)\*\*/s',    // bold
    //         '/\*(.*?)\*/s',        // italic
    //         '/^\s*[\*\-+]\s+/m',   // bullet points
    //         '/`{1,3}(.*?)`{1,3}/s' // code
    //     ], [
    //         '$1',
    //         '$1',
    //         '',
    //         '$1'
    //     ], $rawText);

    //     // Step 1: Detect all IDs
    //     preg_match_all('/QID([0-9A-Za-z]+)/', $cleanText, $matchesQ);
    //     preg_match_all('/TID([0-9A-Za-z]+)/', $cleanText, $matchesT);
    //     preg_match_all('/WQID([0-9A-Za-z]+)/', $cleanText, $matchesW);

    //     $foundQIDs = array_unique($matchesQ[1] ?? []);
    //     $foundTIDs = array_unique($matchesT[1] ?? []);
    //     $foundWQIDs = array_unique($matchesW[1] ?? []);

    //     // Step 2: Generate maps for new unique IDs
    //     $qidMap = [];
    //     $tidMap = [];
    //     $wqidMap = [];

    //     // QIDs
    //     if (!empty($foundQIDs)) {
    //         $existingQIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //         foreach ($foundQIDs as $oldId) {
    //             $newQID = $this->generateUniqueQID($existingQIds, 'question');
    //             $qidMap['QID' . $oldId] = $newQID;
    //             $existingQIds[] = str_replace('QID', '', $newQID);
    //         }
    //     }

    //     // TIDs
    //     if (!empty($foundTIDs)) {
    //         $existingTIds = DocumentRightSection::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //         foreach ($foundTIDs as $oldId) {
    //             $newTID = $this->generateUniqueQID($existingTIds, 'content');
    //             $tidMap['TID' . $oldId] = $newTID;
    //             $existingTIds[] = str_replace('TID', '', $newTID);
    //         }
    //     }

    //     // WQIDs
    //     if (!empty($foundWQIDs)) {
    //         $existingWIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //         foreach ($foundWQIDs as $oldId) {
    //             $newWQID = $this->generateUniqueQID($existingWIds, 'question');
    //             $wqidMap['WQID' . $oldId] = $newWQID;
    //             $existingWIds[] = str_replace('WQID', '', $newWQID);
    //         }
    //     }

    //     // Step 3: Replace old IDs with new IDs safely in text
    //     $finalText = $cleanText;

    //     // Safe replacement function
    //     $replaceIds = function ($map, $text) {
    //         foreach ($map as $old => $new) {
    //             $text = preg_replace(
    //                 '/(?<![A-Za-z0-9_])' . preg_quote($old, '/') . '(?![A-Za-z0-9_])/',
    //                 $new,
    //                 $text
    //             );
    //         }
    //         return $text;
    //     };

    //     $finalText = $replaceIds($qidMap, $finalText);
    //     $finalText = $replaceIds($tidMap, $finalText);
    //     $finalText = $replaceIds($wqidMap, $finalText);

    //     return $finalText;

    // }

    public function generateDocumentQuestionAndText($prompt)
    {
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return "Error: Unable to retrieve access token.";
            }

            $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";
            saveLog("URL:", "AIService", $url);

            $requestPayload = [
                "contents" => [[
                    "role" => "user",
                    "parts" => [["text" => $prompt]]
                ]],
                "generationConfig" => [
                    "responseModalities" => ["TEXT"],
                    "temperature" => 1,
                    "maxOutputTokens" => 65536,
                    "topP" => 0.95
                ],
                "safetySettings" => []
            ];
            saveLog("Request Payload:", "AIService", $requestPayload);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json'
            ])->withOptions([
                'stream' => true,
                'timeout' => 0,
            ])->post($url, $requestPayload);

            if ($response === false) {
                saveLog("Error:", "Unable to connect to AI service.", $response);
                return "Error: Unable to connect to AI service.";
            }

            saveLog("Response:", "AIService", $response);

            $responseDecoded = json_decode($response, true);
            saveLog("Response Decoded:", "AIService", $responseDecoded);

            $jsonParts = [];

            foreach ($responseDecoded as $group) {
                foreach ($group['candidates'] ?? [] as $candidate) {
                    foreach ($candidate['content']['parts'] ?? [] as $part) {
                        $jsonParts[] = $part['text'] ?? '';
                        // saveLog("JSON parts:", "AIService", $jsonParts);
                    }
                }
            }

            $rawText = implode('', $jsonParts);
            saveLog("Raw Text:", "AIService", $rawText);

            // Clean markdown/formatting
            $cleanText = preg_replace([
                '/\*\*(.*?)\*\*/s',
                '/\*(.*?)\*/s',
                '/^\s*[\*\-+]\s+/m',
                '/`{1,3}(.*?)`{1,3}/s'
            ], [
                '$1',
                '$1',
                '',
                '$1'
            ], $rawText);

            saveLog("Clean Text:", "AIService", $cleanText);

            // Correct capture of placeholders
            preg_match_all('/\{QID(\d+)\}/', $cleanText, $qMatches);
            preg_match_all('/\{TID(\d+)\}/', $cleanText, $tMatches);
            preg_match_all('/\{WQID(\d+)\}/', $cleanText, $wMatches);

            $foundQIDs = array_unique($qMatches[1] ?? []);
            $foundTIDs = array_unique($tMatches[1] ?? []);
            $foundWQIDs = array_unique($wMatches[1] ?? []);
            saveLog("Found QIDs:", "AIService", $foundQIDs);
            saveLog("Found TIDs:", "AIService", $foundTIDs);
            saveLog("Found WQIDs:", "AIService", $foundWQIDs);

            // Step 2: Generate maps for new unique IDs only for found placeholders
            $qidMap = [];
            $tidMap = [];
            $wqidMap = [];

            if (!empty($foundQIDs)) {
                $existingQIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
                foreach ($foundQIDs as $oldId) {
                    if (!in_array($oldId, $existingQIds)) {
                        $newQID = $this->generateUniqueQID($existingQIds, 'question');
                        $qidMap['QID' . $oldId] = $newQID;
                        $existingQIds[] = str_replace('QID', '', $newQID);
                    }
                }
            }

            if (!empty($foundTIDs)) {
                $existingTIds = DocumentRightSection::pluck('id')->map(fn($id) => (string)$id)->toArray();
                foreach ($foundTIDs as $oldId) {
                    if (!in_array($oldId, $existingTIds)) {
                        $newTID = $this->generateUniqueQID($existingTIds, 'content');
                        $tidMap['TID' . $oldId] = $newTID;
                        $existingTIds[] = str_replace('TID', '', $newTID);
                    }
                }
            }

            if (!empty($foundWQIDs)) {
                $existingWIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
                foreach ($foundWQIDs as $oldId) {
                    if (!in_array($oldId, $existingWIds)) {
                        $newWQID = $this->generateUniqueQID($existingWIds, 'question');
                        $wqidMap['WQID' . $oldId] = $newWQID;
                        $existingWIds[] = str_replace('WQID', '', $newWQID);
                    }
                }
            }

            // Replace only the found placeholders
            $replaceIds = function ($map, $text) {
                foreach ($map as $old => $new) {
                    $text = preg_replace(
                        '/(?<![A-Za-z0-9_])' . preg_quote($old, '/') . '(?![A-Za-z0-9_])/',
                        $new,
                        $text
                    );
                }
                return $text;
            };

            $finalText = $replaceIds($qidMap, $cleanText);
            $finalText = $replaceIds($tidMap, $finalText);
            $finalText = $replaceIds($wqidMap, $finalText);

            saveLog("Final Text:", "AIService", $finalText);

            return $finalText;
        } catch (\Exception $e) {
            saveLog("Error Exception:", "AIService", $e->getMessage());
        }
    }


    // public function generateDocumentQuestionAndText($prompt)
    // {
    //     try {
    //         $accessToken = $this->getAccessToken();
    //         if (!$accessToken) {
    //             return "Error: Unable to retrieve access token.";
    //         }

    //         $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:streamGenerateContent";

    //         $requestPayload = [
    //             "contents" => [[
    //                 "role" => "user",
    //                 "parts" => [["text" => $prompt]]
    //             ]],
    //             "generationConfig" => [
    //                 "responseModalities" => ["TEXT"],
    //                 "temperature" => 1,
    //                 "maxOutputTokens" => 65536,
    //                 "topP" => 0.95
    //             ],
    //             "safetySettings" => []
    //         ];

    //         $response = Http::withHeaders([
    //             'Authorization' => "Bearer {$accessToken}",
    //             'Content-Type' => 'application/json'
    //         ])->withOptions([
    //             'stream' => true,
    //             'timeout' => 0,
    //         ])->post($url, $requestPayload);

    //         if ($response === false) {
    //             return "Error: Unable to connect to AI service.";
    //         }

    //         $responseDecoded = json_decode($response, true);
    //         $jsonParts = [];

    //         foreach ($responseDecoded as $group) {
    //             foreach ($group['candidates'] ?? [] as $candidate) {
    //                 foreach ($candidate['content']['parts'] ?? [] as $part) {
    //                     $jsonParts[] = $part['text'] ?? '';
    //                 }
    //             }
    //         }

    //         $rawText = implode('', $jsonParts);

    //         $cleanText = preg_replace([
    //             '/\*\*(.*?)\*\*/s',    // bold
    //             '/\*(.*?)\*/s',        // italic
    //             '/^\s*[\*\-+]\s+/m',   // bullet points
    //             '/`{1,3}(.*?)`{1,3}/s' // code
    //         ], [
    //             '$1',
    //             '$1',
    //             '',
    //             '$1'
    //         ], $rawText);

    //         preg_match_all('/\{QID(\d+[A-Za-z]*)\}/', $cleanText, $qMatches);
    //         preg_match_all('/\{TID(\d+[A-Za-z]*)\}/', $cleanText, $tMatches);
    //         preg_match_all('/\{WQID(\d+[A-Za-z]*)\}/', $cleanText, $wMatches);

    //         $foundQIDs = array_unique($matchesQ[1] ?? []);
    //         $foundTIDs = array_unique($matchesT[1] ?? []);
    //         $foundWQIDs = array_unique($matchesW[1] ?? []);

    //         // Step 2: Generate maps for new unique IDs
    //         $qidMap = [];
    //         $tidMap = [];
    //         $wqidMap = [];

    //         // QIDs
    //         if (!empty($foundQIDs)) {
    //             $existingQIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //             foreach ($foundQIDs as $oldId) {
    //                 $newQID = $this->generateUniqueQID($existingQIds, 'question');
    //                 $qidMap['QID' . $oldId] = $newQID;
    //                 $existingQIds[] = str_replace('QID', '', $newQID);
    //             }
    //         }

    //         // TIDs
    //         if (!empty($foundTIDs)) {
    //             $existingTIds = DocumentRightSection::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //             foreach ($foundTIDs as $oldId) {
    //                 $newTID = $this->generateUniqueQID($existingTIds, 'content');
    //                 $tidMap['TID' . $oldId] = $newTID;
    //                 $existingTIds[] = str_replace('TID', '', $newTID);
    //             }
    //         }

    //         // WQIDs
    //         if (!empty($foundWQIDs)) {
    //             $existingWIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
    //             foreach ($foundWQIDs as $oldId) {
    //                 $newWQID = $this->generateUniqueQID($existingWIds, 'question');
    //                 $wqidMap['WQID' . $oldId] = $newWQID;
    //                 $existingWIds[] = str_replace('WQID', '', $newWQID);
    //             }
    //         }

    //         // Step 3: Replace old IDs with new IDs safely in text
    //         $finalText = $cleanText;

    //         // Safe replacement function
    //         $replaceIds = function ($map, $text) {
    //             foreach ($map as $old => $new) {
    //                 $text = preg_replace(
    //                     '/(?<![A-Za-z0-9_])' . preg_quote($old, '/') . '(?![A-Za-z0-9_])/',
    //                     $new,
    //                     $text
    //                 );
    //             }
    //             return $text;
    //         };

    //         $finalText = $replaceIds($qidMap, $finalText);
    //         $finalText = $replaceIds($tidMap, $finalText);
    //         $finalText = $replaceIds($wqidMap, $finalText);

    //         saveLog("Final Text:", "AIService", $finalText);

    //         return $finalText;



    //         // $foundQIDs = array_unique(array_map('trim', $qMatches[1] ?? []));
    //         // $foundTIDs = array_unique(array_map('trim', $tMatches[1] ?? []));
    //         // $foundWQIDs = array_unique(array_map('trim', $wMatches[1] ?? []));

    //         // saveLog("Found QIDs: " . json_encode($foundQIDs), "AIService");
    //         // saveLog("Found TIDs: " . json_encode($foundTIDs), "AIService");
    //         // saveLog("Found WQIDs: " . json_encode($foundWQIDs), "AIService");

    //         // $qidMap = $this->generateIdMap($foundQIDs, 'question', 'QID');
    //         // $tidMap = $this->generateIdMap($foundTIDs, 'content', 'TID');
    //         // $wqidMap = $this->generateIdMap($foundWQIDs, 'question', 'WQID');

    //         // $finalText = $cleanText;

    //         // foreach([$qidMap, $tidMap, $wqidMap] as $map){
    //         //     foreach($map as $old => $new){
    //         //         $newId = str_replace(['QID', 'TID', 'WQID'], '', $new);

    //         //         // Replace inside curly braces {QID2497}
    //         //         $finalText = preg_replace(
    //         //             '/\{' . preg_quote($old, '/') . '\}/',
    //         //             '{' . $newId . '}',
    //         //             $finalText
    //         //         );

    //         //         // Replace plain JSON strings like "QID2497"
    //         //         $finalText = preg_replace(
    //         //             '/"' . preg_quote($old, '/') . '"/',
    //         //             '"' . $newId . '"',
    //         //             $finalText
    //         //         );

    //         //         // Replace JSON keys like "QID2497": {
    //         //         $finalText = preg_replace(
    //         //             '/"' . preg_quote($old, '/') . '"\s*:/',
    //         //             '"' . $newId . '":',
    //         //             $finalText
    //         //         );
    //         //     }
    //         // }


    //         // // Replace old → new in your JSON text
    //         // foreach([$qidMap, $tidMap] as $map){
    //         //     foreach($map as $old => $new){
    //         //         $newId = str_replace(['QID', 'TID'], '', $new);
    //         //         $finalText = preg_replace('/\{' . preg_quote($old, '/') . '\}/', '{' . $newId . '}', $finalText);
    //         //         $finalText = preg_replace('/"' . preg_quote($old, '/') . '"/', '"' . $newId . '"', $finalText);
    //         //         $finalText = preg_replace('/"' . preg_quote($old, '/') . '"\s*:/', '"' . $newId . '":', $finalText);
    //         //     }
    //         // }


    //         // saveLog("Final Text: ", $finalText, "AIService");
    //         // return $finalText;

    //     } catch (Exception $e) {
    //         saveLog("Error Exception:12", "AIService", $e->getMessage());
    //     }
    // }


    public function generateDocumentQuestionAndTextWithOpenAI($prompt)
    {
        set_time_limit(0);
        $settings = web_setting(null, false, 'ai', 'chatgpt');
        $apiKey = $settings['generate_content_api'] ?? null;

        if (!$apiKey) {
            return "Error: OpenAI API key not configured.";
        }

        $model = $settings['model_id'] ?? 'gpt-3.5-turbo';

        $url = rtrim($settings['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 4096,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json'
        ])->timeout(300)
            ->post($url, $payload);


        if (!$response->successful()) {
            Log::error("OpenAI API error: " . $response->body());
            return 'Error: ' . $response->status() . ' - ' . $response->body();
        }

        // Get raw content
        $rawText = $response['choices'][0]['message']['content'] ?? 'No response received';
        // return $rawText;

        // Clean markdown-like formatting
        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',     // bold **text**
            '/\*(.*?)\*/s',         // italic *text*
            '/^\s*[\*\-+]\s+/m',    // unordered list markers
            '/`{1,3}(.*?)`{1,3}/s'  // inline code `text` or ```text```
        ], [
            '$1',
            '$1',
            '',
            '$1'
        ], $rawText);
        // return $cleanText;

        // Normalize extra line breaks
        $cleanText = preg_replace("/[\r\n]{2,}/", "\n\n", trim($cleanText));
        preg_match_all('/QID(\d+)/', $cleanText, $matches);
        preg_match_all('/TID(\d+)/', $cleanText, $matches1);
        preg_match_all('/WQID(\d+)/', $cleanText, $matches2);

        $foundQIDs = array_unique($matches[1] ?? []);
        $foundTIDs = array_unique($matches1[1] ?? []);
        $foundWQIDs = array_unique($matches2[1] ?? []);
        // return $foundWQIDs;

        $qidMap = [];
        $tidMap = [];
        $wqidMap = [];

        if (!empty($foundQIDs)) {
            $existingIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();

            foreach ($foundQIDs as $oldId) {
                $newQID = $this->generateUniqueQID($existingIds, 'question');
                $qidMap['QID' . $oldId] = $newQID;
                $existingIds[] = str_replace('QID', '', $newQID);
            }
        }

        if (!empty($foundTIDs)) {
            $existingTIds = DocumentRightSection::pluck('id')->map(fn($tid) => (string)$tid)->toArray();

            foreach ($foundTIDs as $oldTId) {
                $newTID = $this->generateUniqueQID($existingTIds, 'content');
                $tidMap['TID' . $oldTId] = $newTID;
                $existingTIds[] = str_replace('TID', '', $newTID);
            }
        }

        if (!empty($foundWQIDs)) {
            $existingWIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();

            foreach ($foundWQIDs as $oldWId) {
                $newWQID = $this->generateUniqueQID($existingWIds, 'question');
                $wqidMap['WQID' . $oldWId] = $newWQID;
                $existingWIds[] = str_replace('WQID', '', $newWQID);
            }
        }


        $finalText = $cleanText;

        foreach ($qidMap as $old => $new) {
            $finalText = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $finalText);
        }

        foreach ($tidMap as $old => $new) {
            $finalText = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $finalText);
        }

        foreach ($wqidMap as $old => $new) {
            $finalText = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $finalText);
        }

        return $finalText;
    }

    public function documentGenerationAIVerification($instructionPrompt, $firstresponse, $model)
    {
        $combinedPrompt = $instructionPrompt . "\n\n" . $firstresponse;

        if (strtolower($model) === 'gemini' || strtolower($model) === 'gemini 2.0' || strtolower($model) === 'gemini 2.5 pro') {
            return $this->generateDocumentQuestionAndText($combinedPrompt);
        }

        if (strtolower($model) === 'chatgpt' || strtolower($model) === 'gpt-3.5-turbo' || strtolower($model) === 'gpt-4') {
            return $this->generateDocumentQuestionAndTextWithOpenAI($combinedPrompt);
        }

        return "Error: Unsupported AI model selected.";
    }

    public function validateAIOutput(array $decoded)
    {
        $errors = [];

        $questionData = collect($decoded['Questionnaire'] ?? []);
        $contractData = collect($decoded['Contract_Text'] ?? []);

        if ($questionData->count() < 10) {
            $errors[] = "The contract must contain at least 10 Questionnaire items.";
        }

        if ($contractData->count() < 10) {
            $errors[] = "The contract must contain at least 10 Contract_text items.";
        }

        $qidList = [];
        foreach ($questionData as $qid => $question) {

            if (!is_array($question)) {
                $errors[] = "Question {$qid} must be a valid object.";
                continue;
            }

            $qidList[] = $qid;

            $type = $question['TYPE'] ?? null;
            $label = $question['label'] ?? null;
            $placeholder = $question['placeholder'] ?? null;
            $userinfo = $question['userinfo'] ?? null;

            if (empty($type)) {
                $errors[] = "Question {$qid} is missing the TYPE field.";
            }

            if (!in_array($type, ['TEXTBOX', 'TEXTAREA', 'DROPDOWN', 'RADIOBUTTON', 'DATEFIELD', 'PRICEBOX', 'NUMBERFIELD', 'PERCENTAGEBOX'])) {
                $errors[] = "Question {$qid} has an invalid TYPE: {$type}.";
            }

            if ($type !== 'DATEFIELD') {
                if (empty($placeholder)) {
                    $errors[] = "Question {$qid} is missing a placeholder.";
                }
                if (empty($userinfo)) {
                    $errors[] = "Question {$qid} is missing userinfo.";
                }
            }

            if (in_array($type, ['DROPDOWN', 'RADIOBUTTON'])) {
                $options = $question['options'] ?? [];
                if (!is_array($options) || count($options) === 0) {
                    $errors[] = "Question {$qid} must have options.";
                } else {
                    foreach ($options as $index => $option) {
                        if (empty($option['option_label']) || empty($option['option_value']) || empty($option['go_next_step'])) {
                            $errors[] = "Option #{$index} of question {$qid} must include option_label, option_value, and go_next_step.";
                        }
                    }
                }
            }

            $hasGoto = isset($question['goto']) && $question['goto'] !== '';
            $hasGotoIf = isset($question['goto_if']) && is_array($question['goto_if']) && count($question['goto_if']) > 0;
            $hasAnotherGoTo = isset($question['another_go_to_step']) && is_array($question['another_go_to_step']) && count($question['another_go_to_step']) > 0;

            if (!$hasGoto && !$hasGotoIf) {
                $errors[] = "Question {$qid} must have either 'goto' or 'goto_if'.";
            }

            if (isset($question['goto_if'])) {
                foreach ($question['goto_if'] as $i => $cond) {
                    if (!isset($cond['question_id'], $cond['conditions'], $cond['question_value'])) {
                        $errors[] = "goto_if condition #{$i} in Question {$qid} is missing required fields.";
                    }
                }
            }

            if ($hasAnotherGoTo) {
                foreach ($question['another_go_to_step'] as $index => $gotoBlock) {
                    if (!isset($gotoBlock['conditional_go_to_step']) || !isset($gotoBlock['subconditions'])) {
                        $errors[] = "another_go_to_step #{$index} in Question {$qid} must include conditional_go_to_step and subconditions.";
                    } else {
                        foreach ($gotoBlock['subconditions'] as $subindex => $sub) {
                            if (!isset($sub['question_id'], $sub['conditions'], $sub['question_value'])) {
                                $errors[] = "subcondition #{$subindex} in another_go_to_step #{$index} of Question {$qid} is missing required fields.";
                            }
                        }
                    }
                }
            }

            if (isset($question['question_label_condition'])) {
                foreach ($question['question_label_condition'] as $i => $labelCondition) {
                    if (!isset($labelCondition['question_id'], $labelCondition['value'], $labelCondition['label'])) {
                        $errors[] = "question_label_condition #{$i} in Question {$qid} is missing required fields.";
                    }
                }
            }

            if (isset($question['condition_type']) && !in_array($question['condition_type'], [1, 2, 3])) {
                $errors[] = "Question {$qid} has invalid condition_type value.";
            }
        }

        $tidList = [];
        $firstBlockIsHeadline = false;

        foreach ($contractData as $index => $block) {
            if (!is_array($block)) continue;

            $tidList[] = $index;

            $type = $block['TYPE'] ?? null;
            $text = $block['TEXT'] ?? null;
            $align = $block['ALIGN_TEXT'] ?? null;
            $conditions = $block['CONDITIONS'] ?? [];
            $blur = $block['BLUR_CONTENT'] ?? null;

            if (!$type || !in_array($type, ['HEADLINE', 'CONTENT', 'SIGNATURE'])) {
                $errors[] = "Contract_Text {$index} has invalid or missing TYPE.";
            }

            if (empty($text)) {
                $errors[] = "Contract_Text {$index} is missing TEXT.";
            } elseif (!preg_match('/QID\d+/i', $text)) {
                $errors[] = "Contract_Text {$index} must reference at least one QIDx in TEXT.";
            }

            if (!in_array($align, ['left', 'right', 'center'])) {
                $errors[] = "Contract_Text {$index} ALIGN_TEXT must be 'left', 'right', or 'center'.";
            }

            if (!is_array($conditions)) {
                $errors[] = "Contract_Text {$index} CONDITIONS must be an array.";
            }

            if (!is_bool($blur)) {
                $errors[] = "Contract_Text {$index} BLUR_CONTENT must be true or false.";
            }

            if ($type === 'SIGNATURE' && !preg_match('/QID\d+/i', $text)) {
                $errors[] = "Signature block {$index} must reference a QIDx in TEXT.";
            }

            $contractDataArray = $contractData->toArray();
            $firstTID = array_key_first($contractDataArray);
            if ($index === $firstTID && $type !== 'HEADLINE') {
                $errors[] = "The first Contract_Text block must be of type HEADLINE.";
            }
        }

        if (count($qidList) !== count(array_unique($qidList))) {
            $errors[] = "Duplicate QIDx found in Questionnaire.";
        }

        if (count($tidList) !== count(array_unique($tidList))) {
            $errors[] = "Duplicate TIDx found in Contract_Text.";
        }

        return $errors;
    }

    public function generateQuestionsAndTextByFeedback($prompt)
    {
        // return $prompt;
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return "Error: Unable to retrieve access token.";
        }

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:generateContent";

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseModalities" => ["TEXT"],
                "temperature" => 0.7,
                "maxOutputTokens" => 8192,
                "topP" => 0.95
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json'
        ])
            ->timeout(120)
            ->post($url, $requestPayload);

        if (!$response->ok()) {
            return "Error: " . $response->body();
        }

        $responseDecoded = $response->json();

        $aiOutput = $responseDecoded['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiOutput) {
            return "Error: No AI output returned.";
        }

        $cleanText = preg_replace([
            '/\*\*(.*?)\*\*/s',
            '/\*(.*?)\*/s',
            '/^\s*[\*\-+]\s+/m',
            '/`{1,3}(.*?)`{1,3}/s'
        ], [
            '$1',
            '$1',
            '',
            '$1'
        ], $aiOutput);

        preg_match_all('/QID(\d+)/', $cleanText, $matches);
        preg_match_all('/TID(\d+)/', $cleanText, $matches1);
        $foundQIDs = array_unique($matches[1] ?? []);
        $foundTIDs = array_unique($matches1[1] ?? []);

        $foundQIDs = array_unique($foundQIDs);
        $foundTIDs = array_unique($foundTIDs);

        $qidMap = [];
        $tidMap = [];

        if (!empty($foundQIDs)) {
            $existingIds = Question::pluck('id')->map(fn($id) => (string)$id)->toArray();
            foreach ($foundQIDs as $oldId) {
                $newQID = $this->generateUniqueQID($existingIds, 'question');
                $qidMap['QID' . $oldId] = $newQID;
                $existingIds[] = str_replace('QID', '', $newQID);
            }
        }

        if (!empty($foundTIDs)) {
            $existingTIds = DocumentRightSection::pluck('id')->map(fn($tid) => (string)$tid)->toArray();

            foreach ($foundTIDs as $oldTId) {
                $newTID = $this->generateUniqueQID($existingTIds, 'content');
                $tidMap['TID' . $oldTId] = $newTID;
                $existingTIds[] = str_replace('TID', '', $newTID);
            }
        }

        $finalText = $cleanText;

        foreach ($qidMap as $old => $new) {
            $finalText = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $finalText);
        }

        foreach ($tidMap as $old => $new) {
            $finalText = preg_replace('/\b' . preg_quote($old, '/') . '\b/', $new, $finalText);
        }

        return $finalText;
    }

    public function recommendedSectionIds($prompt)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return "Error: Unable to retrieve access token.";
        }

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:generateContent";

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseModalities" => ["TEXT"],
                "temperature" => 0.7,
                "maxOutputTokens" => 8192,
                "topP" => 0.95
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json'
        ])
            ->timeout(120)
            ->post($url, $requestPayload);

        if (!$response->ok()) {
            return "Error: " . $response->body();
        }

        $responseDecoded = $response->json();

        $aiOutput = $responseDecoded['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiOutput) {
            return "Error: No AI output returned.";
        }

        if (preg_match('/\{.*\}/s', $aiOutput, $matches)) {
            $aiOutput = $matches[0];
        }

        // Fix JSON (single → double quotes)
        $aiOutput = str_replace("'", '"', $aiOutput);

        // Decode JSON
        $json = json_decode($aiOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return "Error: Invalid JSON response - " . json_last_error_msg() . " | Raw: " . $aiOutput;
        }

        return $json['standard_sections_ids'] ?? [];
    }

    public function recommendedSectionIdsWithOpenAI($prompt)
    {
        $settings = web_setting(null, false, 'ai', 'chatgpt');
        $apiKey   = $settings['generate_content_api'] ?? null;

        if (!$apiKey) {
            return "Error: OpenAI API key not configured.";
        }

        $model = $settings['model_id'] ?? 'gpt-4-turbo';
        $url   = rtrim($settings['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a strict JSON generator. Always respond only with valid JSON in the format: {"standard_sections_ids": [1,2,3]}'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0,
            'max_tokens' => 512,
            'response_format' => ['type' => 'json_object'] // force JSON output
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json'
        ])->timeout(120)->post($url, $payload); // allow longer timeout

        if (!$response->successful()) {
            Log::error("OpenAI API error: " . $response->body());
            return 'Error: ' . $response->status() . ' - ' . $response->body();
        }

        $aiOutput = $response['choices'][0]['message']['content'] ?? null;

        if (!$aiOutput) {
            return "Error: No AI output returned.";
        }

        $json = json_decode($aiOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("Failed to decode AI JSON: " . $aiOutput);
            return "Error: Invalid JSON response - " . json_last_error_msg();
        }

        return $json['standard_sections_ids'] ?? [];
    }

    public function generateSectionFromIds($prompt)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return "Error: Unable to retrieve access token.";
        }

        $url = "https://{$this->apiEndpoint}/v1/projects/{$this->projectId}/locations/{$this->locationId}/publishers/google/models/{$this->model}:generateContent";

        $requestPayload = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "responseModalities" => ["TEXT"],
                "temperature" => 0.7,
                "maxOutputTokens" => 8192,
                "topP" => 0.95
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json'
        ])
            ->timeout(120)
            ->post($url, $requestPayload);

        if (!$response->ok()) {
            return "Error: " . $response->body();
        }

        $responseDecoded = $response->json();

        $aiOutput = $responseDecoded['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$aiOutput) {
            return "Error: No AI output returned.";
        }

        if (preg_match('/\{.*\}/s', $aiOutput, $matches)) {
            $aiOutput = $matches[0];
        }

        $decoded = json_decode($aiOutput, true);

        if (!$decoded || !isset($decoded['Sections'])) {
            return "Error: AI did not return valid Sections JSON.";
        }

        $sectionIds = $decoded['Sections'];

        return $sectionIds;
    }

    public function generateSectionFromIdsWithOpenAI($prompt)
    {
        $settings = web_setting(null, false, 'ai', 'chatgpt');
        $apiKey = $settings['generate_content_api'] ?? null;

        if (!$apiKey) {
            return "Error: OpenAI API key not configured.";
        }

        $model = $settings['model_id'] ?? 'gpt-3.5-turbo';
        $url   = rtrim($settings['api_endpoint'] ?? 'https://api.openai.com/v1', '/') . '/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type'  => 'application/json'
        ])->post($url, $payload);

        if (!$response->successful()) {
            Log::error("OpenAI API error: " . $response->body());
            return 'Error: ' . $response->status() . ' - ' . $response->body();
        }

        // Extract AI output
        $aiOutput = $response['choices'][0]['message']['content'] ?? null;

        if (!$aiOutput) {
            return "Error: No AI output returned.";
        }

        // Try to extract JSON from the AI response
        if (preg_match('/\{.*\}/s', $aiOutput, $matches)) {
            $aiOutput = $matches[0];
        }

        $decoded = json_decode($aiOutput, true);

        if (!$decoded || !isset($decoded['Sections'])) {
            Log::warning("AI output did not return valid JSON: " . $aiOutput);
            return "Error: AI did not return valid Sections JSON.";
        }

        return $decoded['Sections'];
    }
}
