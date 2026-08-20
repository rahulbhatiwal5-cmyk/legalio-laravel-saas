<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContractContent;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentFaq;
use App\Models\DocumentGeneratingPrompts;
use App\Models\DocumentGenerator;
use App\Models\DocumentRightSection;
use App\Models\DocumentsField;
use App\Models\MultipleChoiceQuestionOption;
use App\Models\PartiesSectionTemplate;
use App\Models\Prompt;
use App\Models\Question;
use App\Models\QuestionCondition;
use App\Models\QuestionData;
use App\Models\QuestionType;
use App\Models\RecommendedSection;
use App\Models\SaveContractQuestion;
use App\Models\Setting;
use App\Models\StandardDocument;
use App\Models\StateSpecificClause;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class DocumentBetaController extends Controller
{
    public $gemini;
    public function __construct()
    {
        $this->gemini = new GeminiService('Gemini 2.5 pro');
    }
    public function allBetaDocuments()
    {
        $documents = Document::orderBY('created_at', 'asc')->paginate(200);
        return view('admin.documents.all_beta_documents', compact('documents'));
    }

    public function getStateClauses(Request $request)
    {
        // dd("HIU");
        
        $clauses = StateSpecificClause::where('is_active', 1)->get();
            return response()->json([
                'success' => true,
                'data' => $clauses
            ]);
        }

        public function getPartiesTemplates()
        {
            $templates = PartiesSectionTemplate::where('is_active', true)
                ->orderBy('parties_type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates
            ]);
        }

    public function betadocumentGenerator()
    {
        $document_price = Setting::where('key', 'document_price')
        ->where('status', 1)
        ->first();  

        $DocumentGenratingPrompts = DocumentGeneratingPrompts::where('contract_type', 'question')->get();
        $DocumentGeneratingContract = DocumentGeneratingPrompts::where('contract_type', 'contract')->get();

        $technicalSpecifications = Prompt::where('key', 'technical_specifications')->first();

        $partiesTemplates = PartiesSectionTemplate::where('is_active', true)
            ->orderBy('parties_type')
            ->get();
        return view('admin.documents.document_generator_beta', compact('technicalSpecifications', 'DocumentGenratingPrompts', 
        'DocumentGeneratingContract', 'partiesTemplates', 'document_price'
    ));
    }

    public function betadocumentGenerateProcess(Request $request)
{    
    $document = null;
    $slug = '';
    $aiModelRefs = [];
    $document_generator = null;
    $recommendedSection = [];
    $recommendedSectionIds = '';
    $questions = [];
    $types = [];
    $resultSections = [];
    $standardDocument = StandardDocument::where('type', 'global')->get();
    $standardDocuments = StandardDocument::where('type', 'global')->get();
    $documentId = $request->document_id;

    DB::beginTransaction();
    try {
        if (!$documentId) {
            Log::info(' Creating new document');
            
            $document = Document::create([
                'title' => $request->document_name ?? 'Untitled Document',
                'slug' => Str::slug($request->document_name ?? 'untitled-document'),
                'short_description' => $request->short_description,
                'parties_type' => $request->parties_type ?? null,
                'party_labels' => !empty($request->party_labels) 
                        ? json_encode($request->party_labels) 
                        : null,
            ]);

            
            //  Assign the created document's ID
            $documentId = $document->id;
            $slug = $document->slug;
            
            Log::info(' Document created', ['id' => $documentId, 'slug' => $slug]);
        } else {
            Log::info(' Updating existing document', ['id' => $documentId]);
            
            $document = Document::find($documentId);

            if (!$document) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            $slug = $document->slug;
        }

        $selectedState = $request->input('selected_state');
        $stateClauses = collect();

        if (!empty($selectedState)) {
            $stateClauses = StateSpecificClause::where('state', $selectedState)
                ->where('is_active', 1)
                ->get();
            
        }
        
        $aiModelRefs = Setting::where('type', 'ai')
            ->whereNotNull('model_ref')
            ->distinct()
            ->pluck('model_ref');

        $document_generator = DocumentGenerator::where('document_id', $documentId)->first();

        $qidToRealIdMap = [];

        //  Handle section names and create standard documents
        if ($request->has('questions')) {
            Log::info(' Processing questions for standard sections');
            
            $sectionNamesProcessed = [];
            
            foreach ($request->questions as $questionData) {
                if (isset($questionData['section_name']) && !empty($questionData['section_name'])) {
                    $section_name = $questionData['section_name'];
                    
                    //  Skip if already processed
                    if (in_array($section_name, $sectionNamesProcessed)) {
                        continue;
                    }
                    
                    $sectionNamesProcessed[] = $section_name;

                    $standardDocument = StandardDocument::where('title', $section_name)
                        ->where('document_id', $documentId)
                        ->first();

                    if (!$standardDocument) {
                        $standardDocument = new StandardDocument();
                        $standardDocument->title = $section_name;
                        $standardDocument->slug = Str::slug($section_name, '-');
                        $standardDocument->type = 'document';
                        $standardDocument->document_id = $documentId;
                        $standardDocument->save();
                        
                        Log::info(' Standard section created', [
                            'id' => $standardDocument->id,
                            'title' => $section_name
                        ]);
                    }
                }
            }
        }

        // Create StandardDocument records for contract section names
        if ($request->has('contract_sections')) {
            Log::info(' Processing contract sections for standard documents');
            
            $contractSectionNamesProcessed = [];
            
            foreach ($request->contract_sections as $contractSectionData) {
                if (isset($contractSectionData['section_name']) && !empty($contractSectionData['section_name'])) {
                    $section_name = $contractSectionData['section_name'];
                    
                    //  Skip if already processed
                    if (in_array($section_name, $contractSectionNamesProcessed)) {
                        continue;
                    }
                    
                    $contractSectionNamesProcessed[] = $section_name;

                    $standardDocument = StandardDocument::where('title', $section_name)
                        ->where('document_id', $documentId)
                        ->first();

                    if (!$standardDocument) {
                        $standardDocument = new StandardDocument();
                        $standardDocument->title = $section_name;
                        $standardDocument->slug = Str::slug($section_name, '-');
                        $standardDocument->type = 'document';
                        $standardDocument->document_id = $documentId;
                        $standardDocument->save();
                        
                        Log::info(' Standard section created from contract sections', [
                            'id' => $standardDocument->id,
                            'title' => $section_name
                        ]);
                    }
                }
            }
        }
        //  END

        //  Save questions and build QID mapping
        if ($request->has('questions')) {
            Log::info(' Processing and saving questions');
            
            $qidToRealIdMap = [];
            $questionsToSave = [];
            $questionDataToSave = [];

            $orderCounter = 1; // Initialize global question order counter
            
            // Step 1: Save all questions first
            foreach ($request->questions as $index => $questionData) {
                //  Find standard_section_id
                $standardSectionId = null;
                // if (!empty($questionData['section_name'])) {
                //     $standardDoc = StandardDocument::where('title', $questionData['section_name'])
                //         ->where('document_id', $documentId)
                //         ->first();
                    
                //     if ($standardDoc) {
                //         $standardSectionId = $standardDoc->id;
                //     }

                if ($index === 0) {
                    Log::info('Total questions received', [
                        'count' => count($request->questions),
                        'document_id' => $documentId,
                        'first_question_keys' => is_array($request->questions[0]) ? array_keys($request->questions[0]) : 'not array'
                    ]);
                }
                
               
                // }
                if (!is_array($questionData) || !isset($questionData['qid'])) {
                    Log::warning('Skipping invalid question data', ['index' => $index, 'type' => gettype($questionData)]);
                    continue;
                }
                
                $question = new Question();
                $question->document_id = (int)$documentId;
                $question->qid = $questionData['qid'];
                $question->type = $questionData['type'] ?? 'text';
                $question->condition_type = $questionData['condition_type'] ?? null;
                $question->is_end = $questionData['is_end'] ?? false;
                $question->standard_section_id = $standardSectionId;
                $question->order_id = $orderCounter++;
                $question->save();

                if (preg_match('/^QID(\d+)$/i', $questionData['qid'], $qidMatches)) {
                    $numericQid = $qidMatches[1];
                    $qidToRealIdMap[$numericQid] = $question->id;
                }
                $qidToRealIdMap[$questionData['qid']] = $question->id;

                $questionsToSave[] = [
                    'question' => $question,
                    'questionData' => $questionData
                ];
                
                Log::info(' Question saved', [
                    'qid' => $questionData['qid'],
                    'real_id' => $question->id,
                    'order_id' => $question->order_id,
                    'section_id' => $standardSectionId
                ]);
            }

            Log::info(' QID mapping created', ['mappings' => $qidToRealIdMap]);

            // Save QuestionData
            foreach ($questionsToSave as $questionItem) {                
                $question = $questionItem['question'];
                $questionData = $questionItem['questionData'];
                
                $questionDataModel = new QuestionData();
                $questionDataModel->question_id = $question->id;
                $questionDataModel->question_label = $questionData['text'] ?? null;
                $questionDataModel->textbox_id = $questionData['textbox_id'] ?? null;
                
                $nextQuestionId = null;
                if (isset($questionData['goto']) && $questionData['goto'] !== 'END') {
                    $gotoQid = $questionData['goto'];
                    if (isset($qidToRealIdMap[$gotoQid])) {
                        $nextQuestionId = $qidToRealIdMap[$gotoQid];
                    }
                }
                
                $questionDataModel->next_question_id = $nextQuestionId ?? null;
                $questionDataModel->same_contract_link_label = $questionData['same_contract_link_label'] ?? null;
                $questionDataModel->conditional_go_to_step = $nextQuestionId;
                $questionDataModel->text_box_placeholder = $questionData['text_box_placeholder'] ?? null;
                $questionDataModel->question_info_text = $questionData['userinfo'] ?? null;
                $questionDataModel->save();

                $questionDataToSave[] = [
                    'questionDataModel' => $questionDataModel,
                    'questionData' => $questionData
                ];
            }

            // Handle multiple choice options
            foreach ($questionDataToSave as $questionDataItem) {
                $questionDataModel = $questionDataItem['questionDataModel'];
                $questionData = $questionDataItem['questionData'];

                if (!empty($questionData['options']) && is_array($questionData['options'])) {
                    foreach ($questionData['options'] as $order => $optionData) {
                        $option = new MultipleChoiceQuestionOption();
                        $option->question_id = $questionDataModel->question_id;
                        $option->option_label = $optionData['label'] ?? '';
                        $option->option_value = $optionData['value'] ?? '';
                        $option->next_question_id = $questionDataModel->next_question_id;
                        $option->contract_link = $optionData['contract_link'] ?? null;
                        $option->contract_send_to_next_step = $optionData['contract_send_to_next_step'] ?? false;
                        $option->type = $questionData['type'] ?? null;
                        $option->order_id = $order + 1;
                        $option->save();
                    }
                }
            }
        }

        //  Handle contract sections
        if ($request->has('contract_sections')) {
            Log::info(' Processing contract sections');

            $existingSectionIds = $request->has('recomm_sections_ids')
                ? json_decode($request->recomm_sections_ids, true)
                : [];

            // Preserve original order from request - removed usort
            $contractSections = $request->contract_sections;
            
            //  VALIDATE THAT ALL SECTIONS HAVE CONTENT
            foreach ($contractSections as $index => $section) {
                if (empty($section['text']) && empty($section['content']) && $section['type'] !== 'SIGNATURE') {
                    Log::warning(" Section missing content", [
                        'index' => $index,
                        'section_name' => $section['section_name'] ?? 'Unknown',
                        'type' => $section['type'] ?? 'Unknown'
                    ]);
                    
                    //  Add placeholder content for empty sections
                    $contractSections[$index]['text'] = '[Content to be added for ' . ($section['section_name'] ?? 'this section') . ']';
                }
            }
            
            // First pass: attach standard_section_id to each section (without sorting)
            $sectionsWithIds = [];
            foreach ($contractSections as $key => $contractSectionData) {
                $standard_section_id = null;
                if (!empty($contractSectionData['section_name'])) {
                    $standardDoc = StandardDocument::where('title', $contractSectionData['section_name'])
                        ->where('document_id', $documentId)
                        ->first();
                    
                    if ($standardDoc) {
                        $standard_section_id = $standardDoc->id;
                    }
                }
                
                $sectionsWithIds[] = [
                    'data' => $contractSectionData,
                    'standard_section_id' => $standard_section_id,
                    'original_key' => $key
                ];
            }
            
            // Use global order counter instead of per-section counters
            $globalRecommendedOrderCounter = 1;
            $processedStandardSectionIds = []; //  Track processed section IDs to avoid duplicates
            
            foreach ($sectionsWithIds as $sectionItem) {
                $contractSectionData = $sectionItem['data'];
                $standard_section_id = $sectionItem['standard_section_id'];
                
                if (in_array($standard_section_id, $existingSectionIds)) {
                    continue;
                }
                
                //  Skip if this standard_section_id was already processed (avoid duplicate RecommendedSection)
                if ($standard_section_id !== null && in_array($standard_section_id, $processedStandardSectionIds)) {
                    continue;
                }
                
                if ($standard_section_id !== null) {
                    $processedStandardSectionIds[] = $standard_section_id;
                }

                $recommended = RecommendedSection::where([
                    ['document_id', $documentId],
                    ['standard_section_id', $standard_section_id],
                ])->first();

                if ($recommended) {
                    $recommended->standard_section_id = $standard_section_id;
                    $recommended->status = 1;
                    $recommended->order_id = $globalRecommendedOrderCounter++; //  Global sequential order
                    $recommended->update();
                } else {
                    $recommended = new RecommendedSection();
                    $recommended->document_id = $documentId;
                    $recommended->standard_section_id = $standard_section_id;
                    $recommended->order_id = $globalRecommendedOrderCounter++; // Global sequential order
                    $recommended->status = 1;
                    $recommended->save();
                }
                
                Log::info(' Recommended section saved', [
                    'id' => $recommended->id,
                    'standard_section_id' => $standard_section_id,
                    'order_id' => $recommended->order_id
                ]);
            }
        }

        //  Add contract content
        if ($request->has('contract_sections')) {
            Log::info(' Adding contract content');
            
            $contractSections = $request->contract_sections;
            
            //  FIXED: Attach standard_section_id without sorting
            foreach ($contractSections as $key => &$contractSection) {
                $standard_section_id = null;
                if (!empty($contractSection['section_name'])) {
                    $standardDoc = StandardDocument::where('title', $contractSection['section_name'])
                        ->where('document_id', $documentId)
                        ->first();
                    
                    if ($standardDoc) {
                        $standard_section_id = $standardDoc->id;
                    }
                }
                $contractSection['_standard_section_id'] = $standard_section_id;
                $contractSection['_original_index'] = $key; //  Preserve original index
            }
            unset($contractSection); // Break reference
            
            //  FIXED: Separate sections into categories for proper ordering
            // Clause descriptions should appear second to last (before signature_field)
            $regularSections = [];
            $clauseSections = [];
            $signatureSections = [];
            
            foreach ($contractSections as $contractSection) {
                $sectionType = strtoupper($contractSection['type'] ?? 'CONTENT');
                $sectionName = strtolower($contractSection['section_name'] ?? '');
                
                //  Identify signature sections
                if ($sectionType === 'SIGNATURE') {
                    $signatureSections[] = $contractSection;
                }
                //  Identify clause description sections (check type or section_name)
                elseif ($sectionType === 'CLAUSE' || 
                        $sectionType === 'CLAUSE_DESCRIPTION' || 
                        strpos($sectionName, 'clause') !== false ||
                        strpos($sectionName, 'state specific') !== false ||
                        strpos($sectionName, 'state-specific') !== false) {
                    $clauseSections[] = $contractSection;
                }
                //  Regular content sections
                else {
                    $regularSections[] = $contractSection;
                }
            }
            
            //  FIXED: Reorder sections - regular first, then clause descriptions, then signature last
            $orderedSections = array_merge($regularSections, $clauseSections, $signatureSections);
            
            Log::info(' Sections reordered for proper positioning', [
                'regular_count' => count($regularSections),
                'clause_count' => count($clauseSections),
                'signature_count' => count($signatureSections),
                'total' => count($orderedSections)
            ]);
            
            //  FIXED: Use single global order counter for all sections
            $globalContentOrderCounter = 1;

            foreach ($orderedSections as $contractSection) {
                $standard_section_id = $contractSection['_standard_section_id'];
                $text = $contractSection['text'] ?? '';

                // ENSURE EVERY SECTION HAS SOME CONTENT
                if (empty($text) && $contractSection['type'] !== 'SIGNATURE') {
                    $text = '[Content pending for ' . ($contractSection['section_name'] ?? 'this section') . ']';
                    Log::warning("Empty content detected, adding placeholder", [
                        'section_name' => $contractSection['section_name'] ?? 'Unknown',
                        'type' => $contractSection['type'] ?? 'Unknown'
                    ]);
                }

                // Map TYPE to appropriate database values
                $type = match (strtoupper($contractSection['type'] ?? 'CONTENT')) {
                    'HEADLINE' => 'content_heading',
                    'CONTENT' => 'content',
                    'SIGNATURE' => 'signature_field',
                    'CLAUSE', 'CLAUSE_DESCRIPTION' => 'content', //  Handle clause types as content
                    default => 'content',
                };

                // Replace QID placeholders with real question IDs
                $text = preg_replace_callback('/\{QID(\d+)(?:_[^}]*)?\}/', function ($matchesCallback) use ($qidToRealIdMap) {
                    $originalQid = $matchesCallback[1];
                    return isset($qidToRealIdMap[$originalQid]) ? '{' . $qidToRealIdMap[$originalQid] . '}' : $matchesCallback[0];
                }, $text);

                // Extract flags properly
                $secure_blur_content = isset($contractSection['blur_content']) && $contractSection['blur_content'] ? 1 : 0;
                $is_signature = ($type === 'signature_field') ? 1 : 0;
                $is_condition = (!empty($contractSection['conditions'])) ? 1 : 0;

                // Create a new DocumentRightSection entry
                $documentRightSection = new DocumentRightSection();
                $documentRightSection->type = $type;
                $documentRightSection->document_id = $documentId;
                $documentRightSection->standard_section_id = $standard_section_id;
                $documentRightSection->order_id = $globalContentOrderCounter++; //  Global sequential order
                $documentRightSection->content = $text;
                $documentRightSection->text_align = $contractSection['align_text'] ?? 'left';
                $documentRightSection->text_alignment = $contractSection['align_text'] ?? 'left';
                $documentRightSection->is_condition = $is_condition;
                $documentRightSection->signature_field = $is_signature;
                $documentRightSection->secure_blur_content = $secure_blur_content;
                $documentRightSection->content2 = $contractSection['content2'] ?? null;
                $documentRightSection->content3 = $contractSection['content3'] ?? null;
                $documentRightSection->published = 1;
                $documentRightSection->save();
                
                Log::info(' Contract section saved with global order', [
                    'id' => $documentRightSection->id,
                    'type' => $type,
                    'standard_section_id' => $standard_section_id,
                    'order_id' => $documentRightSection->order_id,
                    'has_content' => !empty($text)
                ]);

                // Handle conditions 
                if (!empty($contractSection['conditions'])) {
                    foreach ($contractSection['conditions'] as $condition) {
                        $condition = (array) $condition;

                        $checkType = match (strtolower(trim($condition['conditions'] ?? 'is equal to'))) {
                            'is equal to', 'is_equal_to' => 1,
                            'is greater than', 'is_greater_than' => 2,
                            'is less than', 'is_less_than' => 3,
                            'not equal to', 'not_equal_to' => 4,
                            default => 1,
                        };

                        // Convert QID to actual database question ID
                        $questionId = null;
                        if (!empty($condition['question_id'])) {
                            $rawQuestionId = trim($condition['question_id']);
                            
                            if (preg_match('/^QID(\d+)$/i', $rawQuestionId, $qidMatches)) {
                                $numericQid = $qidMatches[1];
                                $questionId = $qidToRealIdMap[$numericQid] ?? null;
                            } 
                            elseif (is_numeric($rawQuestionId)) {
                                $questionId = (int) $rawQuestionId;
                            }
                            else {
                                $questionId = $qidToRealIdMap[$rawQuestionId] ?? null;
                            }
                        }

                        if ($questionId !== null) {
                            $condition_type = match ($type) {
                                'content' => 'content_condition',
                                'signature_field' => 'signature_field',
                                'content_heading' => 'content_condition',
                                default => 'content_condition',
                            };

                            $documentCondition = new QuestionCondition();
                            $documentCondition->condition_type = $condition_type;
                            $documentCondition->document_right_content_id = $documentRightSection->id;
                            $documentCondition->conditional_question_id = $questionId;
                            $documentCondition->conditional_check = $checkType;
                            $documentCondition->conditional_question_value = $condition['question_value'] ?? '';
                            $documentCondition->status = 1;
                            $documentCondition->question_label = $condition['label'] ?? null;
                            $documentCondition->go_to_step = isset($condition['go_to_step']) && is_numeric($condition['go_to_step']) 
                                ? (int) $condition['go_to_step'] 
                                : null;
                            $documentCondition->save();
                        } else {
                            Log::warning(" Could not resolve question ID for condition", [
                                'condition_question_id' => $condition['question_id'] ?? null,
                                'document_id' => $documentId,
                                'section_id' => $documentRightSection->id,
                            ]);
                        }
                    }
                }
            }
        }

        //  Fetch data for response
        $recommendedSection = RecommendedSection::where('document_id', $documentId)
            ->where('status', 1)
            ->with('standard_section')
            ->orderBy('order_id', 'asc') //  Order by order_id only
            ->get();
        $recommendedSectionIds = $recommendedSection->pluck('standard_section_id')->map(fn($id) => (string) $id);

        $questions = Question::where('document_id', $documentId)
            ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
            ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
            ->get();

        //  FIXED: Order by order_id only (globally sequential now)
        $sections = DocumentRightSection::where('document_id', $documentId)
            ->with('conditions')
            ->orderBy('order_id', 'asc') //  Only order by order_id, not standard_section_id
            ->get();
            
        // VALIDATE ALL SECTIONS HAVE CONTENT BEFORE COMMITTING
        $emptySections = $sections->filter(function($section) {
            return empty($section->content) && $section->type !== 'signature_field';
        });
        
        if ($emptySections->count() > 0) {
            Log::warning("Found empty sections before commit", [
                'count' => $emptySections->count(),
                'section_ids' => $emptySections->pluck('id')->toArray()
            ]);
        }

        if ($request->has('article_sections') && is_array($request->input('article_sections'))) {
            DocumentsField::where('document_id', $documentId)->delete();
            $articleCount = 0;
        
            foreach ($request->input('article_sections') as $section) {
                if (!empty($section['title']) || !empty($section['content'])) {
                    $field = new DocumentsField();
                    $field->document_id = $documentId;
                    $field->heading = $section['title'] ?? null;
                    $field->description = $section['content'] ?? null;
                    $field->save();
                    $articleCount++;
                }
            }
        }
        
        if ($request->has('faqs') && is_array($request->input('faqs'))) {
            DocumentFaq::where('document_id', $documentId)->delete();
            $faqCount = 0;
        
            foreach ($request->input('faqs') as $faq) {
                if (!empty($faq['question']) || !empty($faq['answer'])) {
                    $documentFaq = new DocumentFaq();
                    $documentFaq->document_id = $documentId;
                    $documentFaq->question = $faq['question'] ?? null;
                    $documentFaq->answer = $faq['answer'] ?? null;
                    $documentFaq->save();
                    $faqCount++;
                }
            }
        }

        $htmlSections = DocumentRightSection::where('document_id', $documentId)
        ->orderBy('order_id', 'asc')
        ->get();

        $saveOrderId = $request->input('order_id') ?? null;

        $rawHtmlContent = '';
        foreach ($htmlSections as $sec) {
            $content = $sec->content ?? ''; 

            if ($sec->type === 'content_heading') {
                $rawHtmlContent .= '<h2 style="margin-top:20px;">' . $content . '</h2>';
            } elseif ($sec->type === 'signature_field') {
                $rawHtmlContent .= '<div style="margin-top:40px;border-top:1px solid #000;width:300px;">'
                                . $content . '</div>';
            } else {
                $rawHtmlContent .= $content;
            }
        }

        $existingContract = ContractContent::where([
            ['user_id',      auth()->id()],
            ['document_id',  $documentId],
            ['order_id',     $saveOrderId],
        ])->first();

        if ($existingContract) {
            $existingContract->html   = $rawHtmlContent;
            $existingContract->status = 1;
            $existingContract->save();
        } else {
            $contractContentRecord              = new ContractContent();
            $contractContentRecord->user_id     = auth()->id();
            $contractContentRecord->document_id = $documentId;
            $contractContentRecord->order_id    = $saveOrderId;
            $contractContentRecord->html        = $rawHtmlContent;
            $contractContentRecord->status      = 1;
            $contractContentRecord->save();
        }

        // dd($request->all());
        DB::commit();
        
        $documentId = (int) $documentId;

        Log::info(' Transaction committed successfully');
        Log::info(' Sending response to frontend', [
            'document_id' => $documentId,
            'document_id_type' => gettype($documentId),
            'slug' => $slug
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document saved successfully',
                'document_id' => $documentId,
                'slug' => $slug
            ]);
        }

        // Return view for non-AJAX requests
        $types = QuestionType::all();
        $standardDocuments = StandardDocument::where('document_id', $documentId)->get();
        
        return view('admin.documents.document_generator', compact(
            'document',
            'aiModelRefs',
            'document_generator',
            'slug',
            'standardDocument',
            'recommendedSection',
            'questions',
            'types',
            'recommendedSectionIds',
            'resultSections',
            'standardDocuments',
            'stateClauses',
            'selectedState'
        ));
    } catch (\Exception $e) {
        DB::rollBack();

        //  Detailed error logging
        Log::error(" Error in betadocumentGenerateProcess", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'document_id' => $documentId ?? null,
            'request_data' => [
                'document_name' => $request->document_name,
                'has_questions' => $request->has('questions'),
                'has_contract_sections' => $request->has('contract_sections'),
            ]
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the document: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? [
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile()),
                    'trace' => collect($e->getTrace())->take(3)->toArray()
                ] : null
            ], 500);
        }

        return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}

    public function questionnaireStep(Request $request)
    {
        $request->validate([
            'step' => 'required|integer|min:1',
            'document_name' => 'required|string|max:255',
            'prompt' => 'required|string',
            'previous_outputs' => 'nullable|array',
        ]);

        try {
            $technicalSpecifications = Prompt::where('key', 'technical_specifications')
                ->value('original_prompt') ?? '';

            $previousContext = '';

            if ($request->step > 1 && is_array($request->previous_outputs)) {
                $previousContext .= "\n\nPREVIOUS STEPS OUTPUT:\n";

                foreach ($request->previous_outputs as $output) {
                    if (!is_array($output)) {
                        continue;
                    }

                    $step = $output['step'] ?? 'Unknown';
                    $content = $output['output'] ?? '';

                    if ($content === '') {
                        continue;
                    }

                    $previousContext .= "Step {$step}:\n{$content}\n\n";
                }
            }

            $finalPrompt = $this->buildQuestionnairePrompt(
                $request->document_name,
                $technicalSpecifications,
                $previousContext,
                $request->prompt,
                $request->step,
                $request->total_steps
            );

            $aiResponse = $this->gemini->generateWithGemini($finalPrompt);

            if (!$aiResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI failed to generate questionnaire'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'output' => $aiResponse,
                'step' => $request->step,
            ]);
        } catch (\Throwable $e) {
            Log::error('Questionnaire generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Process contract generation step
     */
    public function contractStep(Request $request)
    {
        $request->validate([
            'step' => 'required|integer|min:1',
            // 'total_steps' => 'required|integer|min:1',
            'document_name' => 'required|string|max:255',
            'prompt' => 'required|string',
            'questionnaire' => 'required|array',
            'previous_outputs' => 'nullable|array',
        ]);

        try {
            $technicalSpecifications = Prompt::where('key', 'technical_specifications')
                ->value('original_prompt') ?? '';

            // Build context from previous contract outputs
            $previousContext = '';
            if ($request->previous_outputs && count($request->previous_outputs) > 0) {
                $previousContext = "\n\nPREVIOUS CONTRACT SECTIONS:\n";
                foreach ($request->previous_outputs as $output) {
                    if (!is_array($output)) {
                        continue;
                    }
                    $step = $output['step'] ?? 'Unknown';
                    $content = $output['output'] ?? '';
                    
                    $previousContext .= "Section {$step}: {$content}\n\n";
                    // $previousContext .= "Section {$output['step']}: {$output['output']}\n\n";
                }
            }

            // Format questionnaire for AI
            // $questionnaireContext = $this->formatQuestionnaireForAI($request->questionnaire);
            $questionnaireContext = $request->questionnaire;

            // ENHANCED PROMPT to ensure all sections get content
            $finalPrompt = $this->buildContractPrompt(
                $request->document_name,
                $technicalSpecifications,
                $questionnaireContext,
                $previousContext,
                $request->prompt,
                $request->step
            );

            // Call AI (Gemini)
            $aiResponse = $this->gemini->generateWithGemini($finalPrompt);


            if (!$aiResponse) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI failed to generate contract'
                ], 500);
            }

            // VALIDATE that AI response contains actual content
            $cleanedOutput = trim($aiResponse);
            if (empty($cleanedOutput) || strlen($cleanedOutput) < 50) {
                Log::warning("AI generated insufficient content for step {$request->step}");
                
                // Retry once with more explicit prompt
                $retryPrompt = $finalPrompt . "\n\nIMPORTANT: You must generate substantial legal content for this section. Do not leave it empty or provide only placeholders.";
                $aiResponse = $this->gemini->generateWithGemini($retryPrompt);
                $cleanedOutput = trim($aiResponse);
            }

            return response()->json([
                'success' => true,
                'output' => $cleanedOutput,
                'step' => $request->step,
                // 'total_steps' => $request->total_steps
            ]);
        } catch (\Throwable $e) {
            Log::error('Contract generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Apply AI edits to contract/questionnaire
     */
    public function applyEdit(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:contract,questionnaire,both',
            'instruction' => 'required|string',
            'questionnaire' => 'nullable|array',
            'contract' => 'nullable|string',
        ]);

        try {
            $result = [];

            if ($request->mode === 'contract' || $request->mode === 'both') {
                $editPrompt = "You are editing a legal contract document.\n\n";
                $editPrompt .= "CURRENT CONTRACT:\n{$request->contract}\n\n";
                $editPrompt .= "EDITING INSTRUCTION: {$request->instruction}\n\n";
                $editPrompt .= "Please apply the requested changes and return the complete edited contract. ";
                $editPrompt .= "Maintain professional legal language and proper formatting.";

                $editedContract = $this->gemini->generateWithGemini($editPrompt);

                $result['contract'] = $editedContract;
            }

            if ($request->mode === 'questionnaire' || $request->mode === 'both') {
                // $questionnaireContext = $this->formatQuestionnaireForAI($request->questionnaire);
                $questionnaireContext = $request->questionnaire;

                $editPrompt = "You are editing a questionnaire for a legal document.\n\n";
                $editPrompt .= "CURRENT QUESTIONNAIRE:\n{$questionnaireContext}\n\n";
                $editPrompt .= "EDITING INSTRUCTION: {$request->instruction}\n\n";
                $editPrompt .= "Please apply the changes and return the questionnaire in the same JSON format. ";
                $editPrompt .= "Each question should have: text, type, options (if applicable), and required status.";

                $editedQuestionnaire = $this->gemini->generateWithGemini($editPrompt);

                // Try to parse as JSON
                $parsed = json_decode($editedQuestionnaire, true);
                if ($parsed) {
                    $result['questionnaire'] = $parsed;
                } else {
                    $result['questionnaire'] = $request->questionnaire;
                }
            }

            return response()->json([
                'success' => true,
                'contract' => $result['contract'] ?? null,
                'questionnaire' => $result['questionnaire'] ?? null,
                'output' => $result['contract'] ?? $result['questionnaire'] ?? ''
            ]);
        } catch (\Exception $e) {
            Log::error('Edit application error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save final document
     */
    public function saveFinalDocument(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'questionnaire' => 'required|array',
            'contract' => 'required|string',
            'final_document' => 'required|string',
        ]);

        try {
            // $document = GeneratedDocument::create([
            //     'document_name' => $request->document_name,
            //     'questionnaire' => json_encode($request->questionnaire),
            //     'contract_text' => $request->contract,
            //     'final_document' => $request->final_document,
            //     'created_by' => auth()->id(),
            //     'status' => 'completed'
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Document saved successfully',
                // 'document_id' => $document->id
            ]);
        } catch (\Exception $e) {
            Log::error('Document save error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Build questionnaire generation prompt
     */
    private function buildQuestionnairePrompt($documentName, $technicalSpecs, $previousContext, $userPrompt, $currentStep, $totalSteps)
    {
        $prompt = "TASK: Generate questions for a legal document questionnaire\n\n";
        $prompt .= "DOCUMENT TYPE: {$documentName}\n\n";

        if ($technicalSpecs) {
            $prompt .= "TECHNICAL SPECIFICATIONS:\n{$technicalSpecs}\n\n";
        }

        $prompt .= "GENERATION PROGRESS: Step {$currentStep} of {$totalSteps}\n\n";

        if ($previousContext) {
            $prompt .= $previousContext;
        }

        $prompt .= "INSTRUCTIONS FOR THIS STEP:\n{$userPrompt}\n\n";
        $prompt .= "Please generate clear, specific questions for this step. ";
        $prompt .= "Format each question on a new line, numbered sequentially. ";
        $prompt .= "Focus only on questions relevant to the current step instructions. ";
        $prompt .= "Make questions clear and easy to understand for users filling out the form.";

        return $prompt;
    }

    /**  increase Build contract generation prompt - ensuring all sections get content */

    private function buildContractPrompt(
        $documentName,
        $technicalSpecs,
        array $questionnaireContext,
        $previousContext,
        $userPrompt,
        $currentStep
    ) {
        $totalSteps = 1;
        
        try {
            $sections = [];
            foreach ($questionnaireContext as $question) {
                if (isset($question['section_name']) && !empty($question['section_name'])) {
                    $sections[] = $question['section_name'];
                }
            }
            
            $uniqueSections = array_unique($sections);
            if (count($uniqueSections) > 0) {
                $totalSteps = count($uniqueSections);
            } else {
                $questionCount = count($questionnaireContext);
                if ($questionCount > 10) {
                    $totalSteps = (int) ceil($questionCount / 5);
                } elseif ($questionCount > 5) {
                    $totalSteps = 2;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not calculate total steps, using default', ['error' => $e->getMessage()]);
            $totalSteps = 1;
        }
    
        //  Safe question slice calculation
        $stepQuestions = $this->getQuestionnaireSliceForStep(
            $questionnaireContext,
            $currentStep,
            $totalSteps
        );
    
        $formattedQuestions = $this->formatQuestionnaireSliceForAI($stepQuestions);
    
        // Increase PROMPT - More explicit requirements
        $prompt = "TASK: Generate a COMPLETE section of a legal contract with actual legal content\n\n";
        $prompt .= "DOCUMENT TYPE: {$documentName}\n\n";
    
        if ($technicalSpecs) {
            $prompt .= "TECHNICAL SPECIFICATIONS:\n{$technicalSpecs}\n\n";
        }
    
        $prompt .= "GENERATION PROGRESS: Step {$currentStep} of {$totalSteps}\n\n";
    
        if ($previousContext) {
            $prompt .= "PREVIOUSLY GENERATED SECTIONS:\n{$previousContext}\n\n";
        }
    
        $prompt .= "QUESTIONNAIRE (ONLY FOR THIS STEP):\n";
        $prompt .= $formattedQuestions . "\n";
    
        $prompt .= "INSTRUCTIONS FOR THIS SECTION:\n{$userPrompt}\n\n";
    
        // $prompt .= "CRITICAL REQUIREMENTS:\n";
        $prompt .= "CONTRACT GENERATION RULES:\n";
        $prompt .= "OUTPUT FORMAT REQUIREMENT:\n";
        $prompt .= "The VERY FIRST TID in your JSON output for Step 1 MUST be a HEADLINE type with TEXT = \"{$documentName}\".\n";
        $prompt .= "Example: \"TID1\": { \"section_name\": \"Title\", \"TYPE\": \"HEADLINE\", \"TEXT\": \"{$documentName}\", \"ALIGN_TEXT\": \"center\" }\n\n";
        $prompt .= "1. Generate ACTUAL legal contract content, NOT placeholders or templates\n";
        $prompt .= "2. Each section MUST contain substantial text (minimum 100 words)\n";
        $prompt .= "3. Use question placeholders like {QID1}, {QID2} for dynamic values\n";
        $prompt .= "4. DO NOT leave any section empty or with just headers\n";
        $prompt .= "5. DO NOT repeat content from previous sections\n";
        $prompt .= "6. Maintain professional legal drafting standards\n";
        $prompt .= "7. Each section must be COMPLETE and READY to use\n";
        $prompt .= "8. If this section has subsections, generate content for ALL subsections\n\n";
        
        $prompt .= "IMPORTANT: You must generate complete, professional legal language for this entire section. ";
        $prompt .= "Do not use placeholders like '[Content to be added]' or '[Insert details here]'. ";
        $prompt .= "Write actual contract language that is legally sound and comprehensive.\n\n";
    
        return $prompt;
    }

    // private function buildContractPrompt(
    //     $documentName,
    //     $technicalSpecs,
    //     array $questionnaireContext,
    //     $previousContext,
    //     $userPrompt,
    //     $currentStep
    // ) {
    //     $totalSteps = 1;
        
    //     try {
    //         $sections = [];
    //         foreach ($questionnaireContext as $question) {
    //             if (isset($question['section_name']) && !empty($question['section_name'])) {
    //                 $sections[] = $question['section_name'];
    //             }
    //         }
            
    //         $uniqueSections = array_unique($sections);
    //         if (count($uniqueSections) > 0) {
    //             $totalSteps = count($uniqueSections);
    //         } else {
    //             $questionCount = count($questionnaireContext);
    //             if ($questionCount > 10) {
    //                 $totalSteps = (int) ceil($questionCount / 5);
    //             } elseif ($questionCount > 5) {
    //                 $totalSteps = 2;
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         Log::warning('Could not calculate total steps, using default', ['error' => $e->getMessage()]);
    //         $totalSteps = 1;
    //     }
    
    //     $stepQuestions = $this->getQuestionnaireSliceForStep(
    //         $questionnaireContext,
    //         $currentStep,
    //         $totalSteps
    //     );
    
    //     $formattedQuestions = $this->formatQuestionnaireSliceForAI($stepQuestions);
    //     $safeDocumentName = addslashes($documentName);
    
    //     $prompt = "TASK: Generate a COMPLETE section of a legal contract with actual legal content\n\n";
    //     $prompt .= "DOCUMENT TITLE (MANDATORY - NEVER CHANGE THIS): \"{$documentName}\"\n";
    //     $prompt .= "CRITICAL TITLE RULE:\n";
    //     $prompt .= "The EXACT document name is: \"{$documentName}\"\n";
    //     $prompt .= "- You MUST use this exact title wherever the document name appears in the contract.\n";
    //     $prompt .= "- Do NOT rename, rephrase, shorten, translate, or alter this title in ANY way.\n";
    //     $prompt .= "- Do NOT substitute with synonyms, abbreviations, or your own interpretation.\n";
    //     $prompt .= "- If you generate a title heading at the top, it MUST be exactly: \"{$documentName}\"\n\n";
    
    //     if ($technicalSpecs) {
    //         $prompt .= "TECHNICAL SPECIFICATIONS:\n{$technicalSpecs}\n\n";
    //     }
    
    //     $prompt .= "GENERATION PROGRESS: Step {$currentStep} of {$totalSteps}\n\n";
    //     if ($previousContext) {
    //         $prompt .= "PREVIOUSLY GENERATED SECTIONS:\n{$previousContext}\n\n";
    //     }
    
    //     $prompt .= "QUESTIONNAIRE (FOR THIS STEP):\n";
    //     $prompt .= $formattedQuestions . "\n";
    //     $prompt .= "INSTRUCTIONS FOR THIS SECTION:\n{$userPrompt}\n\n";
    
    //     // $prompt .= "CONTRACT GENERATION RULES:\n";
    //     $prompt .= "OUTPUT FORMAT REQUIREMENT:\n";
    //     $prompt .= "The VERY FIRST TID in your JSON output for Step 1 MUST be a HEADLINE type with TEXT = \"{$documentName}\".\n";
    //     $prompt .= "Example: \"TID1\": { \"section_name\": \"Title\", \"TYPE\": \"HEADLINE\", \"TEXT\": \"{$documentName}\", \"ALIGN_TEXT\": \"center\" }\n\n";
    //     $prompt .= "1. TITLE LOCK: The document title is \"{$documentName}\" — this is FINAL and IMMUTABLE. Never change, rephrase, translate, or omit it.\n";
    //     $prompt .= "2. If you output a heading at the top of the contract, it MUST be exactly \"{$documentName}\" — no variation allowed.\n";
    //     $prompt .= "3. Generate ACTUAL legal contract content, NOT placeholders or templates\n";
    //     $prompt .= "4. Each section MUST contain substantial text (minimum 100 words)\n";
    //     $prompt .= "5. Use question placeholders like {QID1}, {QID2} for dynamic values\n";
    //     $prompt .= "6. DO NOT leave any section empty or with just headers (important)\n";
    //     $prompt .= "7. DO NOT repeat content from previous sections\n";
    //     $prompt .= "8. Maintain professional legal drafting standards\n";
    //     $prompt .= "9. Each section must be COMPLETE and READY to use\n";
    //     $prompt .= "10. If this section has subsections, generate content for ALL subsections\n\n";

    //     $prompt .= "FINAL REMINDER: Document title = \"{$documentName}\" — use this EXACTLY, no changes allowed.\n\n";
        
    //     $prompt .= "IMPORTANT: You must generate complete, professional legal language for this entire section. ";
    //     $prompt .= "Do not use placeholders like '[Content to be added]' or '[Insert details here]'. ";
    //     $prompt .= "Write actual contract language that is legally sound and comprehensive.\n";
    //     $prompt .= "REMINDER: The document name is \"{$documentName}\" — use it exactly as written.\n\n";
    
    //     return $prompt;
    // }

    private function getQuestionnaireSliceForStep(
        array $questionnaire,
        int $currentStep,
        int $totalSteps
    ): array {
        $totalQuestions = count($questionnaire);
    
        if ($totalQuestions === 0) {
            return [];
        }
        
        if ($totalSteps === 0) {
            return $questionnaire; // Return all if totalSteps is invalid
        }
        
        if ($currentStep > $totalSteps) {
            return []; // No questions for steps beyond total
        }
    
        // Calculate questions per step (minimum 1)
        $perStep = (int) ceil($totalQuestions / $totalSteps);
    
        $offset = ($currentStep - 1) * $perStep;
    
        if ($offset >= $totalQuestions) {
            return [];
        }
    
        return array_slice($questionnaire, $offset, $perStep);
    }
    
// all AI prompts live here
private function getFieldPrompt(string $fieldType, array $context = []): string
{
    $docName      = $context['document_name'] ?? 'this legal document';
    $sectionNames = implode(', ', $context['section_names'] ?? []) ?: 'standard legal clauses';
    $qCount       = $context['questions_count'] ?? 0;
    $sectionHint  = $context['sectionHint'] ?? 'general information';
    $articleTitle = $context['articleTitle'] ?? $docName;
    $faqIndex     = $context['faqIndex'] ?? 1;
    $question     = $context['question'] ?? 'What is this document?';
    $contractName = $context['contract_name'] ?? $docName;
    $clauseList   = $context['clause_list'] ?? '';

    return match ($fieldType) {
        'short_description' =>
            "Write a concise short description (5-6 sentences, max 300 characters) for a legal document titled \"{$docName}\".\n" .
            "It covers: {$sectionNames}.\n" .
            "Return ONLY the description text.",

        'meta_title' =>
            "Write an SEO meta title for a legal document titled \"{$docName}\".\n" .
            "Max 60 characters. Include the document type. Return ONLY the meta title.",

        'meta_description' =>
            "Write an SEO meta description for a legal document titled \"{$docName}\".\n" .
            "It covers: {$sectionNames}.\n" .
            "Max 155 characters. Return ONLY the description.",

        'primary_keywords' =>
            "Generate the single most important SEO keyword phrase for a legal document titled \"{$docName}\".\n" .
            "Return ONLY the keyword phrase, no explanation. Max 5 words.",

        'secondary_keywords' =>
            "Generate 15-18 secondary SEO keyword phrases for a legal document titled \"{$docName}\".\n" .
            "Return ONLY a comma-separated list of keywords, nothing else.",

        'article_title' =>
            "Generate a clear, informative article section title for a legal document titled \"{$docName}\".\n" .
            "Context: {$sectionHint}.\n" .
            "Return ONLY the title text. Max 60 characters.",

        'article_content' =>
            "Write a helpful informational paragraph (7-8 sentences) for a legal document article section titled \"{$articleTitle}\".\n" .
            "Document: \"{$docName}\". Make it practical and informative for someone needing this document.\n" .
            "Return ONLY the paragraph text.",

        'faq_question' =>
            "Generate a frequently asked question about the legal document titled \"{$docName}\".\n" .
            "Context index: {$faqIndex}. Make it practical and specific.\n" .
            "Return ONLY the question text.",

        'faq_answer' =>
            "Write a clear, helpful answer (4-5 sentences) to this FAQ about \"{$docName}\":\n" .
            "Question: \"{$question}\"\n" .
            "Return ONLY the answer text.",

        'fill_all' =>
            "You are filling metadata for a legal document. Return a JSON object with these exact keys:\n" .
            "{\n" .
            "  \"short_description\": \"5-6 sentence description (max 300 chars)\",\n" .
            "  \"meta_title\": \"SEO meta title (max 60 chars)\",\n" .
            "  \"meta_description\": \"SEO meta description (max 155 chars)\",\n" .
            "  \"primary_keywords\": \"single primary keyword phrase\",\n" .
            "  \"secondary_keywords\": \"comma-separated list of 15-18 keywords\"\n" .
            "}\n\n" .
            "Document name: \"{$docName}\"\n" .
            "Sections covered: {$sectionNames}\n" .
            "Number of questions: {$qCount}\n\n" .
            "Return ONLY the JSON object, no markdown, no explanation.",

        //  Step 1 clause analysis 
        // 'clause_analysis' =>
        //     "You are a strict legal expert. A user is creating: \"{$contractName}\".\n\n" .
        //     "Your job: from the list below, return ONLY clause IDs that are ESSENTIAL and SPECIFIC to a \"{$contractName}\".\n\n" .
        //     "RULES (READ CAREFULLY):\n" .
        //     "- Be VERY SELECTIVE. A typical contract needs only 15-18 clauses maximum.\n" .
        //     "- ONLY include clauses whose title directly matches the contract type \"{$contractName}\".\n" .
        //     "- Generic titles like \"Title & Preamble\", \" Date\", \"Governing Law\" — EXCLUDE unless specific to this exact contract.\n" .
        //     "- If a clause could belong to ANY contract type, EXCLUDE it.\n" .
        //     "- If a clause title contains words directly related to \"{$contractName}\" subject matter, INCLUDE it.\n" .
        //     "- Do NOT include Parties or Signature clauses.\n" .
        //     "- Return ONLY a JSON array of matching IDs: [3, 7, 12]\n" .
        //     "- If fewer than 5 match strictly, return only those that truly match.\n" .
        //     "- NO explanation, NO markdown, ONLY the JSON array.\n\n" .
        //     "Clauses:\n{$clauseList}",


        'generate_clauses' =>
            "You are a legal expert. A user is creating a contract named \"{$contractName}\".\n\n" .
            "No matching standard clauses were found in our clause library for this contract type.\n\n" .
            "Your task: generate 15–18 essential legal clauses that MUST appear in a \"{$contractName}\".\n\n" .
            "RULES:\n" .
            "- Each clause must be specific and relevant to \"{$contractName}\".\n" .
            "- Do NOT include generic clauses that belong in every contract (e.g. Governing Law, Signatures).\n" .
            "- Return ONLY a JSON array of objects. Each object must have exactly two keys:\n" .
            "  { \"title\": \"Clause Name\", \"description\": \"One or two sentence summary of what this clause covers.\" }\n" .
            "- No markdown, no explanation, ONLY the JSON array.\n\n" .
            "Example output format:\n" .
            "[\n" .
            "  { \"title\": \"Scope of Services\", \"description\": \"Defines the exact deliverables the service provider must supply.\" },\n" .
            "  { \"title\": \"Payment Schedule\", \"description\": \"Specifies milestone-based payment dates and amounts.\" }\n" .
            "]\n\n" .
            "Generate clauses for: \"{$contractName}\"",

        default => throw new \InvalidArgumentException("Unknown field_type: {$fieldType}"),
    };
}

public function aiAutofill(Request $request)
{
    $request->validate([
        'field_type' => 'required|string',
        'prompt'     => 'nullable|string|max:8000', 
        'context'    => 'nullable|array',
    ]);

    try {
        $prompt = $request->filled('prompt')
            ? $request->prompt
            : $this->getFieldPrompt($request->field_type, $request->context ?? []);

        $aiResponse = $this->gemini->generateWithGemini($prompt);

        if (!$aiResponse) {
            return response()->json(['success' => false, 'message' => 'AI failed to generate content'], 500);
        }

        $content = trim($aiResponse);
        $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $content = trim($content);

        return response()->json([
            'success'    => true,
            'content'    => $content,
            'field_type' => $request->field_type,
        ]);

    } catch (\InvalidArgumentException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (\Throwable $e) {
        Log::error('AI Autofill error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    private function formatQuestionnaireSliceForAI(array $questions): string
    {
        if (empty($questions)) {
            return "No questions available for this step.\n";
        }
    
        $text = "";
    
        foreach ($questions as $q) {
            $qid = $q['qid'] ?? 'Unknown';
            $questionText = $q['text'] ?? 'No question text';
            $type = $q['type'] ?? 'text';
            $required = isset($q['required']) ? ($q['required'] ? 'Yes' : 'No') : 'No';
            
            $text .= "QID: {$qid}\n";
            $text .= "Question: {$questionText}\n";
            $text .= "Type: {$type}\n";
            $text .= "Required: {$required}\n";
    
            if (!empty($q['options']) && is_array($q['options'])) {
                $options = array_map(function($o) {
                    if (is_string($o)) {
                        return $o;
                    }
                    return $o['label'] ?? $o['value'] ?? 'Unknown';
                }, $q['options']);
                $text .= "Options: " . implode(', ', $options) . "\n";
            }
    
            if (!empty($q['section_name'])) {
                $text .= "Section: {$q['section_name']}\n";
            }
    
            if (!empty($q['userinfo'])) {
                $text .= "User Info: {$q['userinfo']}\n";
            }
    
            $text .= str_repeat('-', 40) . "\n";
        }
            return $text;
    }


    /**
     * Format questionnaire for AI context
     */
    private function formatQuestionnaireForAI($questionnaire)
    {
        $formatted = "";
        foreach ($questionnaire as $index => $question) {
            $formatted .= ($index + 1) . ". {$question['text']}\n";
            $formatted .= "   Type: {$question['type']}\n";
            $formatted .= "   Required: " . ($question['required'] ? 'Yes' : 'No') . "\n";

            if (isset($question['options']) && count($question['options']) > 0) {
                $formatted .= "   Options: " . implode(', ', $question['options']) . "\n";
            }

            $formatted .= "\n";
        }
        return $formatted;
    }

    public function editDocument($id, Request $request)
    {
        try {
            $document = Document::find($id);

            if (!$document) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Document not found'
                    ], 404);
                }

                return redirect()->route('admin.dashboard.documents.beta')
                    ->with('error', 'Document not found');
            }

            $document->questions = json_decode($document->questions, true) ?? [];
            $document->contract_sections = json_decode($document->contract_sections, true) ?? [];
            $document->article_section = json_decode($document->article_section, true) ?? [];
            $document->faq = json_decode($document->faq, true) ?? [];

            if ($request->ajax()) {
                return view('admin_dashboard.documents.partials.edit_form', compact('document'));
            }
            return view('admin_dashboard.documents.document_generator_beta', compact('document'));

        } catch (\Exception $e) {
            Log::error(' Edit Document Error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading document: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.dashboard.documents.beta')
                ->with('error', 'Error loading document');
        }
    }

    public function updateDocument(Request $request)
{
    try {
        $documentId = $request->input('id') ?? $request->input('document_id');

        if (!$documentId) {
            return response()->json(['success' => false, 'message' => 'Document ID is required'], 400);
        }

        $document = Document::find($documentId);
        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        if ($request->filled('document_name')) $document->title = $request->input('document_name');
        if ($request->filled('slug')) $document->slug = $request->input('slug');
        if ($request->filled('short_description')) $document->short_description = $request->input('short_description');
        if ($request->filled('primary_keywords')) $document->primary_keyword = $request->input('primary_keywords');
        if ($request->filled('secondary_keywords')) $document->secondary_keyword = $request->input('secondary_keywords');
        if ($request->filled('meta_title')) $document->meta_title = $request->input('meta_title');
        if ($request->filled('meta_description')) $document->meta_description = $request->input('meta_description');

        // ADD: Handle article sections
        // if ($request->has('article_sections') && is_array($request->input('article_sections'))) {
        //     // Delete existing FAQs and article fields for clean re-save
        //     DocumentsField::where('document_id', $documentId)->delete();

        //     foreach ($request->input('article_sections') as $section) {
        //         if (!empty($section['title']) || !empty($section['content'])) {
        //             $field = new DocumentsField();
        //             $field->document_id = $documentId;
        //             $field->heading = $section['title'] ?? null;
        //             $field->description = $section['content'] ?? null;
        //             $field->save();
        //         }
        //     }
        // }

        // // ADD: Handle FAQs
        // if ($request->has('faqs') && is_array($request->input('faqs'))) {
        //     DocumentFaq::where('document_id', $documentId)->delete();

        //     foreach ($request->input('faqs') as $faq) {
        //         if (!empty($faq['question']) || !empty($faq['answer'])) {
        //             $documentFaq = new DocumentFaq();
        //             $documentFaq->document_id = $documentId;
        //             $documentFaq->question = $faq['question'] ?? null;
        //             $documentFaq->answer = $faq['answer'] ?? null;
        //             $documentFaq->save();
        //         }
        //     }
        // }

        if ($request->has('published')) {
            $document->published = $request->input('published') === '1' || $request->input('published') === 1;
        }

        $isFinalized = $request->input('finalize') === '1';
        if ($isFinalized) {
            $document->status = 'published';
            $document->finalized_at = now();
        }

        $document->parties_type = $request->input('parties_type', $document->parties_type);
        if ($request->has('party_labels')) {
            $document->party_labels = json_encode($request->input('party_labels'));
        }

        $document->parties_type = $request->input('parties_type') ?: $document->parties_type;

        if ($request->has('party_labels') && !empty($request->input('party_labels'))) {
            $document->party_labels = json_encode($request->input('party_labels'));
        }

        $document->save();

        return response()->json([
            'success' => true,
            'message' => $isFinalized ? 'Document finalized successfully' : 'Document updated successfully',
            'document_id' => $document->id
        ]);

    } catch (\Exception $e) {
        Log::error('Update Document Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error updating document: ' . $e->getMessage()], 500);
    }
}
}