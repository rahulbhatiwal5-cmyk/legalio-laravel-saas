<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiFaq;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AIService;
use App\Models\AiAssistantChat;

class AiAssistantController extends Controller
{
    protected $AI;

    public function __construct(AIService $service)
    {
        $this->AI = $service;
    }

    public function askFaqTags(Request $request)
    {
        $userQuestion = $request->input('message');

        if (empty(trim($userQuestion))) {
            return response()->json([
                'status'      => false,
                'message'     => "Please enter a question so I can help you.",
                'link_status' => true,
                'link'        => "https://legalio.us/contact",
                'link_name'   => "Help Center"
            ]);
        }

        $response = $this->AI->generateFromFAQandTagContext($userQuestion);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $responseData = $response->getData(true);

            if (isset($responseData['message'])) {

                AiAssistantChat::create([
                    'user_id'  => auth()->id(),
                    'question' => $userQuestion,
                    'answer'   => json_encode([
                        'status'      => $responseData['status']      ?? false,
                        'message'     => $responseData['message'],
                        'link_status' => $responseData['link_status'] ?? false,
                        'link'        => $responseData['link']        ?? null,
                        'link_name'   => $responseData['link_name']   ?? null,
                    ]),
                ]);

                return response()->json([
                    'status'      => $responseData['status']      ?? false,
                    'message'     => $responseData['message'],
                    'link_status' => $responseData['link_status'] ?? false,
                    'link'        => $responseData['link']        ?? null,
                    'link_name'   => $responseData['link_name']   ?? null,
                ]);
            }
        }

        Log::error('AiAssistantController: unexpected response type from AIService', [
            'response' => $response,
        ]);

        AiAssistantChat::create([
            'user_id'  => auth()->id(),
            'question' => $userQuestion,
            'answer'   => json_encode([
                'status'      => false,
                'message'     => 'Something went wrong. Please try again.',
                'link_status' => true,
                'link'        => 'https://legalio.us/contact',
                'link_name'   => 'Support Center',
            ]),
        ]);

        return response()->json([
            'status'      => false,
            'message'     => 'Something went wrong. Please try again.',
            'link_status' => true,
            'link'        => 'https://legalio.us/contact',
            'link_name'   => 'Support Center',
        ]);
    }

    private function generateFAQContext($faqs)
    {
        $context = "";
        foreach ($faqs as $faq) {
            $context .= "Q: " . $faq->question . "\n";
            $context .= "A: " . $faq->answer . "\n\n";
        }
        return $context;
    }
}
