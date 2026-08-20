<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrepareContract;
use App\Models\PrepareContractWork;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\LoginRegister;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Question;
use App\Models\DocumentRightSection;
use App\Models\GeneralSection;
use App\Models\SaveContractQuestion;
use App\Models\QuestionData;
use App\Models\SavedDataId;
use App\Models\ContractContent;
use App\Models\Review;
use App\Models\Plans;
use App\Models\UserPlan;
use App\Models\UserCredit;
use App\Models\CreditTransaction;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class ContractController extends Controller
{
    public function lawyerContract(){
        $keys = [
            'title',
            'fb_link',
            'twitter_link',
            'pinterest_link',
            'short_description',
            'description',
            'page_sub_heading',
            'work_main_heading',
            'economical_main_heading',
            'economical_description',
            'button_text',
            'button_link'
        ];

        $results = PrepareContract::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'fb_link' => $results['fb_link']->value ?? null,
            'twitter_link' => $results['twitter_link']->value ?? null,
            'pinterest_link' => $results['pinterest_link']->value ?? null,
            'short_description' => $results['short_description']->value ?? null,
            'description' => $results['description']->value ?? null,
            'page_sub_heading' => $results['page_sub_heading']->value ?? null,
            'work_main_heading' => $results['work_main_heading']->value ?? null,
            'economical_main_heading' => $results['economical_main_heading']->value ?? null,
            'economical_description' => $results['economical_description']->value ?? null,
            'button_text' => $results['button_text']->value ?? null,
            'button_link' => $results['button_link']->value ?? null,
        ];

        $image = PrepareContract::where('key','image')->whereNotNull('media_id')->with('media')->first();
        $work_sec =  PrepareContract::where('key','prepare_work')->with('contract_work','media')->get();
        $products = Product::where('category_id','4')->get();

        return view('users.contracts.prepare_your_contract_with_lawyer',compact('data','image','work_sec','products'));
    }

    public function getContract(Request $request){
        try{
            if(isset($request->id) && $request->id != null){
                $product = Product::find($request->id);

                $response = ([
                    'data'=> $product,
                    'status'=> 200
                ]);

                return response()->json($response);
            }

        }catch(Exception $e){
            saveLog("Error:", "ContractController", $e->getMessage());
        }
    }
    
    public function legalDocument($page = 1){
        $legal = LoginRegister::where('key','legal')->first();
        $document_category = DocumentCategory::where('is_deleted',0)->get();
        $alldocuments = Document::where('published',1)->paginate(15, ['*'], 'page', $page);
        $alldocuments->withPath('/legal-documents');
        return view('users.contracts.legal_document',compact('legal','document_category','alldocuments'));
    }

    public function categoryDetail($slug){
        $category = DocumentCategory::where(['slug' => $slug , 'is_deleted' => 0])->first();
        if (empty($category)) {
            abort(404);
        }
        $document_category = DocumentCategory::where('is_deleted',0)->get();
        
        // $alldocuments = Document::where('published',1)->paginate(12);
        $alldocuments = Document::where('published',1)->with('categories')->get();

        return view('users.contracts.category_detail',compact('category','document_category','alldocuments'));
    }

    private function encryptText($text, $key){
        $cipherMethod = "AES-256-CBC";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod)); // Generate a secure IV
        // Encrypt the text
        $encryptedText = openssl_encrypt($text, $cipherMethod, $key, 0, $iv);
        // Encode IV with the encrypted text
        return base64_encode($iv . $encryptedText);
    }

    public function contracts($slug){
        $document = Document::where('slug',$slug)->first();
        $id = $document->id;
        $documents = Session::get('document_id','');
        Session::put('document_id', $id);
        
        $maxReviews = 5;

        // 1. Get 5-star reviews from this document WITH file_path (profile image exists)
        $showReviews = Review::where([
                ['status', '1'],
                ['document_id', $document->id],
                ['rating', '5']
            ])
            ->whereHas('user', function ($query) {
                $query->whereNotNull('file_path'); // Check if file_path exists
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take($maxReviews)
            ->get();
        // dd($showReviews);


        // 2. If not enough, get from this document WITHOUT file_path (no profile image)
        if ($showReviews->count() < $maxReviews) {
            $remaining = $maxReviews - $showReviews->count();
            // dd($remaining);
            $noImageReviews = Review::where([
                    ['status', '1'],
                    ['document_id', $document->id],
                    ['rating', '5']
                ])
                ->whereHas('user', function ($query) {
                    $query->whereNull('file_path'); // Check if file_path is null (no profile image)
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take($remaining)
                ->get();
                // dd($noImageReviews);
            // Merge with the previous set of reviews
            $showReviews = $showReviews->merge($noImageReviews);

        }

        // 3. If still not enough, get from other documents WITH file_path (profile image exists)
        if ($showReviews->count() < $maxReviews) {
            $remaining = $maxReviews - $showReviews->count();

            $otherDocWithImage = Review::where('status', '1')
                ->where('rating', '5')
                ->where('document_id', '!=', $document->id)
                ->whereHas('user', function ($query) {
                    $query->whereNotNull('file_path'); // Check if file_path exists
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take($remaining)
                ->get();
                // dd($otherDocWithImage);
            // Merge with the previous set of reviews
            $showReviews = $showReviews->merge($otherDocWithImage);
        }

        // 4. If still not enough, get from other documents WITHOUT file_path (no profile image)
        if ($showReviews->count() < $maxReviews) {
            $remaining = $maxReviews - $showReviews->count();

            $otherDocNoImage = Review::where('status', '1')
                ->where('rating', '5')
                ->where('document_id', '!=', $document->id)
                ->whereHas('user', function ($query) {
                    $query->whereNull('file_path'); // Check if file_path is null (no profile image)
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take($remaining)
                ->get();

            // Merge with the previous set of reviews
            $showReviews = $showReviews->merge($otherDocNoImage);
        }

        // Ensure we have the required number of reviews
        // dd($showReviews);
        $id = (int) $id;
        $questions = Question::where('document_id', $id)
        ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
        ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
        ->get();
 
    

        // $questions = Question::where('document_id',$id)->with(['questionData', 'conditions', 'options', 'nextQuestion'])->orderByRaw('CAST(order_id AS UNSIGNED) ASC')->get();
        // $documentContents = DocumentRightSection::where('document_id', $id)->get();
        
        //  Proper ordering - first by standard_section_id (with NULLs last), then by order_id within each section
        $documentContents = DocumentRightSection::where('document_id', $id)
            ->with('conditions'
            // ,'document'
            )
            // ->orderByRaw('standard_section_id IS NULL')
            // ->orderBy('standard_section_id', 'asc')  
            ->orderBy('order_id', 'asc')              
            ->get();

        // dd($documentContents->toArray());    

        $adminDocumentContents = $documentContents->map(function ($item) {
            return clone $item;
        });

        

        foreach($documentContents as $content){
            $content->content = preg_replace_callback(
                '/#(\d+)#|\{(\d+)\}|\{W?QID(\d+)\}/i',
                function($matches) {
                    // Fix: correctly capture the number from whichever group matched
                    if (!empty($matches[1])) {
                        $classNumber = $matches[1];
                    } elseif (!empty($matches[2])) {
                        $classNumber = $matches[2];
                    } else {
                        $classNumber = $matches[3]; // QID group
                    }
                    return "<span class=\"answered_spns qidtarget-$classNumber\">_______</span>";
                },
                $content->content
            );
        
            if($content->type == 'signature_field'){
                $content->content2 = preg_replace_callback(
                    '/#(\d+)#|\{(\d+)\}|\{W?QID(\d+)\}/i',
                    function($matches) {
                        if (!empty($matches[1])) {
                            $classNumber = $matches[1];
                        } elseif (!empty($matches[2])) {
                            $classNumber = $matches[2];
                        } else {
                            $classNumber = $matches[3];
                        }
                        return "<span class=\"answered_spns qidtarget-$classNumber\">_______</span>";
                    },
                    $content->content2 ?? '',
                );
        
                $content->content3 = preg_replace_callback(
                    '/#(\d+)#|\{(\d+)\}|\{W?QID(\d+)\}/i',
                    function($matches) {
                        if (!empty($matches[1])) {
                            $classNumber = $matches[1];
                        } elseif (!empty($matches[2])) {
                            $classNumber = $matches[2];
                        } else {
                            $classNumber = $matches[3];
                        }
                        return "<span class=\"answered_spns qidtarget-$classNumber\">_______</span>";
                    },
                    $content->content3 ?? '',
                );
            }
        
            $content->content = preg_replace_callback(
                '/{abclist1}/',
                function($matches){
                    $list = substr($matches[0], 1, -1);
                    return "<span class=\"abclist $list\"></span>";
                },
                $content->content
            );
        
            if($content->secure_blur_content){
                $content->content= $this->encryptText($content->content, "test");
                $content->content2= $this->encryptText($content->content2 ?? '', "test");
                $content->content3= $this->encryptText($content->content3 ?? '', "test");
            }
        }

        $keys = [
            'rating_text',
            'valid_in',
            'contract_heading',
            'review_modal_hace_text',
        ];

        $results = GeneralSection::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'rating_text' => $results['rating_text']->value ?? null,
            'valid_in' => $results['valid_in']->value ?? null,
            'contract_heading' => $results['contract_heading']->value ?? null,
            'review_modal_hace_text' => $results['review_modal_hace_text']->value ?? null,
        ];


        $total_questions = count($questions);
        // dd($total_questions , $questions );
        return view('users.contracts.contracts', compact('questions', 'documentContents','id','document','total_questions','showReviews','data' , 'adminDocumentContents'));
    }

    public function saveContractsQuestions(Request $request)
    {
        $userID = $request->user_id;
        $documentID = $request->document_id;
        $questions = $request->attempted_questions;

        if (empty($userID) || empty($questions)) {
            return response()->json([
                'code' => '400',
                'status' => 'invalid request'
            ]);
        }
        $savedData = SavedDataId::firstOrCreate(
            ['user_id' => $userID, 'document_id' => $documentID],
            ['user_id' => $userID, 'document_id' => $documentID]
        );

        $saved_id = $savedData->id;
        $status = null;

        $questionIds = Question::where('document_id', $documentID)->pluck('id');
        $questionDataIds = QuestionData::whereIn('question_id', $questionIds)->pluck('question_id')->unique();

        SaveContractQuestion::where('saved_id', $saved_id)
            ->whereNotIn('question_id', $questionDataIds)
            ->delete();

        foreach ($questions as $data) {
            if (!isset($data['question_id'])) {
                continue;
            }

            $saveContract = SaveContractQuestion::where([
                ['saved_id', $saved_id],
                ['question_id', $data['question_id']]
            ])->first();

            if ($saveContract) {
                $saveContract->answer = $data['attempted_value'] ?? $data['attempted_answer'] ?? null;
                $saveContract->update();
                $status = 'update';
            } else {
                $saveContract = new SaveContractQuestion();
                $saveContract->question_type = $data['type'] ?? null;
                $saveContract->question_id = $data['question_id'] ?? null;
                $saveContract->answer = $data['attempted_value'] ?? $data['attempted_answer'] ?? null;
                $saveContract->question_label = $data['label'] ?? null;
                $saveContract->attempted_value = $data['attempted_value'] ?? null;
                $saveContract->prev_id = $data['previous_id'] ?? null;
                $saveContract->next_id = $data['next_id'] ?? null;
                $saveContract->progress = $data['progress'] ?? null;
                $saveContract->total_steps = $data['total_steps'] ?? null;
                $saveContract->attempted_steps = $data['attempted_step'] ?? null;
                $saveContract->saved_id = $saved_id;
                $saveContract->save();
                $status = 'add';
            }
        }

        return response()->json([
            'code' => '200',
            'status' => $status ?? 'no changes',
        ]);
    }


    public function saveContractContent(Request $request)
{
    $documentId = $request->document_id;
    $html       = $request->html;
    $userID     = $request->user_id;

    if ($userID != null) {

        $existing = ContractContent::where([
            ['document_id', $documentId],
            ['user_id', $userID],
        ])->first();

        if ($existing) {

            $existing->html = $html;
            $existing->update();

            $contractContent = $existing;

            $status = 'update';

        } else {

            $contractContent = new ContractContent;

            $contractContent->document_id = $documentId;
            $contractContent->user_id     = $userID;
            $contractContent->html        = $html;

            $contractContent->save();

            $status = 'save';
        }

        Session::put('content_id', $contractContent->id);

    }

    else {

        // session token generate only once
        if (!Session::has('contract_session_token')) {

            Session::put(
                'contract_session_token',
                Str::uuid()
            );
        }

        $sessionToken = Session::get(
            'contract_session_token'
        );

        // same guest + same document update
        $contractContent = ContractContent::updateOrCreate(

            [
                'document_id'   => $documentId,
                'session_token' => $sessionToken,
            ],

            [
                'html' => $html,
            ]
        );

        $status = $contractContent->wasRecentlyCreated
                    ? 'save'
                    : 'update';

        Session::put('content_id', $contractContent->id);
    }

    return response()->json([
        'code'   => 200,
        'status' => $status,
    ]);
}

    // public function saveContractContent(Request $request){
    //     $documentId = $request->document_id;
    //     $html = $request->html;
    //     $userID = $request->user_id;
    
    //     $processedHtml = $html;
    
    //     $answersFromHtml = [];
    //     preg_match_all('/<span[^>]*class="[^"]*answered_spns[^"]*qidtarget-(\d+)[^"]*"[^>]*>(.*?)<\/span>/is', $html, $matches);
    //     if (!empty($matches[1])) {
    //         foreach ($matches[1] as $index => $qid) {
    //             $answersFromHtml[$qid] = strip_tags($matches[2][$index]);
    //         }
    //     }
    
    //     preg_match_all('/<span[^>]*class="[^"]*qidtarget-(\d+)[^"]*answered_spns[^"]*"[^>]*>(.*?)<\/span>/is', $html, $matches2);
    //     if (!empty($matches2[1])) {
    //         foreach ($matches2[1] as $index => $qid) {
    //             if (!isset($answersFromHtml[$qid])) {
    //                 $answersFromHtml[$qid] = strip_tags($matches2[2][$index]);
    //             }
    //         }
    //     }
    
    //     if ($userID) {
    //         $savedData = SavedDataId::where('user_id', $userID)
    //             ->where('document_id', $documentId)
    //             ->first();
    
    //         if ($savedData) {
    //             $dbAnswers = SaveContractQuestion::where('saved_id', $savedData->id)
    //                 ->pluck('answer', 'question_id')
    //                 ->toArray();
    
    //             foreach ($dbAnswers as $qid => $answer) {
    //                 if (!isset($answersFromHtml[$qid]) && $answer !== null && $answer !== '') {
    //                     $answersFromHtml[$qid] = $answer;
    //                 }
    //             }
    //         }
    //     }
    
    //     if (!empty($answersFromHtml)) {
    //         $processedHtml = preg_replace_callback('/\{(\d+)\}/', function ($matches) use ($answersFromHtml) {
    //             $qid = $matches[1];
    //             return isset($answersFromHtml[$qid]) && $answersFromHtml[$qid] !== ''
    //                 ? $answersFromHtml[$qid]
    //                 : '____________';
    //         }, $processedHtml);
    //     }
    
    //     if ($userID != null) {
    //         $existing = ContractContent::where([
    //             ['document_id', $documentId],
    //             ['user_id', $userID],
    //         ])->first();
    
    //         if ($existing) {
    //             $existing->html = $processedHtml;
    //             $existing->update();
    //             $status = 'update';
    //             Session::put('content_id', $existing->id);
    //         } else {
    //             $contractContent = new ContractContent;
    //             $contractContent->document_id = $documentId;
    //             $contractContent->user_id = $userID;
    //             $contractContent->html = $processedHtml;
    //             $contractContent->save();
    //             $status = 'save';
    //             Session::put('content_id', $contractContent->id);
    //         }
    //     } else {
    //         $contractContent = new ContractContent;
    //         $contractContent->document_id = $documentId;
    //         $contractContent->html = $processedHtml;
    //         $contractContent->save();
    //         $status = 'save';
    //         Session::put('content_id', $contractContent->id);
    //     }
    
    //     return response()->json([
    //         'code' => 200,
    //         'status' => $status,
    //     ]);
    // }

    public function updateContractContent(Request $request)
    {
        // return $request->all();

        $user_id = $request->user_id;
        $editType = $request->type;
        $orderId = $request->order_id;
        $status = '';

        if (!$user_id) {
            return response()->json([
                'code' => 400,
                'status' => 'failed',
                'message' => 'User ID is required',
            ]);
        }

        if ($editType === 'full') {
            // if ($request->has_subscription && $request->has_credits) {
            //     $userCredit = UserCredit::where('user_id', $user_id)->first();

            //     if ($userCredit && $userCredit->balance > 0) {
            //         $userPlan = UserPlan::where('user_id', $user_id)->where('status', 1)->first();
            //         $amountPerCredit = 1;

            //         if ($userCredit->balance >= $amountPerCredit) {
            //             $userCredit->balance -= $amountPerCredit;
            //             $userCredit->save();

            //             CreditTransaction::create([
            //                 'plan_id' => $userPlan?->plan_id,
            //                 'document_id' => $request->document_id,
            //                 'user_id' => $user_id,
            //                 'type' => 0,
            //                 'used_amount' => $amountPerCredit,
            //                 'amount' => $userCredit->balance,
            //                 'transaction_date' => now(),
            //                 'description' => '1 credit used for full contract edit',
            //             ]);
            //         } else {
            //             return response()->json([
            //                 'code' => 400,
            //                 'status' => 'failed',
            //                 'message' => 'Insufficient credit balance',
            //             ]);
            //         }
            //     }
            // }

            // $existingContent = ContractContent::where([
            //     ['user_id', $user_id],
            //     ['document_id', $request->document_id],
            // ])->latest()->first();

            // $contractContent = new ContractContent();
            // $contractContent->user_id = $user_id;
            // $contractContent->document_id = $request->document_id;
            // $contractContent->html = $request->html;
            // $contractContent->edit_type = 'full_edit';
            // $contractContent->parent_id = $existingContent?->id;
            // $contractContent->order_id = $orderId ?? null;
            // $contractContent->status = 1;
            // $contractContent->save();

            // Session::put('content_id', $contractContent->id);
            // $status = 'save';

            if ($request->has_subscription && $request->has_credits) {
                $userCredit = UserCredit::where('user_id', $user_id)->where('document_id',$request->document_id)->first();
                $userPlan = UserPlan::where('user_id', $user_id)->where('status', 1)->first();
                $subscription = Subscription::where('order_id', $orderId)->where('status', 1)->first();
            
                if ($userCredit && $userCredit->balance > 0) {
                    // Count how many full-edit contracts the user created this month
                    $contractsThisMonth = ContractContent::where('user_id', $user_id)
                        ->where('document_id', $request->document_id)
                        ->where('edit_type', 'full_edit')
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->count();
            
                    // If still under limit, use 1 credit and create new contract
                    if ($contractsThisMonth < $userCredit->balance) {
                        $amountPerCredit = 1;
            
                        // Deduct 1 credit
                        $userCredit->balance -= $amountPerCredit;
                        $userCredit->save();
            

                        CreditTransaction::create([
                            'user_id'         => $user_id,
                            'document_id'     => $request->document_id,
                            'plan_id'         => $userPlan?->plan_id ?? null,
                            'subscription_id' => $subscription?->id ?? null,
                            'order_id'        => $orderId ?? null,
                            'type'            => 0, 
                            'used_amount'     => $amountPerCredit,
                            'amount'          => $userCredit->balance,
                            'transaction_date'=> now(),
                            'period_start'    => $subscription?->current_period_start ?? null,
                            'period_end'      => $subscription?->current_period_end ?? null,
                            'description'     => '1 credit used for full contract edit',
                        ]);
            
                        
                        $existingContent = ContractContent::where([
                            ['user_id', $user_id],
                            ['document_id', $request->document_id],
                            ['type','original']
                        ])->first();
            
                        $contractContent = new ContractContent();
                        $contractContent->user_id = $user_id;
                        $contractContent->document_id = $request->document_id;
                        $contractContent->html = $request->html;
                        $contractContent->edit_type = 'full_edit';
                        $contractContent->type = 'copied';
                        $contractContent->parent_id = $existingContent?->id;
                        $contractContent->order_id = $orderId ?? null;
                        $contractContent->status = 1;
                        $contractContent->save();
            
                    } else {
                        // If limit reached — stop
                        return response()->json([
                            'code' => 400,
                            'status' => 'failed',
                            'message' => 'You have used all your credits for this month.',
                        ]);
                    }
                } else {
                    // No credits at all
                    return response()->json([
                        'code' => 400,
                        'status' => 'failed',
                        'message' => 'No available credits found.',
                    ]);
                }
            }

        }else if ($editType === 'edit') {
            $editedContract = ContractContent::where([
                ['user_id', $user_id],
                ['document_id', $request->document_id],
                ['edit_type', 'text_only']
            ])->latest()->first();

            if($editedContract && $editedContract->edit_count >= 1){
                $contractContent = new ContractContent();
                $contractContent->user_id = $user_id;
                $contractContent->document_id = $request->document_id;
                $contractContent->html = $request->html;
                $contractContent->save();

                Session::put('content_id', $contractContent->id);

                return response()->json([
                    'code' => 302,
                    'status' => 'redirect_checkout',
                    'redirect_url' => url('/checkout'),
                    'message' => 'You have already edited this contract once. Please complete checkout to edit again.',
                ]);
            }

            $existingContent = ContractContent::where([
                ['user_id', $user_id],
                ['document_id', $request->document_id],
            ])->latest()->first();

            $contractContent = new ContractContent();
            $contractContent->user_id = $user_id;
            $contractContent->document_id = $request->document_id;
            $contractContent->html = $request->html;
            $contractContent->edit_type = 'text_only';
            $contractContent->edit_count = 1;
            $contractContent->order_id = $orderId ?? null;
            $contractContent->parent_id = $existingContent?->id;
            $contractContent->status = 1;
            $contractContent->save();

            Session::put('content_id', $contractContent->id);
            $status = 'save';
        }

        return response()->json([
            'code' => 200,
            'status' => $status,
        ]);
    }

    public function editPurchasedDocument()
    {
        $id = $_GET['id'];
        $order_id = $_GET['order_id'] ?? null;

        if ($order_id) {
            $contractContent = ContractContent::where([
                ['user_id', auth()->id()],
                ['document_id', $id],
                ['order_id', $order_id],
                ['edit_type', 'edit_text']
            ])->first();

            if (!$contractContent) {
                $contractContent = ContractContent::where([
                    ['user_id', auth()->id()],
                    ['document_id', $id],
                    ['order_id', null],
                    ['edit_type', 'edit_text']
                ])->latest()->first();
            }
        } else {
            $contractContent = ContractContent::where([
                ['user_id', auth()->id()],
                ['document_id', $id],
                ['order_id', null],
                ['edit_type', 'edit_text']
            ])->latest()->first();
        }

        if (!$contractContent) {
            return redirect()->back()->with('error', 'Contract content not found.');
        }

        $contract_html = $contractContent->html;

        return view('users.contracts.edit_contract', compact('contract_html', 'id', 'order_id'));
    }


    public function editPurchasedDocumentProcc(Request $request){
        // return $request->all();

        try{
            // $contractContent = ContractContent::where([
            //     ['user_id', $request->user_id],
            //     ['document_id', $request->document_id],
            // ])->latest()->first();

            $contractContent = ContractContent::where([
                ['user_id', $request->user_id],
                ['document_id', $request->document_id],
                ['order_id', $request->order_id],
                ['edit_type', 'edit_text']
            ])->first();

            if(!$contractContent){
                $contractContent = new ContractContent;
                $contractContent->user_id = $request->user_id;
                $contractContent->document_id = $request->document_id;
                $contractContent->html = $request->html;
                $contractContent->edit_type = $request->type;
                $contractContent->order_id = $request->order_id;
                $contractContent->status = 1;
                $contractContent->save();
            }else{
                $contractContent->html = $request->html;
                $contractContent->edit_type = $request->type;
                $contractContent->order_id = $request->order_id;
                $contractContent->status = 1;
                $contractContent->update();
            }

            $url = route('user.purchased');

            $response = [
                'code' => '200',
                'status' => true,
                'redirect_url' => $url
            ];
            return response()->json($response);

        }catch(\Exception $e){
            saveLog("Error:", "ContractController", $e->getMessage());
        }
    }
}