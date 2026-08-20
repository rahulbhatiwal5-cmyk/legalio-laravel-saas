<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DocumentFormRequest;
use App\Models\DocumentCategory;
use App\Models\Document;
use App\Models\DocumentFaq;
use App\Models\DocumentGuide;
use App\Models\DocumentsField;
use App\Models\DocumentRelated;
use App\Models\Review;
use App\Models\DocumentAgreement;
use App\Models\DocumentWithCategory;
use App\Models\Media;
use App\Models\Question;
use App\Models\QuestionType;
use App\Models\DocumentRightSection;
use App\Models\RightSectionCondition;
use App\Models\QuestionCondition;
use App\Models\SubCondition;
use App\Models\QuestionData;
use App\Models\MultipleChoiceQuestionOption;
use App\Models\GeneralSection;
use App\Models\ArticleSection;
use App\Models\PromptAttach;
use App\Models\DocumentGenerator;
use Illuminate\Support\Str;
use App\Services\FileUploadService;
use App\Services\AIService;
use App\Services\ImageTextService;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use App\Models\Prompt;
use App\Models\PromptVerification;
use App\Models\GlobalContractText;
use App\Models\GlobalContractQuestion;
use App\Models\GlobalContractQuestionData;
use App\Models\GlobalContractSubCondition;
use App\Models\GlobalContractQuestionCondition;
use App\Models\GlobalContractMultipleChoiceQuestion;
use App\Models\PartiesSectionTemplate;
use App\Models\RecommendedSection;
use App\Models\StandardDocument;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Exception;
use Imagick;
use PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    protected $fileUploadService;
    protected $IMG;
    public function __construct(FileUploadService $fileUploadService, ImageTextService $IMG)
    {
        $this->fileUploadService = $fileUploadService;
        $this->IMG = $IMG;
    }


    public function documents()
    {
        $categories  = DocumentCategory::where('is_deleted', 0)->get();
        $related_documents = Document::where('published', 1)->get();
        $reviews = Document::with('reviews')->get();
        $selectedCategoryIds = isset($document)
            ? $document->categories->pluck('id')->toArray()
            : [];
        return view('admin.documents.document', compact('categories', 'related_documents', 'reviews', 'selectedCategoryIds'));
    }

    // DocumentFormRequest
    public function addDocuments(DocumentFormRequest $request)
    {
        // return $request->all();

        DB::beginTransaction();
        try {
            $document = new Document;
            $document->title = $request->title;
            $document->slug = $request->slug;
            $document->name_on_image = implode('@', explode(' ', $request->title));
            $document->save();

            $document->document_image = $this->IMG->addTextToImage($document->name_on_image);

            $document->short_description = $request->short_description;
            $document->btn_text = $request->document_button_text;
            $document->long_description = $request->long_description;
            $document->faq_title = $request->faq_title;

            if ($request->hasFile('field_image') != null && $request->has('img_heading') != null && $request->has('img_description') != null && $request->has('img_description_second') != null) {
                for ($i = 0; $i < count($request->img_heading); $i++) {
                    $file = $request->field_image[$i];
                    $img_headings = $request->img_heading[$i];
                    $img_descriptions = $request->img_description[$i];
                    $img_descriptions2 = $request->img_description_second[$i];

                    $directory = "public/document_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if (isset($fileuploadData) && $fileuploadData->status == '200') {
                        $document_field = new DocumentsField;
                        $document_field->document_id = $document->id;
                        $document_field->heading = $img_headings;
                        $document_field->description = $img_descriptions;
                        $document_field->description2 = $img_descriptions2;
                        $document_field->media_id = $fileuploadData->id;
                        $document_field->save();
                    } elseif ($fileuploadData->status == '400') {
                        DB::rollBack();
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            if ($request->hasFile('new_field_image') != null && $request->has('new_img_heading') != null && $request->has('new_img_description') != null && $request->has('new_img_description_second') != null) {
                for ($i = 0; $i < count($request->new_img_heading); $i++) {
                    $file = $request->new_field_image[$i];
                    $img_headings = $request->new_img_heading[$i];
                    $img_descriptions = $request->new_img_description[$i];
                    $img_descriptions2 = $request->new_img_description_second[$i];
                    $directory = "public/document_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();
                    if (isset($fileuploadData) && $fileuploadData->status == '200') {
                        $document_field = new DocumentsField;
                        $document_field->document_id = $document->id;
                        $document_field->heading = $img_headings;
                        $document_field->description = $img_descriptions;
                        $document_field->description2 = $img_descriptions2;
                        $document_field->media_id = $fileuploadData->id;
                        $document_field->save();
                    } elseif ($fileuploadData->status == '400') {
                        DB::rollBack();
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            if ($request->has('new_question') && $request->has('new_answer')) {
                for ($i = 0; $i < count($request->new_question); $i++) {
                    $document_faq = new DocumentFaq;
                    $document_faq->document_id = $document->id;
                    $document_faq->question = $request->new_question[$i];
                    $document_faq->answer = $request->new_answer[$i];
                    $document_faq->save();
                }
            }

            $document->legal_heading = $request->legal_heading;
            $document->legal_description = $request->legal_description;
            $document->legal_btn_text = $request->legal_btn_text;


            // if($request->hasFile('legal_doc_image')){
            //     $legal_image = $request->file('legal_doc_image');
            //     $directory = "public/document_images";
            //     $imagename = generateFileName($legal_image);
            //     $path = $legal_image->storeAs($directory, $imagename);

            //     $fullPath = storage_path("app/" . $path);
            //     chmod($fullPath, 0775);

            //     $document->legal_doc_image = $imagename;
            //     $document->directory_name = $directory;
            //     $document->file_path = $path;
            // }

            $documentQuestion = Question::where('document_id', $document->id)->get();
            $documentText = DocumentRightSection::where('document_id', $document->id)->get();

            if ($documentQuestion->isNotEmpty() && $documentText->isNotEmpty()) {
                $document->published = $request->published;
                $this->generatePDFImage($document->id);
            } else {
                if ($request->published == 0) {
                    $document->published = $request->published;
                } else {
                    $document->published = 0;
                }
            }


            $related_doc = $request->has('select_related_doc') ? $request->select_related_doc : [];

            $aiRecommendedDocs = $document->getSimilarDocuments();

            $finalRecommendations = array_unique(array_merge($related_doc, $aiRecommendedDocs));

            $current_related_docs = DocumentRelated::where('document_id', $document->id)
                ->pluck('related_document_id')
                ->toArray();

            $docs_to_delete = array_diff($current_related_docs, $finalRecommendations);

            DocumentRelated::where('document_id', $document->id)
                ->whereIn('related_document_id', $docs_to_delete)
                ->delete();

            $new_recommendations = array_diff($finalRecommendations, $current_related_docs);

            $insertData = array_map(fn($related_document_id) => [
                'document_id' => $document->id,
                'related_document_id' => $related_document_id,
                'status' => 1,
            ], $new_recommendations);

            // dd($insertData);

            if (!empty($insertData)) {
                foreach ($insertData as $data) {
                    $related_document = new DocumentRelated;
                    $related_document->document_id = $data['document_id'];
                    $related_document->related_document_id = $data['related_document_id'];
                    $related_document->status = $data['status'];
                    $related_document->save();
                }
            }



            $document->doc_price = $request->doc_price;

            if ($request->has('category_id')) {
                for ($i = 0; $i < count($request->category_id); $i++) {
                    $category_id = $request->category_id[$i];

                    $document_with_category = new DocumentWithCategory;
                    $document_with_category->document_id = $document->id;
                    $document_with_category->category_id = $category_id;
                    $document_with_category->save();
                }
            }

            $document->meta_title = $request->meta_title;
            $document->meta_description = $request->meta_description;
            $document->primary_keywords = $request->primary_keywords;
            $document->secondary_keywords = $request->secondary_keywords;
            // $document->longtail_keywords = $request->longtail_keywords;
            // $document->high_intent_keywords = $request->high_intent_keywords;
            $document->status = 0;
            $document->save();
            DB::commit();

            // return redirect()->back()->with('success','Document Added Successfully.');
            return redirect('/admin-dashboard/documents')->with('success', 'Document Added Successfully.');
        } catch (Exception $e) {

            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function allDocuments()
    {
        

        $documents = Document::orderBY('created_at', 'asc')->paginate(200);
        return view('admin.documents.all_documents', compact('documents'));
    }

    public function editDocument($slug)
    {
        $document_price = Setting::where('key', 'document_price')
        ->where('status', 1)
        ->first();        
        $document = Document::where('slug', $slug)->with('documentAgreement', 'documentGuide', 'documentField.media', 'relatedDocuments', 'documentFaq')->first();
        $categories  = DocumentCategory::where('is_deleted', 0)->get();
        $related_documents = Document::where('published', 1)->get();
        $selectedCategoryIds = isset($document)
            ? $document->categories->pluck('id')->toArray()
            : [];

        return view('admin.documents.document', compact('categories', 'document', 'related_documents', 'selectedCategoryIds', 'document_price'));
    }

    public function  updateDocument(Request $request)
    {
        $request->validate([
            'category_id' => 'required|array',
            'category_id.*' => 'exists:document_categories,id',
        ]);

        DB::beginTransaction();
        try {
            $document = Document::where('id', $request->id)->first();
            $document->title = $request->title;
            $document->slug = $request->slug;
            $document->short_description = $request->short_description;
            $document->btn_text = $request->document_button_text;
            $document->long_description = $request->long_description;
            $document->legal_heading = $request->legal_heading;
            $document->legal_description = $request->legal_description;
            $document->legal_btn_text = $request->legal_btn_text;

            $documentQuestion = Question::where('document_id', $request->id)->get();
            $documentText = DocumentRightSection::where('document_id', $request->id)->get();

            if ($documentQuestion->isNotEmpty() && $documentText->isNotEmpty()) {
                $document->published = $request->published;
                $this->generatePDFImage($request->id);
            } else {
                if ($request->published == 0) {
                    $document->published = $request->published;
                } else {
                   

                    $document->published = 0;
                    return redirect('/admin-dashboard/documents/edit/' . $document->slug)
                        ->with('error', 'This document does not fulfill the requirements to be published.');
                }
            }

            $document->doc_price = $request->doc_price;
            $document->meta_title = $request->meta_title;
            $document->meta_description = $request->meta_description;
            $document->primary_keywords = $request->primary_keywords;
            $document->secondary_keywords = $request->secondary_keywords;
            $document->update();


            // Add Faq section //

            if ($request->has('new_question') && $request->has('new_answer')) {
                for ($i = 0; $i < count($request->new_question); $i++) {
                    $document_faq = new DocumentFaq;
                    $document_faq->document_id = $request->id;
                    $document_faq->question = $request->new_question[$i];
                    $document_faq->answer = $request->new_answer[$i];
                    $document_faq->save();
                }
            }

            if ($request->has('question')) {
                foreach ($request->question as $index => $value) {
                    $document_faq = DocumentFaq::find($index);
                    $document_faq->question = $value;
                    $document_faq->update();
                }
            }

            if ($request->has('answer')) {
                foreach ($request->answer as $key => $val) {
                    $document_faq = DocumentFaq::find($key);
                    $document_faq->answer = $val;
                    $document_faq->update();
                }
            }

            if ($request->has('is_ai')) {
                foreach ($request->is_ai as $faq_id => $value) {
                    DocumentFaq::where('id', $faq_id)->update([
                        'is_ai' => $value
                    ]);
                }
            }

            if ($request->has('img_heading') != null) {
                foreach ($request->img_heading as $index => $value) {
                    $document_field = DocumentsField::find($index);
                    $document_field->heading = $value;
                    $document_field->update();
                }
            }

            if ($request->has('img_description') != null) {
                foreach ($request->img_description as $key => $val) {
                    $document_field = DocumentsField::find($key);
                    $document_field->description = $val;
                    $document_field->update();
                }
            }

            if ($request->has('img_description_second') != null) {
                foreach ($request->img_description_second as $index => $value) {
                    $document_field = DocumentsField::find($index);
                    $document_field->description2 = $value;
                    $document_field->update();
                }
            }

            if ($request->hasFile('new_field_image') != null && $request->has('new_img_heading') != null && $request->has('new_img_description') != null && $request->has('new_img_description_second') != null) {
                for ($i = 0; $i < count($request->new_img_heading); $i++) {
                    $file = $request->new_field_image[$i];
                    $img_headings = $request->new_img_heading[$i];
                    $img_descriptions = $request->new_img_description[$i];
                    $img_descriptions2 = $request->new_img_description_second[$i];

                    $directory = "public/document_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if (isset($fileuploadData) && $fileuploadData->status == '200') {
                        $document_field = new DocumentsField;
                        $document_field->document_id = $request->id;
                        $document_field->heading = $img_headings;
                        $document_field->description = $img_descriptions;
                        $document_field->description2 = $img_descriptions2;
                        $document_field->media_id = $fileuploadData->id;
                        $document_field->save();
                    } elseif ($fileuploadData->status == '400') {
                        DB::rollBack();
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            $related_doc = $request->has('select_related_doc') ? $request->select_related_doc : [];

            $aiRecommendedDocs = $document->getSimilarDocuments();

            $finalRecommendations = array_unique(array_merge($related_doc, $aiRecommendedDocs));

            $current_related_docs = DocumentRelated::where('document_id', $document->id)
                ->pluck('related_document_id')
                ->toArray();

            $docs_to_delete = array_diff($current_related_docs, $finalRecommendations);

            DocumentRelated::where('document_id', $document->id)
                ->whereIn('related_document_id', $docs_to_delete)
                ->delete();

            $new_recommendations = array_diff($finalRecommendations, $current_related_docs);

            $insertData = array_map(fn($related_document_id) => [
                'document_id' => $document->id,
                'related_document_id' => $related_document_id,
                'status' => 1,
            ], $new_recommendations);

            if (!empty($insertData)) {
                foreach ($insertData as $data) {
                    $related_document = new DocumentRelated;
                    $related_document->document_id = $data['document_id'];
                    $related_document->related_document_id = $data['related_document_id'];
                    $related_document->status = $data['status'];
                    $related_document->save();
                }
            }

            if ($request->has('category_id')) {
                $document = Document::find($request->id);
                $categoryIds = $request->category_id;
                $document->categories()->sync($categoryIds);
            }

            if ($request->field_img_id != null) {
                $deleteIds = explode(',', $request->field_img_id);
                foreach ($deleteIds as $id) {
                    $document_field = DocumentsField::where('id', $id)->with('media')->first();
                    if ($document_field->media) {
                        $image_path = getFilePath($document_field->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if (is_dir($directory_path) && count(scandir($directory_path)) == 2) {
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id', $document_field->media_id)->delete();
                        $document_field->media_id = null;
                        $document_field->update();
                    }
                }
            }


            if ($request->img_sec_ids != null) {
                $removeIds = explode(',', $request->img_sec_ids);
                foreach ($removeIds as $id) {
                    $removeDocumentFields = DocumentsField::where('id', $id)->with('media')->first();
                    if ($removeDocumentFields->media) {
                        $image_path = getFilePath($removeDocumentFields->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if (is_dir($directory_path) && count(scandir($directory_path)) == 2) {
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id', $removeDocumentFields->media_id)->delete();
                    }
                    $removeDocumentFields->delete();
                }
            }

            if ($request->has('faq_ids')) {
                $faqIds = explode(',', $request->faq_ids);
                foreach ($faqIds as $id) {
                    $document_faq = DocumentFaq::find($id);
                    if ($document_faq) {
                        $document_faq->delete();
                    }
                }
            }

            DB::commit();

            return redirect('/admin-dashboard/documents/edit/' . $document->slug)->with('success', 'Document Successfully Updated.');
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateDocumentApi(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:documents,id',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:document_categories,id',
        ]);

        DB::beginTransaction();
        try {

            $document = Document::findOrFail($request->id);
            $document->title = $request->title;
            $document->slug = $request->slug;
            $document->short_description = $request->short_description;
            $document->btn_text = $request->document_button_text;
            $document->long_description = $request->long_description;
            $document->legal_heading = $request->legal_heading;
            $document->legal_description = $request->legal_description;
            $document->legal_btn_text = $request->legal_btn_text;

            // Added legal_doc_image handling
            if ($request->hasFile('legal_doc_image')) {
                $legal_image = $request->file('legal_doc_image');
                $directory = "public/document_images";
                $imagename = generateFileName($legal_image);
                $path = $legal_image->storeAs($directory, $imagename);

                $fullPath = storage_path("app/" . $path);
                chmod($fullPath, 0775);

                $document->legal_doc_image = $imagename;
                $document->directory_name = $directory;
                $document->file_path = $path;
            }

            $documentQuestion = Question::where('document_id', $request->id)->exists();
            $documentText = DocumentRightSection::where('document_id', $request->id)->exists();

            if ($documentQuestion && $documentText) {
                $document->published = $request->published;
                if ($request->published == 1) {
                    $this->generatePDFImage($request->id);
                }
            } else {
                if ($request->published == 1) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'This document does not fulfill the requirements to be published '
                    ], 422);
                }
                $document->published = 0;
            }

            $document->doc_price = $request->doc_price;
            $document->meta_title = $request->meta_title;
            $document->meta_description = $request->meta_description;
            $document->primary_keywords = $request->primary_keywords;
            $document->secondary_keywords = $request->secondary_keywords;
            $document->longtail_keywords = $request->longtail_keywords;
            $document->high_intent_keywords = $request->high_intent_keywords;
            $document->faq_title = $request->faq_title;

            $document->save();

            //  FAQ CREATE
            if ($request->has('new_question') && $request->has('new_answer')) {
                foreach ($request->new_question as $i => $question) {
                    DocumentFaq::create([
                        'document_id' => $request->id,
                        'question' => $question,
                        'answer' => $request->new_answer[$i] ?? null
                    ]);
                }
            }

            if ($request->has('question')) {
                foreach ($request->question as $id => $value) {
                    DocumentFaq::where('id', $id)->update(['question' => $value]);
                }
            }

            if ($request->has('answer')) {
                foreach ($request->answer as $id => $value) {
                    DocumentFaq::where('id', $id)->update(['answer' => $value]);
                }
            }

            /**
             *  DOCUMENT FIELDS UPDATE
             */
            if ($request->has('img_heading')) {
                foreach ($request->img_heading as $id => $value) {
                    DocumentsField::where('id', $id)->update(['heading' => $value]);
                }
            }

            if ($request->has('img_description')) {
                foreach ($request->img_description as $id => $value) {
                    DocumentsField::where('id', $id)->update(['description' => $value]);
                }
            }

            if ($request->has('img_description_second')) {
                foreach ($request->img_description_second as $id => $value) {
                    DocumentsField::where('id', $id)->update(['description2' => $value]);
                }
            }

            /**
             *  NEW IMAGE SECTIONS
             */
            if (
                $request->hasFile('new_field_image') &&
                $request->has('new_img_heading')
            ) {
                foreach ($request->new_img_heading as $i => $heading) {
                    $upload = $this->fileUploadService->upload(
                        $request->new_field_image[$i],
                        'public/document_images'
                    );

                    $data = $upload->getData();

                    if ($data->status !== '200') {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => $data->error
                        ], 422);
                    }

                    DocumentsField::create([
                        'document_id' => $request->id,
                        'heading' => $heading,
                        'description' => $request->new_img_description[$i] ?? null,
                        'description2' => $request->new_img_description_second[$i] ?? null,
                        'media_id' => $data->id,
                    ]);
                }
            }

            /**
             *  RELATED DOCUMENTS
             */
            $related = $request->select_related_doc ?? [];
            $aiDocs = $document->getSimilarDocuments();
            $finalDocs = array_unique(array_merge($related, $aiDocs));

            DocumentRelated::where('document_id', $document->id)->delete();

            foreach ($finalDocs as $relatedId) {
                DocumentRelated::create([
                    'document_id' => $document->id,
                    'related_document_id' => $relatedId,
                    'status' => 1
                ]);
            }

            /**
             *  CATEGORY SYNC
             */
            $document->categories()->sync($request->category_id);

            /**
             *  FAQ DELETE
             */
            if ($request->has('faq_ids')) {
                DocumentFaq::whereIn('id', explode(',', $request->faq_ids))->delete();
            }

            DB::commit();

            /**
             *  FINAL API RESPONSE
             */
            return response()->json([
                'success' => true,
                'message' => 'Document successfully updated ',
                'data' => [
                    'id' => $document->id,
                    'title' => $document->title,
                    'slug' => $document->slug,
                    'price' => $document->doc_price,
                    'published' => $document->published
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong ',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateDocumentSlug(Request $request)
    {
        $existing = Document::where('slug', $request->slug)
            ->where('id', '!=', $request->document_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'This slug already exists.',
            ]);
        }

        $document = Document::findOrFail($request->document_id);
        $document->slug = Str::slug($request->slug);
        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Permalink updated successfully.',
            'redirect_url' => route('admin.dashboard.edit_documents', $document->slug)
        ]);
    }

    public function getDocument($id)
    {
        try {
            $document = Document::find($id);

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'document' => [
                    'id' => $document->id,
                    'slug' => $document->slug,
                    'title' => $document->title,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
            public function loadMoreReviews(Request $request)
        {
            $reviews = Review::where('status', 1)
                ->where('document_id', $request->document_id)
                ->where('rating', 5)
                ->where('type', 'custom')
                ->orderBy('created_at', 'desc')
                ->skip($request->offset)
                ->take(10)
                ->get();

            return view('users.contracts.review-items', compact('reviews'));
        }


    public function deleteDocument($id)
    {
        $document = Document::find($id);

        if (!$document) {

            return redirect()->back()->with('error', 'Document not found');
        }

        // **Delete related records**
        $document->relatedDocuments()->detach();
        $document->categories()->detach();
        $document->documentAgreement()->delete();
        $document->documentGuide()->delete();
        $document->documentField()->delete();
        $document->documentFaq()->delete();
        $document->reviews()->delete();

        $questions = $document->relatedQuestions;
        foreach ($questions as $question) {
            $question->options()->delete();
            $question->questionData()->delete();

            foreach ($question->conditions as $condition) {
                $condition->subconditions()->delete();
                $condition->delete();
            }

            $question->delete();
        }

        $texts = $document->relatedTexts;
        foreach ($texts as $text) {
            $text->conditions()->delete();
            $text->delete();
        }

        $documentFields = DocumentsField::where('document_id', $document->id)->get();
        foreach ($documentFields as $field) {
            if ($field->media_id) {
                Storage::delete("public/document_images/" . $field->media_id);
            }
            $field->delete();
        }

        if ($document->legal_doc_image) {
            Storage::delete($document->file_path);
        }

        if ($document->document_image) {
            $publicImagePath = public_path("assets/img/contracts/" . basename($document->document_image));
            if (file_exists($publicImagePath)) {
                unlink($publicImagePath);
            }
        }

        $document->delete();

        return redirect()->back()->with(['success' => 'Document and all related data deleted successfully']);
    }

    public function updateFieldImage(Request $request)
    {
        if ($request->id != null) {
            $id = $request->id;
            if ($request->hasFile('field_image')) {
                $file = $request->file('field_image');
                $directory = "public/document_images";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();

                $document_field = DocumentsField::find($id);
                if (isset($fileuploadData) && $fileuploadData->status == '200') {
                    $document_field->media_id = $fileuploadData->id;
                    $document_field->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];
                } elseif ($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);
            } else {
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }

    public function generateKeywords(Request $request)
    {

        try {
            $request->validate([
                'title' => 'required|string|max:255',
            ]);

            $title = trim($request->title);
            $language = web_setting('language')->value ?? '';
            $country = web_setting('country')->value ?? '';

            $keywordPromptAttach = PromptAttach::with('prompt')->where('resource_id', 1007)->first();
            $ai_model = $keywordPromptAttach->prompt->prompt_ai_model;

            $aiService = new AIService();

            if (!$keywordPromptAttach) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keyword generation prompt not found.',
                ]);
            }

            $template = $keywordPromptAttach->prompt->updated_prompt ?? '';
            $finalPrompt = str_replace(['{document_name}', '{language} '], [$title, $language], $template);
            $keywordOutput = $aiService->generateText($finalPrompt);

            $cleanedOutput = trim(preg_replace(['/^(json|```json|```)\s*/i', '/```$/'], '', $keywordOutput));
            $decoded = json_decode($cleanedOutput, true);

            if (!is_array($decoded)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid keyword response format from AI.',
                ]);
            }

            return response()->json([
                'success' => true,
                'primary_keyword' => $decoded['primary_keyword'] ?? null,
                'secondary_keywords' => $decoded['secondary_keywords'] ?? [],
            ]);
        } catch (Exception $e) {
            saveLog("Error:", "DocumentController", $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    // public function saveDocument(Request $request)
    // {
    //     try {
    //         $request->validate([
    //             'title' => 'required|string|max:255',
    //         ]);

    //         $title = trim($request->title);
    //         $primary_keywords = $request->primary_keyword;
    //         $secondary_keywords = $request->secondary_keywords;
    //         $slug = Str::slug($title, '-');

    //         $language = web_setting('language')->value ?? '';
    //         $country = web_setting('country')->value ?? '';

    //         $documentId = $request->input('document_id');
    //         $query = Document::where(function ($q) use ($title, $slug) {
    //             $q->where('title', $title)->orWhere('slug', $slug);
    //         });

    //         if ($documentId) {
    //             $query->where('id', '!=', $documentId);
    //         }

    //         if ($query->exists()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'A document with the same title already exists.',
    //             ]);
    //         }

    //         $document = new Document();
    //         $document->title = $title;
    //         $document->slug = Str::slug($title, '-');
    //         $document->name_on_image = implode('@', explode(' ', $title));
    //         $document->save();

    //         $document->document_image = $this->IMG->addTextToImage($document->name_on_image);
    //         $document->save();

    //         $document->primary_keywords = $request->primary_keyword;

    //         if (is_array($request->secondary_keywords)) {
    //             $document->secondary_keywords = json_encode($request->secondary_keywords);
    //         } else {
    //             $document->secondary_keywords = $request->secondary_keywords;
    //         }

    //         $fullContentPrompt = PromptAttach::with('prompt')->where('resource_id', 1010)->first();
    //         $ai_model = $fullContentPrompt->prompt->prompt_ai_model;

    //         $aiService = new AIService($ai_model);
    //         $template = $fullContentPrompt->prompt->updated_prompt ?? '';
    //         $finalPrompt = str_replace(['{primary_keyword}', '{language}', '{country}'], [$request->primary_keyword, $language, $country], $template);
    //         $aiOutput = $aiService->generateText($finalPrompt);
    //         $cleanedOutput = trim($aiOutput);
    //         $cleanedOutput = preg_replace('/^(json|```json|```)\s*/i', '', $cleanedOutput);
    //         $cleanedOutput = preg_replace('/```$/', '', $cleanedOutput);
    //         $decoded = json_decode($cleanedOutput, true);

    //         $short_description = $decoded['short_description'] ?? '';
    //         $long_description = $decoded['long_description'] ?? '';

    //         $document->short_description = $short_description;

    //         $article_sections = $decoded['article_sections'] ?? [];

    //         foreach ($article_sections as $section) {
    //             $document_field = new DocumentsField();
    //             $document_field->document_id = $document->id;
    //             $document_field->heading = $section['title'] ?? null;
    //             $document_field->description = $section['short_description'] ?? null;
    //             $document_field->description2 = $section['follow_up_text'] ?? null;
    //             $image_description = $section['image_description'] ?? '';

    //             if (!empty($image_description)) {
    //                 $media = $aiService->generateAndStoreImageWithOpenAI($image_description);
    //                 Log::info('Generated image for section', ['media' => $media]);
    //                 if ($media) {
    //                     $document_field->media_id = $media->id;
    //                 }
    //             }
    //             $document_field->save();
    //         }

    //         $faqs = $decoded['faqs'] ?? [];
    //         foreach ($faqs as $faq) {
    //             $document_faq = new DocumentFaq();
    //             $document_faq->document_id = $document->id;
    //             $document_faq->question = $faq['faq_question'] ?? null;
    //             $document_faq->answer = $faq['faq_answer'] ?? null;
    //             $document_faq->save();
    //         }

    //         $metaPromptAttach = PromptAttach::with('prompt')->where('resource_id', 1006)->first();
    //         if ($metaPromptAttach) {
    //             $template = $metaPromptAttach->prompt->updated_prompt ?? '';
    //             $finalPrompt = str_replace(['{short_description}'], [$short_description], $template);
    //             $metaOutput = $aiService->generateText($finalPrompt);

    //             $cleanedOutput = trim($metaOutput);
    //             $cleanedOutput = preg_replace('/^(json|```json|```)\s*/i', '', $cleanedOutput);
    //             $cleanedOutput = preg_replace('/```$/', '', $cleanedOutput);
    //             $decoded = json_decode($cleanedOutput, true);

    //             if (is_array($decoded)) {
    //                 $document->meta_title = $decoded['meta_title'] ?? null;
    //                 $document->meta_description = $decoded['meta_description'] ?? null;
    //             }
    //         }

    //         $relatedDocumentPrompt = PromptAttach::with('prompt')->where('resource_id', 1005)->first();

    //         if ($relatedDocumentPrompt) {
    //             $available_documents = Document::where('published', '1')->get();
    //             $allDocumentsList = "";

    //             foreach ($available_documents as $avail_doc) {
    //                 $allDocumentsList .= "ID: {$avail_doc->id}, Title: {$avail_doc->title}, Description: {$avail_doc->short_description}\n";
    //             }

    //             $template = $relatedDocumentPrompt->prompt->updated_prompt ?? '';
    //             $finalPrompt = str_replace(
    //                 ['{short_description}', '{document_name}', '{available_documents}'],
    //                 [$short_description ?? '', $document->title ?? '', $allDocumentsList ?? ''],
    //                 $template
    //             );


    //             $relatedOutput = $aiService->generateText($finalPrompt);

    //             Log::info('Raw related document response from AI', ['output' => $relatedOutput]);

    //             $cleanedOutput = trim(preg_replace('/^json\s*/i', '', $relatedOutput));
    //             $decoded = json_decode($cleanedOutput, true);

    //             Log::info('Decoded related document IDs', ['decoded' => $decoded]);

    //             if (is_array($decoded) && !empty($decoded)) {
    //                 foreach ($decoded as $relatedId) {
    //                     if (!is_numeric($relatedId)) {
    //                         Log::warning('Invalid related document ID skipped', ['id' => $relatedId]);
    //                         continue;
    //                     }

    //                     DocumentRelated::updateOrCreate([
    //                         'document_id' => $document->id,
    //                         'related_document_id' => $relatedId,
    //                     ], [
    //                         'status' => 1
    //                     ]);
    //                 }
    //             } else {
    //                 Log::error('Failed to decode valid related document JSON response.', ['cleaned' => $cleanedOutput]);
    //             }
    //         }

    //         $document->save();

    //         return response()->json([
    //             'success' => true,
    //             'redirect_url' => route('admin.dashboard.edit_documents', $document->slug)
    //         ]);
    //     } catch (Exception $e) {
    //         saveLog("Error:", "DocumentController", $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong. Please try again.',
    //         ]);
    //     }
    // }

    public function saveDocument(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $title = trim($request->title);
        $primary_keywords = $request->primary_keyword;
        $secondary_keywords = $request->secondary_keywords;
        $slug = Str::slug($title, '-');

        $language = web_setting('language')->value ?? '';
        $country = web_setting('country')->value ?? '';

        $documentId = $request->input('document_id');

        if ($documentId) {
            $document = Document::find($documentId);
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found.',
                ]);
            }
        } else {
            $query = Document::where(function ($q) use ($title, $slug) {
                $q->where('title', $title)->orWhere('slug', $slug);
            });

            if ($query->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A document with the same title already exists.',
                ]);
            }

            $document = new Document();
            $document->title = $title;
            $document->slug = $slug;
            $document->name_on_image = implode('@', explode(' ', $title));
            $document->save();

            $document->document_image = $this->IMG->addTextToImage($document->name_on_image);
            $document->save();
        }

        $document->primary_keywords = $primary_keywords;

        if (is_array($secondary_keywords)) {
            $document->secondary_keywords = json_encode($secondary_keywords);
        } else {
            $document->secondary_keywords = $secondary_keywords;
        }

        $fullContentPrompt = PromptAttach::with('prompt')->where('resource_id', 1010)->first();
        $ai_model = $fullContentPrompt->prompt->prompt_ai_model ?? '';

        $aiService = new AIService($ai_model);
        $template = $fullContentPrompt->prompt->updated_prompt ?? '';
        $finalPrompt = str_replace(
            ['{primary_keyword}', '{language}', '{country}'],
            [$primary_keywords, $language, $country],
            $template
        );
        $aiOutput = $aiService->generateText($finalPrompt);
        $cleanedOutput = trim(preg_replace(['/^(json|```json|```)\s*/i', '/```$/'], '', $aiOutput));
        $decoded = json_decode($cleanedOutput, true);

        $short_description = $decoded['short_description'] ?? '';
        $long_description  = $decoded['long_description']  ?? '';

        $document->short_description = $short_description;

        if (!$documentId) {
            $article_sections = $decoded['article_sections'] ?? [];
            foreach ($article_sections as $section) {
                $document_field = new DocumentsField();
                $document_field->document_id  = $document->id;
                $document_field->heading      = $section['title']             ?? null;
                $document_field->description  = $section['short_description'] ?? null;
                $document_field->description2 = $section['follow_up_text']    ?? null;
                $image_description = $section['image_description'] ?? '';

                if (!empty($image_description)) {
                    $media = $aiService->generateAndStoreImageWithOpenAI($image_description);
                    if ($media) {
                        $document_field->media_id = $media->id;
                    }
                }
                $document_field->save();
            }

            $faqs = $decoded['faqs'] ?? [];
            foreach ($faqs as $faq) {
                $document_faq = new DocumentFaq();
                $document_faq->document_id = $document->id;
                $document_faq->question    = $faq['faq_question'] ?? null;
                $document_faq->answer      = $faq['faq_answer']   ?? null;
                $document_faq->save();
            }
        }

        $metaPromptAttach = PromptAttach::with('prompt')->where('resource_id', 1006)->first();
        if ($metaPromptAttach) {
            $template    = $metaPromptAttach->prompt->updated_prompt ?? '';
            $finalPrompt = str_replace(['{short_description}'], [$short_description], $template);
            $metaOutput  = $aiService->generateText($finalPrompt);

            $cleanedOutput = trim(preg_replace(['/^(json|```json|```)\s*/i', '/```$/'], '', $metaOutput));
            $decodedMeta   = json_decode($cleanedOutput, true);

            if (is_array($decodedMeta)) {
                $document->meta_title       = $decodedMeta['meta_title']       ?? null;
                $document->meta_description = $decodedMeta['meta_description'] ?? null;
            }
        }

        // Related documents
        $relatedDocumentPrompt = PromptAttach::with('prompt')->where('resource_id', 1005)->first();
        if ($relatedDocumentPrompt) {
            $available_documents = Document::where('published', '1')->where('id', '!=', $document->id)->get();
            $allDocumentsList    = '';

            foreach ($available_documents as $avail_doc) {
                $allDocumentsList .= "ID: {$avail_doc->id}, Title: {$avail_doc->title}, Description: {$avail_doc->short_description}\n";
            }

            $template    = $relatedDocumentPrompt->prompt->updated_prompt ?? '';
            $finalPrompt = str_replace(
                ['{short_description}', '{document_name}', '{available_documents}'],
                [$short_description ?? '', $document->title ?? '', $allDocumentsList ?? ''],
                $template
            );

            $relatedOutput = $aiService->generateText($finalPrompt);
            $cleanedOutput = trim(preg_replace('/^json\s*/i', '', $relatedOutput));
            $decodedRelated = json_decode($cleanedOutput, true);

            if (is_array($decodedRelated) && !empty($decodedRelated)) {
                foreach ($decodedRelated as $relatedId) {
                    if (!is_numeric($relatedId)) continue;

                    DocumentRelated::updateOrCreate([
                        'document_id'         => $document->id,
                        'related_document_id' => $relatedId,
                    ], ['status' => 1]);
                }
            }
        }

        $document->save();

        return response()->json([
            'success'           => true,
            'redirect_url'      => route('admin.dashboard.edit_documents', $document->slug),
            'short_description' => $short_description,
            'meta_title'        => $document->meta_title,
            'meta_description'  => $document->meta_description,
        ]);

    } catch (Exception $e) {
        saveLog("Error:", "DocumentController", $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please try again.',
        ]);
    }
}


    public function generalSection()
    {
        $keys = [
            'guide_heading',
            'guide_button',
            'rating_text',
            'valid_in',
            'applicable_in',
            'related_heading',
            'related_description',
            'detail_page_letter_now_btn',
            'detail_page_job_recommend_btn',
            'legal_section_heading',
            'document_faq_heading',
            'ultima_revision_text',
            'formatos_disponibles_text',
            'formatos_disponibles_data_text',
            'aplicable_en_text',
            'descargas_text',
            'descargas_data_text',
            'open_review_modal_button_text',
            'review_modal_publicamente_text',
            'review_modal_nombre_publico_placeholder',
            'review_modal_description_placeholder',
            'review_modal_not_login_message_text',
            'review_modal_hace_text',
            'agreement_headline',
            'agreement_short_description',
        ];

        $results = GeneralSection::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'guide_heading' => $results['guide_heading']->value ?? null,
            'guide_button' => $results['guide_button']->value ?? null,
            'rating_text' => $results['rating_text']->value ?? null,
            'valid_in' => $results['valid_in']->value ?? null,
            'applicable_in' => $results['applicable_in']->value ?? null,
            'related_heading' => $results['related_heading']->value ?? null,
            'related_description' => $results['related_description']->value ?? null,
            'detail_page_letter_now_btn' => $results['detail_page_letter_now_btn']->value ?? null,
            'detail_page_job_recommend_btn' => $results['detail_page_job_recommend_btn']->value ?? null,
            'legal_section_heading' => $results['legal_section_heading']->heading ?? null,
            'legal_section_description' => $results['legal_section_heading']->description ?? null,
            'document_faq_heading' => $results['document_faq_heading']->value ?? null,
            'ultima_revision_text' => $results['ultima_revision_text']->value ?? null,
            'formatos_disponibles_text' => $results['formatos_disponibles_text']->value ?? null,
            'formatos_disponibles_data_text' => $results['formatos_disponibles_data_text']->value ?? null,
            'aplicable_en_text' => $results['aplicable_en_text']->value ?? null,
            'descargas_text' => $results['descargas_text']->value ?? null,
            'descargas_data_text' => $results['descargas_data_text']->value ?? null,
            'open_review_modal_button_text' => $results['open_review_modal_button_text']->value ?? null,
            'review_modal_publicamente_text' => $results['review_modal_publicamente_text']->value ?? null,
            'review_modal_nombre_publico_placeholder' => $results['review_modal_nombre_publico_placeholder']->value ?? null,
            'review_modal_description_placeholder' => $results['review_modal_description_placeholder']->value ?? null,
            'review_modal_not_login_message_text' => $results['review_modal_not_login_message_text']->value ?? null,
            'review_modal_hace_text' => $results['review_modal_hace_text']->value ?? null,
            'agreement_headline' => $results['agreement_headline']->value ?? null,
            'agreement_short_description' => $results['agreement_short_description']->value ?? null,
        ];

        $agreements = GeneralSection::where('key', 'agreement')->with('media')->get();
        $guides = GeneralSection::where('key', 'guide_section')->get();
        $legal_sections = GeneralSection::where('key', 'legal')->with('media')->get();

        return view('admin.documents.general_section', compact('data', 'agreements', 'guides', 'legal_sections'));
    }

    public function addGeneralSection(Request $request)
    {
        // dd($request->all());

        DB::beginTransaction();
        try {
            if ($request->hasFile('agreement_image') != null && $request->has('agreement_heading') != null && $request->has('agreement_description') != null) {
                $agreement_image = $request->file('agreement_image');
                for ($i = 0; $i < count($agreement_image); $i++) {
                    $file = $agreement_image[$i];
                    $agreement_heading = $request->agreement_heading[$i];
                    $agreement_description = $request->agreement_description[$i];

                    $directory = "public/general_section_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if (isset($fileuploadData) && $fileuploadData->status == '200') {
                        $general_section = new GeneralSection;
                        $general_section->key = 'agreement';
                        $general_section->media_id = $fileuploadData->id;
                        $general_section->heading = $agreement_heading;
                        $general_section->description = $agreement_description;
                        $general_section->save();
                    } elseif ($fileuploadData->status == '400') {
                        DB::rollBack();
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            if ($request->has('new_agreement_heading') != null) {
                foreach ($request->new_agreement_heading as $key => $val) {
                    $general_section = GeneralSection::find($key);
                    $general_section->heading = $val;
                    $general_section->update();
                }
            }

            if ($request->has('new_agreement_description') != null) {
                foreach ($request->new_agreement_description as $index => $value) {
                    $general_section = GeneralSection::find($index);
                    $general_section->description = $value;
                    $general_section->update();
                }
            }

            if ($request->has('step_title') != null && $request->has('step_description') != null) {
                $step_title = $request->step_title;
                for ($i = 0; $i < count($step_title); $i++) {
                    $title_steps = $step_title[$i];
                    $description = $request->step_description[$i];

                    $general_section = new GeneralSection;
                    $general_section->key = 'guide_section';
                    $general_section->heading = $title_steps;
                    $general_section->description = $description;
                    $general_section->save();
                }
            }

            if ($request->has('new_step_title') != null) {
                foreach ($request->new_step_title as $key => $val) {
                    $general_section = GeneralSection::find($key);
                    $general_section->heading = $val;
                    $general_section->update();
                }
            }

            if ($request->has('new_step_description') != null) {
                foreach ($request->new_step_description as $key => $val) {
                    $general_section = GeneralSection::find($key);
                    $general_section->description = $val;
                    $general_section->update();
                }
            }

            if ($request->has('legal_section_heading') && $request->has('legal_section_description')) {
                $legalSection = GeneralSection::where('key', 'legal_section_heading')->first();
                $legalSection->heading = $request->legal_section_heading;
                $legalSection->description = $request->legal_section_description;
                $legalSection->update();
            }

            if ($request->hasFile('legal_section_image')) {
                $legal_image = $request->file('legal_section_image');
                $directory = "public/general_section_images";
                $fileupload = $this->fileUploadService->upload($legal_image, $directory);
                $fileuploadData = $fileupload->getData();

                if (isset($fileuploadData) && $fileuploadData->status == '200') {
                    $general_section = GeneralSection::where('key', 'legal_section_heading')->first();
                    if ($general_section) {
                        $general_section->media_id = $fileuploadData->id;
                        $general_section->update();
                    }
                } elseif ($fileuploadData->status == '400') {
                    DB::rollBack();
                    return redirect()->back()->with('error', $fileuploadData->error);
                }
            }

            if ($request->hasFile('new_legal_img') != null && $request->has('new_legal_heading') != null && $request->has('new_legal_description') != null) {
                $new_legal_img = $request->file('new_legal_img');
                for ($i = 0; $i < count($new_legal_img); $i++) {
                    $file = $new_legal_img[$i];
                    $legal_heading = $request->new_legal_heading[$i];
                    $legal_description = $request->new_legal_description[$i];

                    $directory = "public/general_section_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if (isset($fileuploadData) && $fileuploadData->status == '200') {
                        $general_section = new GeneralSection;
                        $general_section->key = 'legal';
                        $general_section->media_id = $fileuploadData->id;
                        $general_section->heading = $legal_heading;
                        $general_section->description = $legal_description;
                        $general_section->save();
                    } elseif ($fileuploadData->status == '400') {
                        DB::rollBack();
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            if ($request->has('legal_heading') != null) {
                foreach ($request->legal_heading as $key => $val) {
                    $general_section = GeneralSection::find($key);
                    $general_section->heading = $val;
                    $general_section->update();
                }
            }

            if ($request->has('legal_description') != null) {
                foreach ($request->legal_description as $index => $value) {
                    $general_section = GeneralSection::find($index);
                    $general_section->description = $value;
                    $general_section->update();
                }
            }

            $fields = [
                'guide_heading' => 'guide_heading',
                'guide_button' => 'guide_button',
                'document_faq_heading' => 'document_faq_heading',
                'rating_text' => 'rating_text',
                'valid_in' => 'valid_in',
                'applicable_in' => 'applicable_in',
                'related_heading' => 'related_heading',
                'related_description' => 'related_description',
                'detail_page_letter_now_btn' => 'detail_page_letter_now_btn',
                'detail_page_job_recommend_btn' => 'detail_page_job_recommend_btn',
                'open_review_modal_button_text' => 'open_review_modal_button_text',
                'review_modal_publicamente_text' => 'review_modal_publicamente_text',
                'review_modal_nombre_publico_placeholder' => 'review_modal_nombre_publico_placeholder',
                'review_modal_description_placeholder' => 'review_modal_description_placeholder',
                'review_modal_not_login_message_text' => 'review_modal_not_login_message_text',
                'review_modal_hace_text' => 'review_modal_hace_text',
                'ultima_revision_text' => 'ultima_revision_text',
                'formatos_disponibles_text' => 'formatos_disponibles_text',
                'formatos_disponibles_data_text' => 'formatos_disponibles_data_text',
                'aplicable_en_text' => 'aplicable_en_text',
                'descargas_text' => 'descargas_text',
                'descargas_data_text' => 'descargas_data_text',
                'agreement_headline' => 'agreement_headline',
                'agreement_short_description' => 'agreement_short_description',
            ];

            foreach ($fields as $key => $input) {
                if ($request->has($input)) {
                    $general_section = GeneralSection::where('key', $key)->first();
                    if ($general_section) {
                        $general_section->value = $request->$input;
                        $general_section->update();
                    } else {
                        $general_section = new GeneralSection;
                        $general_section->key = $key;
                        $general_section->value = $request->$input;
                        $general_section->save();
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data Updated Successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function addNewImage(Request $request)
    {
        // return $request->all();
        if ($request->id != null) {
            $id = $request->id;
            $type = $request->type;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $directory = "public/general_section_images";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();

                if (isset($fileuploadData) && $fileuploadData->status == '200') {
                    $general_section = GeneralSection::where([['id', $id], ['key', $type]])->first();
                    $general_section->media_id = $fileuploadData->id;
                    $general_section->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];
                } elseif ($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);
            } else {
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }

    public function updateAgreementImage(Request $request)
    {
        if ($request->id != null) {
            $id = $request->id;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $directory = "public/general_section_images";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();

                $document_agreement = DocumentAgreement::find($id);
                if (isset($fileuploadData) && $fileuploadData->status == '200') {
                    $document_agreement->media_id = $fileuploadData->id;
                    $document_agreement->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];
                } elseif ($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);
            } else {
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }

    public function addDocumentCategory()
    {
        $parent_category = DocumentCategory::all();
        return view('admin.documents.add_document_category', compact('parent_category'));
    }

    // public function categoryProcess(Request $request)
    // {
    //     try {
    //         if ($request->id != null) {
    //             $request->validate([
    //                 'name' => 'required',
    //                 'slug' => 'required',
    //             ]);
    //             $document_category = DocumentCategory::find($request->id);
    //             $status = 'updated';
    //         } else {
    //             $request->validate([
    //                 'name' => 'required',
    //                 'slug' => 'required|unique:document_categories,slug',
    //             ]);
    //             $document_category = new DocumentCategory;
    //             $status = 'saved';
    //         }
    //         $document_category->name = $request->name;
    //         $document_category->slug = $request->slug;
    //         $document_category->parent_category = $request->parent_category;
    //         $document_category->description = $request->description;
    //         $document_category->save();

    //         if ($status == 'updated') {
    //             return redirect('/admin-dashboard/document/categories')->with('success', 'Data Successfully updated');
    //         } elseif ($status == 'saved') {
    //             return redirect('/admin-dashboard/document/categories')->with('success', 'Data Successfully saved');
    //         }
    //     } catch (Exception $e) {
    //         saveLog("Error:", "DocumentController", $e->getMessage());
    //         return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    //     }
    // }

public function categoryProcess(Request $request)
{
    try {

        $request->validate([
            'name' => 'required',
            'slug' => [
                'required',
                // Rule::unique('document_categories', 'slug')->ignore($request->id)
                Rule::unique('document_categories', 'slug')
                    ->where(function ($query) {
                        return $query->where('is_deleted', false);
                    })
                    ->ignore($request->id)
            ],
        ]);

        if ($request->id != null) {
            $document_category = DocumentCategory::find($request->id);
            $status = 'updated';
        } else {
            $document_category = new DocumentCategory;
            $status = 'saved';
        }

        $document_category->name = $request->name;
        $document_category->slug = $request->slug;
        $document_category->parent_category = $request->parent_category;
        $document_category->description = $request->description;
        $document_category->save();

        return redirect('/admin-dashboard/document/categories')
            ->with('success', "Data Successfully {$status}");

    } catch (Exception $e) {
        saveLog("Error:", "DocumentController", $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
}

    public function allCategories()
    {
        $categories = DocumentCategory::where('is_deleted', false)->get();
        // $categories = DocumentCategory::get();
        //  echo"<pre>";
        //  print_r($categories);
        //  die();
        return view('admin.documents.categories', compact('categories'));
    }

    public function categoriesApi()
    {
        $categories = DocumentCategory::where('is_deleted', false)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function editCategory($slug)
    {
        $category =  DocumentCategory::where('slug', $slug)->first();
        $parent_category = DocumentCategory::where('slug', '!=', $slug)->get();
        return view('admin.documents.add_document_category', compact('category', 'parent_category'));
    }

    public function deleteCategory($id)
    {
        $category = DocumentCategory::with('documents')->where('id', $id)->firstOrFail();

        // Soft delete the category
        $category->is_deleted = true;
        $category->save();

        // Detach this category from documents
        // foreach ($category->documents as $document) {
        //     $document->categories()->detach($category->id);
        // }

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }


    public function reviews()
    {
        $documents = Document::where('published', 1)->get();
        return view('admin.reviews.review', compact('documents'));
    }

    public function addReview(Request $request)
    {
        //    return $request->all();

        try {
            if ($request->id != null) {

                $review = Review::find($request->id);
                $review->document_id = $request->doc_id;
                $review->rating = $request->rating;
                $review->first_name = $request->first_name;
                $review->last_name = $request->last_name;
                $review->city = $request->city_name;
                $review->date = $request->date;
                $review->description = $request->description;

                if ($request->is_show) {
                    $review->is_show = $request->is_show;
                } else {
                    $review->is_show = 0;
                }

                $review->update();

                if ($review->status == 1) {
                    return redirect('/admin-dashboard/published-reviews')->with('success', 'Review updated.');
                }

                if ($review->status == 0) {
                    return redirect('/admin-dashboard/pending-reviews')->with('success', 'Review updated.');
                }
            } else {
                $review = new Review;
                $review->document_id = $request->document;
                $review->rating = $request->rating;
                $review->first_name = $request->first_name;
                $review->last_name = $request->last_name;
                $review->city = $request->city_name;
                $review->date = $request->date;
                $review->description = $request->description;
                $review->type = 'custom';
                $review->is_show = 1;
                $review->status = 1;
                $review->save();

                return redirect('/admin-dashboard/published-reviews')->with('success', 'Review added.');
            }
        } catch (Exception $e) {
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function reviewsConfig(Request $request)
    {
        try {
            $web_settings = Setting::where('type', 'review')->all();
            return view('admin.reviews.config', 'web_settings');
        } catch (Exception $e) {
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function publishedReview()
    {
        $reviews = Review::where('status', 1)->get();
        return view('admin.reviews.all_reviews', compact('reviews'));
    }

    public function editReview($id)
    {
        $documents = Document::all();
        $review = Review::find($id);
        return view('admin.reviews.review', compact('documents', 'review'));
    }

    public function deleteReview(Request $request)
    {
        if ($request->id != null) {
            $review = Review::where('id', $request->id)->delete();
            $response = ([
                'code' => 200,
                'satus' => 'success',
            ]);

            return response()->json($response);
        }
    }

    public function reviewStatus(Request $request)
    {
        if ($request->value == 'unpublish') {
            $review = Review::find($request->id);
            $review->status = 0;
            $review->update();

            $response = ([
                'code' => 200,
                'status' => 'unpublish',
            ]);
        } elseif ($request->value == 'approve') {
            $review = Review::find($request->id);
            $review->status = 1;
            $review->update();

            $response = ([
                'code' => 200,
                'status' => 'approve',
            ]);
        }

        return response()->json($response);
    }

    public function pendingReviews()
    {
        $reviews = Review::where('status', 0)->get();
        return view('admin.reviews.pending_reviews', compact('reviews'));
    }

    public function rejectReviews(Request $request)
    {
        if ($request->id != null) {
            $review = Review::find($request->id);
            $review->status = 3;
            $review->update();

            $response = ([
                'code' => 200,
                'satus' => 'success',
            ]);

            return response()->json($response);
        }
    }

    public function allQuestion()
    {
        return view('admin.documents.all_document_question');
    }

    public function documentQuestion(Request $request)
    {
        // $documents = Document::where('published',1)->get();
        $types = QuestionType::all();
        $questions = '';
        $document = '';
        $slug = '';
        $document_questions = '';

        if (isset($request->id) && $request->id != null) {
            $questions = Question::where('document_id', $request->id)->get();
            $document = Document::find($request->id);
            $slug = $document->slug;

            $document_questions = Question::where('document_id', $request->id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                // ->orderByRaw('CAST(order_id AS UNSIGNED) ASC') // <- this ensures numeric sorting if column is stored as string
                ->get();


            // dd($document_questions);
        }

        // return view('admin.documents.document_questions',compact('documents','types','questions','document_questions','document','slug'));
        return view('admin.documents.document_questions', compact('types', 'questions', 'document_questions', 'document', 'slug'));
    }

    public function allquestionType()
    {
        $question_types = QuestionType::all();
        return view('admin.documents.all_types', compact('question_types'));
    }

    public function questionType()
    {
        return view('admin.documents.question_types');
    }

    public function addTypes(Request $request)
    {
        try {
            if ($request->id != null) {
                $question_type = QuestionType::find($request->id);
                $status = 'updated';
            } else {
                $request->validate([
                    'name' => 'required',
                    'slug' => 'required|unique:question_types,slug',
                ]);

                $question_type = new QuestionType;
                $status = 'saved';
            }

            $question_type->name = $request->name;
            $question_type->slug = $request->slug;
            $question_type->save();

            if ($status == 'updated') {
                return redirect('/admin-dashboard/edit-question-type/' . $question_type->slug)->with('success', 'Data Successfully updated');
            } elseif ($status == 'saved') {
                return redirect()->back()->with('success', 'Data Successfully saved');
            }
        } catch (Exception $e) {
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function editQuestionType($slug)
    {
        $question_type = QuestionType::where('slug', $slug)->first();
        return view('admin.documents.question_types', compact('question_type'));
    }

    public function addDocumentQuestion(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            // Change the questions types of existing questions
            if (isset($request->changed_question_types) && $request->changed_question_types != null) {
                $changed_question_types = json_decode($request->changed_question_types);

                foreach ($changed_question_types as $Types) {
                    $exists = DB::table('question_types')
                        ->whereIn('slug', [$Types->change_from, $Types->change_to])
                        ->pluck('slug')->toArray();

                    if (!in_array($Types->change_from, $exists) || !in_array($Types->change_to, $exists)) {
                        continue;
                    }
                    if ($Types->change_from == $Types->change_to) {
                        continue;
                    }

                    $qid = $Types->que_id;
                    // From Textbox/TextArea to Dropdown/ Radio
                    if ($Types->change_from == 'textarea' || $Types->change_from == 'textbox' && $Types->change_to == 'dropdown' || $Types->change_to == 'radio-button'  || $Types->change_to == 'textarea') {
                        $que = Question::find($qid);
                        $que->update(['type' => $Types->change_to]);
                    } else if ($Types->change_from == 'dropdown' && $Types->change_to == 'radio-button'  || $Types->change_from == 'radio-button' && $Types->change_to == 'dropdown') {
                        $que = Question::find($qid);
                        $que->update(['type' => $Types->change_to]);
                    } else if ($Types->change_from == 'dropdown' || $Types->change_from == 'radio-button' && $Types->change_to == 'textarea' || $Types->change_to == 'textbox' || $Types->change_to == 'date-field') {
                        $que = Question::find($qid);
                        $que->update(['type' => $Types->change_to]);
                        $que->options()->delete();
                    }
                }
            }

            Log::info(["formData => ", $request->formdata]);
            // End changed question types

            if (isset($request->formdata) && $request->formdata != null) {
                $formData = json_decode($request->formdata);

                foreach ($formData as $data) {
                    if ($data->is_new == true) {
                        $questions = new Question;
                        $questions->document_id = $request->documentID;
                        $questions->type = $data->type;

                        if (!empty($data->order_id)) {
                            $questions->order_id = $data->order_id;
                        } else {
                            $lastOrder = Question::where('document_id', $request->documentID)
                                ->orderBy('order_id', 'desc')
                                ->first();

                            $questions->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;
                        }

                        if (!empty($data->is_conditional_question) && !empty($data->is_conditional_step)) {
                            $is_condition = 1;
                            $condition_type = 3;
                        } elseif (!empty($data->is_conditional_question)) {
                            $is_condition = 1;
                            $condition_type = 1;
                        } elseif (!empty($data->is_conditional_step)) {
                            $is_condition = 1;
                            $condition_type = 2;
                        } else {
                            $is_condition = 0;
                            $condition_type = null;
                        }

                        $questions->is_condition = $is_condition;
                        $questions->condition_type = $condition_type;
                        $questions->is_end = $data->is_end;
                        $questions->save();

                        $question_data = new QuestionData;
                        $question_data->question_id = $questions->id;
                        $question_data->question_label = $data->question_label;
                        $question_data->question_info_text = $data->question_info_text;

                        if (isset($data->text_box_placeholder) && $data->text_box_placeholder != null) {
                            $question_data->text_box_placeholder = $data->text_box_placeholder;
                        }

                        if ($data->type == "dropdown-link") {
                            $question_data->same_contract_link_label = $data->same_contract_link;
                        }

                        // if(isset($data->go_to_step)){
                        //     if($data->go_to_step == "0"){
                        //         $question_data->next_question_id = null;
                        //     }else{
                        //         $question_data->next_question_id = $data->go_to_step;
                        //     }
                        // }

                        if (isset($data->go_to_step)) {
                            if (empty($data->go_to_step) || $data->go_to_step == "0") {
                                $question_data->next_question_id = null;
                            } else {
                                $question_data->next_question_id = (int) $data->go_to_step;
                            }
                        }


                        if ($questions->condition_type == 1) {
                            $question_condition_type = "question_label_condition";
                            $conditional_question_labels = $data->new_conditional_question_labels;
                            for ($i = 0; $i < count($conditional_question_labels); $i++) {
                                $conditional = $conditional_question_labels[$i];

                                $question_conditions = new QuestionCondition;
                                $question_conditions->question_id = $questions->id;
                                $question_conditions->condition_type = $question_condition_type;
                                $question_conditions->question_label = $conditional->label;
                                $question_conditions->conditional_question_id = $conditional->questionID;
                                $question_conditions->conditional_question_value = $conditional->question_value;
                                $question_conditions->save();
                            }
                        } elseif ($questions->condition_type == 2) {
                            $question_condition_type = "go_to_step_condition";
                            $step_conditions = $data->new_conditions;
                            for ($i = 0; $i < count($step_conditions); $i++) {
                                $step = $step_conditions[$i];

                                $question_conditions = new QuestionCondition;
                                $question_conditions->question_id = $questions->id;
                                $question_conditions->condition_type = $question_condition_type;

                                if (!empty($step->question_condition)) {
                                    if ($step->question_condition == "is_equal_to") {
                                        $conditionCheck = 1;
                                    } elseif ($step->question_condition == "is_greater_than") {
                                        $conditionCheck = 2;
                                    } elseif ($step->question_condition == "is_less_than") {
                                        $conditionCheck = 3;
                                    } elseif ($step->question_condition == "not_equal_to") {
                                        $conditionCheck = 4;
                                    }
                                }

                                $question_conditions->conditional_check = $conditionCheck;
                                $question_conditions->conditional_question_id = $step->questionID;
                                $question_conditions->conditional_question_value = $step->question_value;
                                $question_conditions->save();
                            }

                            if (isset($data->condition_go_to_step)) {
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                            }

                            if (!empty($data->is_another_conditional_step)) {
                                $question_condition_type = "another_go_to_step_condition";
                                if (!empty($data->new_another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $question_condition = new QuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if (!empty($step['go_to_step'])) {
                                            $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new SubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if (!empty($sub['question_condition'])) {
                                                    if ($sub['question_condition'] == "is_equal_to") {
                                                        $conditionCheck = 1;
                                                    } elseif ($sub['question_condition'] == "is_greater_than") {
                                                        $conditionCheck = 2;
                                                    } elseif ($sub['question_condition'] == "is_less_than") {
                                                        $conditionCheck = 3;
                                                    } elseif ($sub['question_condition'] == "not_equal_to") {
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($questions->condition_type == 3) {
                            if (!empty($data->new_conditional_question_labels)) {
                                $question_condition_type = "question_label_condition";
                                $conditional_question_labels = $data->new_conditional_question_labels;
                                for ($i = 0; $i < count($conditional_question_labels); $i++) {
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (!empty($data->new_conditions)) {
                                $question_condition_type = "go_to_step_condition";
                                $step_conditions = $data->new_conditions;
                                for ($i = 0; $i < count($step_conditions); $i++) {
                                    $step = $step_conditions[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if (!empty($step->question_condition)) {
                                        if ($step->question_condition == "is_equal_to") {
                                            $conditionCheck = 1;
                                        } elseif ($step->question_condition == "is_greater_than") {
                                            $conditionCheck = 2;
                                        } elseif ($step->question_condition == "is_less_than") {
                                            $conditionCheck = 3;
                                        } elseif ($step->question_condition == "not_equal_to") {
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (isset($data->condition_go_to_step)) {
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                            }


                            if (!empty($data->is_another_conditional_step)) {
                                $question_condition_type = "another_go_to_step_condition";
                                if (!empty($data->new_another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $question_condition = new QuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if (!empty($step['go_to_step'])) {
                                            $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new SubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                // $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if (!empty($sub['question_condition'])) {
                                                    if ($sub['question_condition'] == "is_equal_to") {
                                                        $conditionCheck = 1;
                                                    } elseif ($sub['question_condition'] == "is_greater_than") {
                                                        $conditionCheck = 2;
                                                    } elseif ($sub['question_condition'] == "is_less_than") {
                                                        $conditionCheck = 3;
                                                    } elseif ($sub['question_condition'] == "not_equal_to") {
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        Log::info(["data new_options => ", $data->new_options]);


                        if (isset($data->new_options) && $data->new_options != null) {
                            $order = 1;
                            for ($i = 0; $i < count($data->new_options); $i++) {
                                $option = $data->new_options[$i];
                                $multiple_options = new MultipleChoiceQuestionOption;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $option->option_label;
                                $multiple_options->option_value = $option->option_value;
                                $multiple_options->next_question_id = $option->option_go_to_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                            Log::info(["next_question_id => ", $option->option_go_to_step]);
                        }

                        Log::info(["data new_rows => ", $data->new_rows]);

                        if (!empty($data->new_rows)) {
                            $lastOrder = MultipleChoiceQuestionOption::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;

                            for ($i = 0; $i < count($data->new_rows); $i++) {
                                $row = $data->new_rows[$i];

                                $multiple_options = new MultipleChoiceQuestionOption;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                // $multiple_options->contract_send_to_next_step = $row->next_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                            Log::info(["row containing next_question_id => ", $row]);
                        }
                        $question_data->save();
                    } elseif ($data->is_new == false) {
                        $questions = Question::find($data->id);
                        $order_id = $data->order_id;

                        if (!empty($data->is_conditional_question) && !empty($data->is_conditional_step)) {
                            $is_condition = 1;
                            $condition_type = 3;
                        } elseif (!empty($data->is_conditional_question)) {
                            $is_condition = 1;
                            $condition_type = 1;
                        } elseif (!empty($data->is_conditional_step)) {
                            $is_condition = 1;
                            $condition_type = 2;
                        } else {
                            $is_condition = 0;
                            $condition_type = null;
                        }

                        $questions->is_condition = $is_condition;
                        $questions->condition_type = $condition_type;
                        $questions->is_end = $data->is_end;
                        $questions->order_id = $order_id;
                        $questions->update();

                        $question_data = QuestionData::where('question_id', $data->id)->first();
                        $question_data->question_label = $data->question_label;

                        if (isset($data->text_box_placeholder) && $data->text_box_placeholder != null) {
                            $question_data->text_box_placeholder = $data->text_box_placeholder;
                        }

                        if ($data->type == "dropdown-link") {
                            $question_data->same_contract_link_label = $data->same_contract_link;
                        }

                        if (isset($data->go_to_step)) {
                            if ($data->go_to_step == "0") {
                                $question_data->next_question_id = null;
                            } else {
                                $question_data->next_question_id = $data->go_to_step;
                            }
                        }
                        $question_data->question_info_text = $data->question_info_text;
                        $question_data->update();

                        if ($questions->condition_type == 1) {
                            $question_condition_type = "question_label_condition";

                            if (!empty($data->new_conditional_question_labels)) {
                                $new_conditional = $data->new_conditional_question_labels;
                                for ($i = 0; $i < count($new_conditional); $i++) {
                                    $conditional = $new_conditional[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (!empty($data->conditional_question_labels)) {
                                $conditional_question_labels = $data->conditional_question_labels;
                                for ($i = 0; $i < count($conditional_question_labels); $i++) {
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = QuestionCondition::where('id', $conditional->condition_id)->first();
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->update();
                                }
                            }
                        } elseif ($questions->condition_type == 2) {
                            $question_condition_type = "go_to_step_condition";

                            if (!empty($data->new_conditions)) {
                                $new_conditions = $data->new_conditions;
                                for ($i = 0; $i < count($new_conditions); $i++) {
                                    $step = $new_conditions[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if (!empty($step->question_condition)) {
                                        if ($step->question_condition == "is_equal_to") {
                                            $conditionCheck = 1;
                                        } elseif ($step->question_condition == "is_greater_than") {
                                            $conditionCheck = 2;
                                        } elseif ($step->question_condition == "is_less_than") {
                                            $conditionCheck = 3;
                                        } elseif ($step->question_condition == "not_equal_to") {
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (!empty($data->conditions)) {
                                $step_conditions = $data->conditions;
                                for ($i = 0; $i < count($step_conditions); $i++) {
                                    $step = $step_conditions[$i];

                                    $question_conditions = QuestionCondition::where('id', $step->condition_id)->first();

                                    if (!empty($step->question_condition)) {
                                        if ($step->question_condition == "is_equal_to") {
                                            $conditionCheck = 1;
                                        } elseif ($step->question_condition == "is_greater_than") {
                                            $conditionCheck = 2;
                                        } elseif ($step->question_condition == "is_less_than") {
                                            $conditionCheck = 3;
                                        } elseif ($step->question_condition == "not_equal_to") {
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if (isset($data->condition_go_to_step)) {
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                                $question_data->update();
                            }

                            if (!empty($data->is_another_conditional_step)) {
                                $question_condition_type = "another_go_to_step_condition";
                                if (!empty($data->new_another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $question_condition = new QuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if (!empty($step['go_to_step'])) {
                                            if ($step['go_to_step'] == '0') {
                                                $question_condition->go_to_step = null;
                                            } else {
                                                $question_condition->go_to_step = $step['go_to_step'];
                                            }
                                            // $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new SubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                // $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if (!empty($sub['question_condition'])) {
                                                    if ($sub['question_condition'] == "is_equal_to") {
                                                        $conditionCheck = 1;
                                                    } elseif ($sub['question_condition'] == "is_greater_than") {
                                                        $conditionCheck = 2;
                                                    } elseif ($sub['question_condition'] == "is_less_than") {
                                                        $conditionCheck = 3;
                                                    } elseif ($sub['question_condition'] == "not_equal_to") {
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($data->another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $existing_condition_id = $step['existing_condition_id'] ?? null;

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $conditionCheck = null;

                                                if (!empty($sub['question_condition'])) {
                                                    switch (strtolower(str_replace(' ', '_', $sub['question_condition']))) {
                                                        case 'is_equal_to':
                                                            $conditionCheck = 1;
                                                            break;
                                                        case 'is_greater_than':
                                                            $conditionCheck = 2;
                                                            break;
                                                        case 'is_less_than':
                                                            $conditionCheck = 3;
                                                            break;
                                                        case 'not_equal_to':
                                                            $conditionCheck = 4;
                                                            break;
                                                    }
                                                }

                                                if ($sub['status'] === false) {
                                                    $subcondition = SubCondition::find($sub['condition_id']);
                                                    if ($subcondition) {
                                                        $question_id = $subcondition->question_condition_id;
                                                        $question_condition = QuestionCondition::find($question_id);

                                                        if ($question_condition) {
                                                            if (isset($step['go_to_step'])) {
                                                                $question_condition->go_to_step = $step['go_to_step'] == '0' ? null : $step['go_to_step'];
                                                                $question_condition->save();
                                                            }
                                                        }

                                                        $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                        $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                        $subcondition->conditional_check = $conditionCheck;
                                                        $subcondition->save();
                                                    }
                                                } elseif ($sub['status'] === true) {
                                                    $subcondition = new SubCondition;
                                                    $subcondition->question_condition_id = $existing_condition_id;
                                                    $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                    $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                    $subcondition->conditional_check = $conditionCheck;
                                                    $subcondition->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($questions->condition_type == 3) {
                            if (!empty($data->new_conditional_question_labels)) {
                                $question_condition_type = "question_label_condition";
                                $new_conditional = $data->new_conditional_question_labels;
                                for ($i = 0; $i < count($new_conditional); $i++) {
                                    $conditional = $new_conditional[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (!empty($data->conditional_question_labels)) {
                                $conditional_question_labels = $data->conditional_question_labels;
                                for ($i = 0; $i < count($conditional_question_labels); $i++) {
                                    $conditional = $conditional_question_labels[$i];

                                    $question_conditions = QuestionCondition::where('id', $conditional->condition_id)->first();
                                    $question_conditions->question_label = $conditional->label;
                                    $question_conditions->conditional_question_id = $conditional->questionID;
                                    $question_conditions->conditional_question_value = $conditional->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if (!empty($data->new_conditions)) {
                                $question_condition_type = "go_to_step_condition";
                                $new_conditions = $data->new_conditions;
                                for ($i = 0; $i < count($new_conditions); $i++) {
                                    $step = $new_conditions[$i];

                                    $question_conditions = new QuestionCondition;
                                    $question_conditions->question_id = $questions->id;
                                    $question_conditions->condition_type = $question_condition_type;

                                    if (!empty($step->question_condition)) {
                                        if ($step->question_condition == "is_equal_to") {
                                            $conditionCheck = 1;
                                        } elseif ($step->question_condition == "is_greater_than") {
                                            $conditionCheck = 2;
                                        } elseif ($step->question_condition == "is_less_than") {
                                            $conditionCheck = 3;
                                        } elseif ($step->question_condition == "not_equal_to") {
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->save();
                                }
                            }

                            if (!empty($data->conditions)) {
                                $step_conditions = $data->conditions;
                                for ($i = 0; $i < count($step_conditions); $i++) {
                                    $step = $step_conditions[$i];

                                    $question_conditions = QuestionCondition::where('id', $step->condition_id)->first();

                                    if (!empty($step->question_condition)) {
                                        if ($step->question_condition == "is_equal_to") {
                                            $conditionCheck = 1;
                                        } elseif ($step->question_condition == "is_greater_than") {
                                            $conditionCheck = 2;
                                        } elseif ($step->question_condition == "is_less_than") {
                                            $conditionCheck = 3;
                                        } elseif ($step->question_condition == "not_equal_to") {
                                            $conditionCheck = 4;
                                        }
                                    }

                                    $question_conditions->conditional_check = $conditionCheck;
                                    $question_conditions->conditional_question_id = $step->questionID;
                                    $question_conditions->conditional_question_value = $step->question_value;
                                    $question_conditions->update();
                                }
                            }

                            if (isset($data->condition_go_to_step)) {
                                $question_data->conditional_go_to_step = $data->condition_go_to_step;
                                $question_data->update();
                            }

                            if (!empty($data->is_another_conditional_step)) {
                                $question_condition_type = "another_go_to_step_condition";
                                if (!empty($data->new_another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->new_another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $question_condition = new QuestionCondition();
                                        $question_condition->question_id = $questions->id;
                                        $question_condition->condition_type = $question_condition_type;

                                        if (!empty($step['go_to_step'])) {
                                            if ($step['go_to_step'] == '0') {
                                                $question_condition->go_to_step = null;
                                            } else {
                                                $question_condition->go_to_step = $step['go_to_step'];
                                            }
                                            // $question_condition->go_to_step = $step['go_to_step'];
                                        }

                                        $question_condition->save();

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $subcondition = new SubCondition();
                                                $subcondition->question_condition_id = $question_condition->id;
                                                // $subcondition->key = $key;
                                                $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                $subcondition->conditional_question_value = $sub['question_value'] ?? null;

                                                if (!empty($sub['question_condition'])) {
                                                    if ($sub['question_condition'] == "is_equal_to") {
                                                        $conditionCheck = 1;
                                                    } elseif ($sub['question_condition'] == "is_greater_than") {
                                                        $conditionCheck = 2;
                                                    } elseif ($sub['question_condition'] == "is_less_than") {
                                                        $conditionCheck = 3;
                                                    } elseif ($sub['question_condition'] == "not_equal_to") {
                                                        $conditionCheck = 4;
                                                    }
                                                }

                                                $subcondition->conditional_check = $conditionCheck;
                                                $subcondition->save();
                                            }
                                        }
                                    }
                                }

                                if (!empty($data->another_conditions)) {
                                    $step_conditions = json_decode(json_encode($data->another_conditions), true);

                                    foreach ($step_conditions as $key => $step) {
                                        $existing_condition_id = $step['existing_condition_id'] ?? null;

                                        if (!empty($step['subconditions']) && is_array($step['subconditions'])) {
                                            foreach ($step['subconditions'] as $sub) {
                                                $conditionCheck = null;

                                                if (!empty($sub['question_condition'])) {
                                                    switch (strtolower(str_replace(' ', '_', $sub['question_condition']))) {
                                                        case 'is_equal_to':
                                                            $conditionCheck = 1;
                                                            break;
                                                        case 'is_greater_than':
                                                            $conditionCheck = 2;
                                                            break;
                                                        case 'is_less_than':
                                                            $conditionCheck = 3;
                                                            break;
                                                        case 'not_equal_to':
                                                            $conditionCheck = 4;
                                                            break;
                                                    }
                                                }

                                                if ($sub['status'] === false) {
                                                    $subcondition = SubCondition::find($sub['condition_id']);
                                                    if ($subcondition) {
                                                        $question_id = $subcondition->question_condition_id;
                                                        $question_condition = QuestionCondition::find($question_id);

                                                        if ($question_condition) {
                                                            if (isset($step['go_to_step'])) {
                                                                $question_condition->go_to_step = $step['go_to_step'] == '0' ? null : $step['go_to_step'];
                                                                $question_condition->save();
                                                            }
                                                        }

                                                        $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                        $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                        $subcondition->conditional_check = $conditionCheck;
                                                        $subcondition->save();
                                                    }
                                                } elseif ($sub['status'] === true) {
                                                    $subcondition = new SubCondition;
                                                    $subcondition->question_condition_id = $existing_condition_id;
                                                    $subcondition->conditional_question_id = $sub['questionID'] ?? null;
                                                    $subcondition->conditional_question_value = $sub['question_value'] ?? null;
                                                    $subcondition->conditional_check = $conditionCheck;
                                                    $subcondition->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($data->add_options)) {
                            for ($i = 0; $i < count($data->add_options); $i++) {
                                $option = $data->add_options[$i];

                                if ($option->option_go_to_step == "0") {
                                    $go_to_step = null;
                                } else {
                                    $go_to_step = $option->option_go_to_step;
                                }

                                $multiple_options = MultipleChoiceQuestionOption::where('id', $option->option_id)->first();
                                if ($multiple_options) {
                                    $multiple_options->option_label = $option->option_label;
                                    $multiple_options->option_value = $option->option_value;
                                    $multiple_options->next_question_id = $go_to_step;
                                    $multiple_options->update();
                                }
                            }
                        }

                        if (!empty($data->new_options)) {
                            $lastOrder = MultipleChoiceQuestionOption::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;
                            foreach ($data->new_options as $option) {
                                if ($option->option_go_to_step == "0") {
                                    $go_to_step = null;
                                } else {
                                    $go_to_step = $option->option_go_to_step;
                                }

                                $multiple_options = new MultipleChoiceQuestionOption;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $option->option_label;
                                $multiple_options->option_value = $option->option_value;
                                $multiple_options->next_question_id = $go_to_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }


                        if (!empty($data->add_rows)) {
                            for ($i = 0; $i < count($data->add_rows); $i++) {
                                $row = $data->add_rows[$i];

                                $multiple_options = MultipleChoiceQuestionOption::where('id', $row->option_id)->first();
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                // $multiple_options->contract_send_to_next_step = $row->next_step;
                                $multiple_options->update();
                            }
                        }

                        if (!empty($data->new_rows)) {
                            $lastOrder = MultipleChoiceQuestionOption::where('question_id', $questions->id)->max('order_id');
                            $order = $lastOrder ? $lastOrder + 1 : 1;

                            for ($i = 0; $i < count($data->new_rows); $i++) {
                                $row = $data->new_rows[$i];

                                $multiple_options = new MultipleChoiceQuestionOption;
                                $multiple_options->question_id = $questions->id;
                                $multiple_options->option_label = $row->label;
                                $multiple_options->contract_link = $row->contract_link;
                                // $multiple_options->contract_send_to_next_step = $row->next_step;
                                $multiple_options->order_id = $order++;
                                $multiple_options->save();
                            }
                        }
                    }
                }

                if (!empty($request->option_id)) {
                    $ids = explode(',', $request->option_id);
                    foreach ($ids as $id) {
                        $options = MultipleChoiceQuestionOption::where('id', $id)->first();
                        if ($options) {
                            $options->delete();
                        }
                    }
                }

                if (!empty($request->condition_id)) {
                    $ids = explode(',', $request->condition_id);

                    foreach ($ids as $id) {
                        $condition = QuestionCondition::where('id', $id)->first();

                        if ($condition) {
                            $question_id = $condition->question_id;

                            // Delete the condition
                            $condition->delete();

                            // Fetch remaining conditions for the question
                            $remainingConditions = QuestionCondition::where('question_id', $question_id)->get();

                            if ($remainingConditions->isEmpty()) {
                                // No more conditions, reset values in questions table
                                Question::where('id', $question_id)->update([
                                    'is_condition' => 0,
                                    'condition_type' => null
                                ]);
                            } else {
                                // Determine new condition_type based on remaining string types
                                $types = $remainingConditions->pluck('condition_type')->unique()->toArray();

                                $newConditionType = null;
                                $isCondition = 1;

                                // Check what types are present
                                $hasQuestionLabel = in_array('question_label_condition', $types);
                                $hasGoToStep = in_array('go_to_step_condition', $types);

                                if ($hasQuestionLabel && $hasGoToStep) {
                                    $newConditionType = 3; // Both
                                } elseif ($hasQuestionLabel) {
                                    $newConditionType = 1; // Only question label
                                } elseif ($hasGoToStep) {
                                    $newConditionType = 2; // Only go to step
                                }

                                // Update question table
                                Question::where('id', $question_id)->update([
                                    'is_condition' => $isCondition,
                                    'condition_type' => $newConditionType
                                ]);
                            }
                        }
                    }
                }

                if (!empty($request->sub_condition_id)) {
                    $ids = explode(',', $request->sub_condition_id);

                    foreach ($ids as $id) {
                        $sub_condition = SubCondition::where('id', $id)->first();

                        // dd($sub_condition);

                        if ($sub_condition) {
                            $qu_condition_id = $sub_condition->question_condition_id;
                            $sub_condition->delete();

                            $remainingSubConditions = SubCondition::where('question_condition_id', $qu_condition_id)->get();

                            if ($remainingSubConditions->isEmpty()) {
                                QuestionCondition::where('id', $qu_condition_id)->delete();
                            }
                        }
                    }
                }


                if (!empty($request->remove_question_id)) {
                    $deleteIds = explode(',', $request->remove_question_id);
                    foreach ($deleteIds as $id) {
                        $delete_question = Question::where('id', $id)->first();
                        if ($delete_question) {
                            $delete_question->delete();
                        }
                    }
                }

                DB::commit();
                return redirect()->back()->with('success', 'Document Questions added successfully.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function articleSection()
    {
        $article_sections = ArticleSection::where('key', 'article')->get();
        $keys = [
            'example_section_heading',
            'example_section_description1',
            'example_section_description2',

        ];

        $results = ArticleSection::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'example_section_heading' => $results['example_section_heading']->heading ?? null,
            'example_section_description1' => $results['example_section_description1']->description ?? null,
            'example_section_description2' => $results['example_section_description2']->description ?? null,
        ];

        return view('admin.documents.article_section', compact('article_sections', 'data'));
    }

    public function addArticleSection(Request $request)
    {
        DB::beginTransaction();
        try {

            if ($request->has('article_heading') != null) {
                foreach ($request->article_heading as $key => $val) {
                    $article_section = ArticleSection::find($key);
                    $article_section->heading = $val;
                    $article_section->update();
                }
            }

            if ($request->has('article_description') != null) {
                foreach ($request->article_description as $key => $val) {
                    $article_section = ArticleSection::find($key);
                    $article_section->description = $val;
                    $article_section->update();
                }
            }

            if ($request->has('new_article_heading') != null) {
                foreach ($request->new_article_heading as $heading) {
                    $article_section = new ArticleSection;
                    $article_section->heading = $heading;
                    $article_section->save();
                }
            }

            if ($request->has('new_article_description') != null) {
                foreach ($request->new_article_description as $description) {
                    $article_section = new ArticleSection;
                    $article_section->description = $description;
                    $article_section->save();
                }
            }

            if ($request->has('example_section_heading')) {
                $example_section = ArticleSection::where('key', 'example_section_heading')->first();
                $example_section->heading = $request->example_section_heading;
                $example_section->update();
            }

            if ($request->has('example_section_description1')) {
                $example_section = ArticleSection::where('key', 'example_section_description1')->first();
                $example_section->description = $request->example_section_description1;
                $example_section->update();
            }

            if ($request->has('example_section_description2')) {
                $example_section = ArticleSection::where('key', 'example_section_description2')->first();
                $example_section->description = $request->example_section_description2;
                $example_section->update();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data Updated Successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    // public function updateDocumentImage(Request $request)
    // {
    //     $document = Document::find($request->id);

    //     if (!$document) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Document not found.'
    //         ], 404);
    //     }

    //     $nameOnImage = is_array($request->image_name)
    //         ? implode('@', array_filter($request->image_name))
    //         : $request->image_name;

    //     $document->name_on_image = $nameOnImage;

    //     $imagePath = $document->getRawOriginal('document_image');
    //     if (!empty($nameOnImage) && $imagePath && File::exists($imagePath)) {
    //         File::delete($imagePath);
    //     }

    //     if (!empty($nameOnImage)) {
    //         $document->document_image = $this->IMG->addTextToImage($nameOnImage);
    //     }

    //     $document->update();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Successfully generated new svg'
    //     ]);
    // }

    // ADD this method to handle document image updates
    public function updateDocumentImage(Request $request)
    {
        try {
            $document = Document::find($request->id);

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found.'
                ], 404);
            }

            $nameOnImage = is_array($request->image_name)
                ? implode('@', array_filter($request->image_name))
                : $request->image_name;

            $document->name_on_image = $nameOnImage;

            // Delete old image if exists
            $imagePath = $document->getRawOriginal('document_image');
            if (!empty($nameOnImage) && $imagePath && File::exists($imagePath)) {
                File::delete($imagePath);
            }

            //  Generate new image
            if (!empty($nameOnImage)) {
                $document->document_image = $this->IMG->addTextToImage($nameOnImage);
            }

            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully',
                'image_url' => $document->document_image
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function documentGenerator(Request $request)
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

        if (isset($request->id) && $request->id != null) {
            $document = Document::find($request->id);
            $slug = $document->slug;

            $aiModelRefs = Setting::where('type', 'ai')
                ->whereNotNull('model_ref')
                ->distinct()
                ->pluck('model_ref');

            $document_generator = DocumentGenerator::where('document_id', $request->id)->first();

            $standardDocument = StandardDocument::where('type', 'global')->get();

            $recommendedSection = RecommendedSection::where('document_id', $request->id)->where('status', 1)->with('standard_section')->get();
            $recommendedSectionIds = $recommendedSection->pluck('standard_section_id')->map(fn($id) => (string) $id);
            // dd($recommendedSectionIds);

            $questions = Question::where('document_id', $request->id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
                ->get();

            $questionIds = $questions->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $sections = DocumentRightSection::where('document_id', $document->id)->with('conditions')->get();
            $standard_section_Ids = $sections->pluck('standard_section_id')
                ->filter()
                ->unique()
                ->map(fn($id) => (string) $id);

            $standardDocuments = collect();

            $ids = $standard_section_Ids->toArray();

            if (count($ids) > 0) {
                $standardDocuments = StandardDocument::whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, " . implode(',', $ids) . ")")
                    ->get();
            } else {
                $standardDocuments = collect();
            }

            $resultSections = [];

            foreach ($sections as $section) {
                $content = $section->content;

                preg_match_all('/\{(\d+)\}/', $content, $matches);

                $matchedQids = $matches[1] ?? [];

                $matchedQids = array_filter($matchedQids, function ($qid) use ($questionIds) {
                    return in_array($qid, $questionIds);
                });

                $matchedQuestions = [];
                foreach ($matchedQids as $qid) {
                    $q = $questions->firstWhere('id', (int)$qid);
                    if ($q) {
                        $matchedQuestions[] = [
                            'id'    => $q->id,
                            'questions' => $q,
                        ];
                    }
                }

                if (count($matchedQuestions) > 0) {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                } else {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                }
            }

            $usedQids = collect($resultSections)->pluck('questions')->flatten(1)->pluck('id')->toArray();
            $standaloneQuestions = $questions->whereNotIn('id', $usedQids);

            foreach ($standaloneQuestions as $q) {
                $resultSections[] = [
                    'section_id' => null,
                    'text'       => null,
                    'questions'  => [
                        [
                            'id'    => $q->id,
                            'questions' => $q,
                        ]
                    ],
                    'section_id' => null,
                    'type'       => null,
                    'text_align' => null,
                    'text_alignment' => null,
                    'is_condition' => null,
                    'conditions' => null,
                    'blurr_content' => null,
                    'content2' => null,
                    'content3' => null,
                    'standard_section_id' => null,
                ];
            }

            $types = QuestionType::all();
        }

        // dd(['document' => $document,'aiModelRefs' => $aiModelRefs,'document_generator' => $document_generator,'slug' => $slug,'standardDocument' => $standardDocument,'recommendedSection' => $recommendedSection,'questions' => $questions,'types' => $types,'recommendedSectionIds' => $recommendedSectionIds,'resultSections' => $resultSections, 'standardDocuments' => $standardDocuments ]);
        return view('admin.documents.document_generator', compact('document', 'aiModelRefs', 'document_generator', 'slug', 'standardDocument', 'recommendedSection', 'questions', 'types', 'recommendedSectionIds', 'resultSections', 'standardDocuments'));
    }

    public function documentGenerateProcess(Request $request)
    {
        // DB::beginTransaction();
        try {
            $prompt = Prompt::where([['key', 'document_generator'], ['location', 'document']])->first();
            $second_prompt = Prompt::where([['key', 'initial_document_generation'], ['location', 'document']])->first();

            $prompt_ai_model = $prompt?->prompt_ai_model ?? '';

            $document = Document::find($request->document_id);
            if (!$document) {
                $document = new Document();
                $document->title = $request->document_name;
                $document->slug = Str::slug($request->document_name);
                $document->save();
            }

            $document_generator_prompt = $prompt?->updated_prompt ?? '';
            $document_generator_prompt2 = $second_prompt?->updated_prompt ?? '';
            $ai_verification_model = $prompt?->ai_verification_model ?? '';

            $promptVerification = PromptVerification::first();
            $verification_prompt = $promptVerification?->ai_prompt ?? '';

            $language = web_setting('language')->value;
            $country = web_setting('country')->value;
            $currency = web_setting('country_currency')->value;

            $minPrompt = Prompt::where([['key', 'minimum_requirements'], ['location', 'document']])->first();
            $minimum_requirements = $minPrompt->updated_prompt;
            $validPrompt = Prompt::where([['key', 'validation_rules'], ['location', 'document']])->first();
            $validation_rules = $validPrompt->updated_prompt;

            $finalPrompt = str_replace('{document_name}', $document->title, $document_generator_prompt);
            $finalPrompt = str_replace('{language}', $language, $finalPrompt);
            $finalPrompt = str_replace('{country}', $country, $finalPrompt);
            $finalPrompt = str_replace('{currency}', $currency, $finalPrompt);

            if (!empty($request->additional_information)) {
                $finalPrompt .= "\n\nAdditional Information: " . $request->additional_information;
            }

            if (!empty($minimum_requirements)) {
                $finalPrompt .= "\n\nMinimum Requirements: " . $minimum_requirements;
            }

            if (!empty($validation_rules)) {
                $finalPrompt .= "\n\nValidation Rules: " . $validation_rules;
            }

            $filename = '';
            $relativePath = '';

            if ($request->hasFile('fileInput')) {
                $file = $request->file('fileInput');

                if ($file->isValid()) {
                    $filename = generateFileName($file);
                    $destinationPath = public_path('storage/document_generator');

                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $file->move($destinationPath, $filename);

                    $relativePath = 'storage/document_generator/' . $filename;
                    $publicUrl = asset($relativePath);

                    $finalPrompt .= "\n\nImage here: " . $publicUrl;
                }
            }

            if (!empty($document_generator_prompt2)) {
                $finalPrompt2 = str_replace('{document_name}', $request->document_name, $document_generator_prompt2);
                $finalPrompt2 = str_replace('{language}', $language, $finalPrompt2);
                $finalPrompt2 = str_replace('{country}', $country, $finalPrompt2);

                $finalPrompt .= "\n\nInitial Document Generation: " . $finalPrompt2;
            }

            $document_generator = DocumentGenerator::where([['id', $request->id], ['document_id', $document->id]])->first() ?? new DocumentGenerator;

            $aiOutput = null;
            $decoded = null;

            $aiService = new AIService($prompt_ai_model);
            $aiOutput = $aiService->generateDocumentQuestionAndText($finalPrompt);
            return $aiOutput;

            if ($prompt_ai_model === 'Gemini 2.0' || $prompt_ai_model === 'Gemini 2.5 pro') {
                $aiService = new AIService($prompt_ai_model);
                saveLog("gemini:", "DocumentController", $finalPrompt);
                $aiOutput = $aiService->generateDocumentQuestionAndText($finalPrompt);
                $document_generator->ai_response = json_encode($aiOutput);
                saveLog("Document Generator Output:", "DocumentController", $aiOutput);
            } elseif ($prompt_ai_model === 'chatgpt') {
                $aiService = new AIService($prompt_ai_model);
                saveLog("chatgpt:", "DocumentController", $finalPrompt);
                $aiOutput = $aiService->generateDocumentQuestionAndTextWithOpenAI($finalPrompt);
                $document_generator->ai_response = json_encode($aiOutput);
                saveLog("Document Generator Output:", "DocumentController", $aiOutput);
            }

            $document_generator->document_id = $document->id;
            $document_generator->document_name = $document->title ?? $document_generator->document_name;
            $document_generator->additional_information = $request->additional_information;
            $document_generator->is_verified = $request->is_verified ?? 0;

            if ($document_generator->exists && !empty($document_generator->file_path)) {
                $oldFilePath = public_path($document_generator->file_path);
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }
            $document_generator->file_name = $filename;
            $document_generator->file_path = $relativePath;
            $document_generator->save();

            // DB::commit();

            return response()->json([
                'status' => true,
                'document_id' => $document->id,
                'id' => $document_generator->id,
                'ai_model' => $prompt_ai_model,
                'message' => 'Prompt sent successfully. Please wait while we process the response.',
            ]);
        } catch (Exception $e) {
            // DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while saving. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveDocumentGeneratorData(Request $request)
    {
        DB::beginTransaction();
        try {
            $document_id = $request->document_id;
            $ai_model = $request->ai_model;
            $id = $document_id;

            $document = Document::find($document_id);
            if (!$document) {
                return response()->json([
                    'status' => false,
                    'message' => 'Document not found.',
                ], 404);
            }
            $document_generator = DocumentGenerator::where([['id', $request->id], ['document_id', $document->id]])->first();
            $aiOutput = $document_generator ? json_decode($document_generator->ai_response, true) : null;

            if ($ai_model === 'Gemini 2.0' || $ai_model === 'Gemini 2.5 pro') {
                $cleanedOutput = trim($aiOutput);
                $cleanedOutput = preg_replace('/^(json|```json|```)\s*/i', '', $cleanedOutput);
                $cleanedOutput = preg_replace('/```$/', '', $cleanedOutput);

                $decoded = json_decode($cleanedOutput, true);

                if (!is_array($decoded) || empty($decoded)) {
                    saveLog("Error:", "DocumentController", $aiOutput);

                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid AI response format. Root must be a valid JSON object.',
                        'raw' => $cleanedOutput
                    ], 422);
                }

                $firstSection = reset($decoded);

                if (!is_array($firstSection)) {
                    saveLog("Error:", "DocumentController", $aiOutput);

                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid AI response format. Section must be an object.',
                        'raw' => $cleanedOutput
                    ], 422);
                }
            } elseif ($ai_model === 'chatgpt') {
                $cleanedOutput = trim($aiOutput);
                $cleanedOutput = preg_replace('/^```(?:json)?\s*/mi', '', $cleanedOutput);
                $cleanedOutput = preg_replace('/```$/m', '', $cleanedOutput);
                $cleanedOutput = preg_replace('/^#+.*\n/m', '', $cleanedOutput);

                preg_match('/\{(?:[^{}]|(?R))*\}/s', $cleanedOutput, $matches);
                $jsonPart = $matches[0] ?? null;

                if (!$jsonPart) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No valid JSON found in AI output.',
                        'raw' => $aiOutput
                    ], 422);
                }

                $jsonPart = preg_replace('/,\s*([}\]])/', '$1', $jsonPart);
                $jsonPart = trim($jsonPart);
                $decoded = json_decode($jsonPart, true);
            }

            $is_questions = Question::where('document_id', $document->id)->get();

            foreach ($is_questions as $q) {
                $q->questionData()->delete();
                $q->conditions()->each(function ($condition) {
                    $condition->subconditions()->delete();
                    $condition->delete();
                });
                $q->options()->delete();
                $q->nextQuestion()->delete();
                $q->delete();
            }

            $is_document_right_section = DocumentRightSection::where('document_id', $document->id)->get();
            foreach ($is_document_right_section as $s) {
                $s->conditions()->delete();
                $s->delete();
            }

            $is_standard_section = StandardDocument::where('document_id', $document->id)->get();

            foreach ($is_standard_section as $section) {
                $section->delete();
            }

            if ($decoded != null && is_array($decoded)) {
                foreach ($decoded as $sectionBlocks) {
                    $standardDocumentID = '';
                    $questionData = $sectionBlocks['Questionnaire'] ?? [];
                    $rightContentData = $sectionBlocks['Contract_Text'] ?? [];

                    $qidToRealIdMap = [];
                    $questionModels = [];
                    $questionDataModels = [];

                    foreach ($questionData as $qid => $question) {
                        if (!is_array($question)) continue;

                        if (isset($question['TYPE'])) {
                            $type = match ($question['TYPE']) {
                                'RADIOBUTTON'   => 'radio-button',
                                'DATEFIELD'     => 'date-field',
                                'NUMBERFIELD'   => 'number-field',
                                'PERCENTAGEBOX' => 'percentage-box',
                                default         => strtolower($question['TYPE']),
                            };
                        }

                        $section_name = $question['section_name'];

                        if (isset($question['section_name'])) {
                            $standardDocument = StandardDocument::where('title', $section_name)
                                ->where('document_id', $id)
                                ->first();
                            if ($standardDocument) {
                                $standardDocumentID = $standardDocument->id;
                            } else {
                                $standardDocument = new StandardDocument;
                                $standardDocument->title = $section_name;
                                $standardDocument->slug = Str::slug($section_name, '-');
                                $standardDocument->type = 'document';
                                $standardDocument->document_id = $id;
                                $standardDocument->save();

                                $standardDocumentID = $standardDocument->id;
                            }
                        }

                        $qId = preg_replace('/[^0-9]/', '', $qid);
                        $questionModel = new Question();
                        $questionModel->document_id = $id;
                        $questionModel->standard_section_id = $standardDocumentID ?? null;
                        $questionModel->type = $type;

                        $lastOrder = Question::where('document_id', $id)
                            ->orderBy('order_id', 'desc')
                            ->first();

                        $questionModel->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;

                        if (!empty($question['condition_type'])) {
                            $questionModel->is_condition = 1;
                            $questionModel->condition_type = $question['condition_type'];
                        }

                        $questionModel->is_end = isset($question['goto']) && $question['goto'] === 'END' ? 1 : 0;
                        $questionModel->save();

                        $qidToRealIdMap[$qId] = $questionModel->id;
                        $questionModels[$qId] = $questionModel;

                        $questionDataModels[$qId] = [
                            'label' => $question['label'] ?? null,
                            'userinfo' => $question['userinfo'] ?? null,
                            'placeholder' => $question['placeholder'] ?? null,
                            'goto' => $question['goto'] ?? null,
                            'goto_if' => $question['goto_if'] ?? [],
                            'options' => $question['options'] ?? null,
                            'condition_type' => $question['condition_type'] ?? null,
                            'another_go_to_step' => $question['another_go_to_step'] ?? [],
                            'question_label_condition' => $question['question_label_condition'] ?? [],
                            'conditional_go_to_step' => $question['conditional_go_to_step'] ?? null,
                        ];
                    }

                    foreach ($questionDataModels as $q_id => $data) {
                        $goto = $data['goto'] ?? null;
                        $gotoClean = preg_replace('/[^0-9]/', '', $goto);
                        $nextQuestionId = $goto && $goto !== 'END' ? ($qidToRealIdMap[$gotoClean] ?? null) : null;

                        $questionDataModel = new QuestionData();
                        $questionDataModel->question_id = $qidToRealIdMap[$q_id];
                        $questionDataModel->question_label = $data['label'];
                        $questionDataModel->question_info_text = $data['userinfo'];
                        $questionDataModel->text_box_placeholder = $data['placeholder'];
                        $questionDataModel->next_question_id = $nextQuestionId;

                        if (!empty($data['options'])) {
                            $order = 1;
                            foreach ($data['options'] as $opt) {
                                $opt = (array)$opt;
                                $option = new MultipleChoiceQuestionOption();
                                $option->question_id = $qidToRealIdMap[$q_id];
                                $option->option_label = $opt['option_label'] ?? '';
                                $option->option_value = $opt['option_value'] ?? '';

                                $nextQId = null;
                                if (!empty($opt['go_next_step']) && $opt['go_next_step'] !== 'END') {
                                    if (preg_match('/QID(\d+)/', $opt['go_next_step'], $match)) {
                                        $nextQId = $qidToRealIdMap[$match[1]] ?? null;
                                    }
                                }

                                $option->next_question_id = $nextQId;
                                $option->order_id = $order++;
                                $option->save();
                            }
                        }

                        if ($data['condition_type'] == "1") {
                            $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                            foreach ($labels as $labelCondition) {
                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$q_id];
                                $qc->condition_type = 'question_label_condition';
                                $qc->question_label = $labelCondition['label'] ?? '';

                                $condQid = null;
                                if (!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)) {
                                    $condQid = $qidToRealIdMap[$match[1]] ?? null;
                                }

                                $qc->conditional_question_id = $condQid;
                                $qc->conditional_question_value = $labelCondition['value'] ?? '';
                                $qc->save();
                            }
                        }

                        if ($data['condition_type'] == "2") {
                            $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                            $goToStepTarget = null;

                            if (isset($data['conditional_go_to_step'])) {
                                if (preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)) {
                                    $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                                }
                            }

                            foreach ($gotoIfConditions as $condition) {
                                if (isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)) {
                                    $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                                    $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                                    $checkType = match ($operatorText) {
                                        'is equal to' => 1,
                                        'is greater than' => 2,
                                        'is less than' => 3,
                                        'is not equal to' => 4,
                                        default => 1,
                                    };

                                    $qc = new QuestionCondition();
                                    $qc->question_id = $qidToRealIdMap[$q_id];
                                    $qc->condition_type = 'go_to_step_condition';
                                    $qc->conditional_question_id = $checkQid;
                                    $qc->conditional_question_value = $condition['question_value'] ?? '';
                                    $qc->conditional_check = $checkType;
                                    $qc->save();
                                }
                            }

                            if ($goToStepTarget) {
                                $questionDataModel->conditional_go_to_step = $goToStepTarget;
                            }

                            $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                            foreach ($another_go_to_step as $index => $cond) {
                                $subGoToStep = null;
                                if (isset($cond['conditional_go_to_step'])) {
                                    if (preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)) {
                                        $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                                    }
                                }

                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$q_id];
                                $qc->condition_type = 'another_go_to_step_condition';
                                $qc->go_to_step = $subGoToStep;
                                $qc->save();

                                $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                                foreach ($subConditions as $subC) {
                                    $sub = new SubCondition();
                                    $sub->question_condition_id = $qc->id;

                                    if (isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)) {
                                        $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                                    }

                                    $sub->conditional_question_value = $subC['question_value'] ?? null;

                                    $checkType = match (strtolower(trim($subC['conditions'] ?? 'is equal to'))) {
                                        'is equal to' => 1,
                                        'is greater than' => 2,
                                        'is less than' => 3,
                                        'is not equal to' => 4,
                                        default => 1,
                                    };

                                    $sub->conditional_check = $checkType;
                                    $sub->save();
                                }
                            }
                        }

                        if ($data['condition_type'] == "3") {
                            $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                            foreach ($labels as $labelCondition) {
                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$q_id];
                                $qc->condition_type = 'question_label_condition';
                                $qc->question_label = $labelCondition['label'] ?? '';

                                $condQid = null;
                                if (!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)) {
                                    $condQid = $qidToRealIdMap[$match[1]] ?? null;
                                }

                                $qc->conditional_question_id = $condQid;
                                $qc->conditional_question_value = $labelCondition['value'] ?? '';
                                $qc->save();
                            }

                            $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                            $goToStepTarget = null;


                            if (isset($data['conditional_go_to_step'])) {
                                if (preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)) {
                                    $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                                }
                            }


                            foreach ($gotoIfConditions as $condition) {
                                if (isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)) {
                                    $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                                    $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                                    $checkType = match ($operatorText) {
                                        'is equal to' => 1,
                                        'is greater than' => 2,
                                        'is less than' => 3,
                                        'is not equal to' => 4,
                                        default => 1,
                                    };

                                    $qc = new QuestionCondition();
                                    $qc->question_id = $qidToRealIdMap[$qid];
                                    $qc->condition_type = 'go_to_step_condition';
                                    $qc->conditional_question_id = $checkQid;
                                    $qc->conditional_question_value = $condition['question_value'] ?? '';
                                    $qc->conditional_check = $checkType;
                                    $qc->save();
                                }
                            }

                            if ($goToStepTarget) {
                                $questionDataModel->conditional_go_to_step = $goToStepTarget;
                            }

                            $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                            foreach ($another_go_to_step as $index => $cond) {
                                $subGoToStep = null;
                                if (isset($cond['conditional_go_to_step'])) {
                                    if (preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)) {
                                        $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                                    }
                                }

                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$qid];
                                $qc->condition_type = 'another_go_to_step_condition';
                                $qc->go_to_step = $subGoToStep;
                                $qc->save();

                                $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                                foreach ($subConditions as $subC) {
                                    $sub = new SubCondition();
                                    $sub->question_condition_id = $qc->id;

                                    if (isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)) {
                                        $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                                    }

                                    $sub->conditional_question_value = $subC['question_value'] ?? null;

                                    $checkType = match (strtolower(trim($subC['conditions'] ?? 'is equal to'))) {
                                        'is equal to' => 1,
                                        'is greater than' => 2,
                                        'is less than' => 3,
                                        'is not equal to' => 4,
                                        default => 1,
                                    };

                                    $sub->conditional_check = $checkType;
                                    $sub->save();
                                }
                            }
                        }

                        $questionDataModel->save();
                    }

                    foreach ($rightContentData as $tid => $content) {
                        if (!is_array($content)) continue;

                        if (isset($content['TYPE'])) {
                            $type = match ($content['TYPE']) {
                                'HEADLINE'  => 'content_heading',
                                'CONTENT'   => 'content',
                                'SIGNATURE' => 'signature_field',
                                default     => strtolower($content['TYPE']),
                            };
                        } else {
                            $type = null;
                        }

                        $section_name = $content['section_name'];

                        if (isset($content['section_name'])) {
                            $standardDocument = StandardDocument::where('title', $section_name)
                                ->where('document_id', $id)
                                ->first();
                            if ($standardDocument) {
                                $standardDocumentID = $standardDocument->id;
                            } else {
                                $standardDocument = new StandardDocument;
                                $standardDocument->title = $section_name;
                                $standardDocument->slug = Str::slug($section_name, '-');
                                $standardDocument->type = 'document';
                                $standardDocument->document_id = $id;
                                $standardDocument->save();

                                $standardDocumentID = $id;
                            }
                        }
                        $text = $content['TEXT'] ?? '';
                        $text = preg_replace_callback('/\{QID(\d+)\}/', function ($matches) use ($qidToRealIdMap) {
                            $originalQid = $matches[1];
                            return isset($qidToRealIdMap[$originalQid]) ? '{' . $qidToRealIdMap[$originalQid] . '}' : $matches[0];
                        }, $text);

                        $secure_blur_content = isset($content['BLUR_CONTENT']) && $content['BLUR_CONTENT'] ? 1 : 0;
                        $is_signature = ($type === 'signature_field') ? 1 : 0;
                        $is_condition = (!empty($content['CONDITIONS'])) ? 1 : 0;

                        $document_right_section = new DocumentRightSection();
                        $document_right_section->type = $type;
                        $document_right_section->document_id = $id;
                        $document_right_section->standard_section_id = $standardDocumentID ?? null;

                        $lastOrder = DocumentRightSection::where('document_id', $id)
                            ->orderBy('order_id', 'desc')
                            ->first();
                        $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;

                        $document_right_section->content = $text;
                        $document_right_section->text_align = $content['ALIGN_TEXT'] ?? 'left';
                        $document_right_section->is_condition = $is_condition;
                        $document_right_section->signature_field = $is_signature;
                        $document_right_section->secure_blur_content = $secure_blur_content;
                        $document_right_section->save();

                        if (!empty($content['CONDITIONS'])) {
                            foreach ($content['CONDITIONS'] as $condition) {
                                $condition = (array)$condition;

                                $checkType = match (strtolower(trim($condition['conditions'] ?? 'is equal to'))) {
                                    'is equal to'   => 1,
                                    'is greater than' => 2,
                                    'is less than'  => 3,
                                    'not equal to'  => 4,
                                    default         => 1,
                                };

                                $questionId = null;
                                if (!empty($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $matches)) {
                                    $questionId = $qidToRealIdMap[$matches[1]] ?? null;
                                }

                                $condition_type = match ($type) {
                                    'content'        => 'content_condition',
                                    'signature_field' => 'signature_field',
                                    default          => 'content_condition',
                                };

                                if ($questionId !== null) {
                                    $documentCondition = new QuestionCondition();
                                    $documentCondition->condition_type = $condition_type;
                                    $documentCondition->document_right_content_id = $document_right_section->id;
                                    $documentCondition->conditional_question_id = $questionId;
                                    $documentCondition->conditional_check = $checkType;
                                    $documentCondition->conditional_question_value = $condition['question_value'] ?? '';
                                    $documentCondition->save();
                                }
                            }
                        }
                    }
                }
            }

            $document_generator->ai_status = 2;
            $document_generator->update();

            $questions = Question::where('document_id', $document->id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
                ->get();

            // $questionIds = $questions->pluck('id')->map(fn($id) => (string) $id)->toArray(); 
            // $sections = DocumentRightSection::where('document_id', $document->id)->with('conditions')->get();
            // $standard_section_Ids = $questions->pluck('standard_section_id')
            // ->merge($sections->pluck('standard_section_id'))
            // ->filter() 
            // ->unique()
            // ->map(fn($id) => (string) $id);

            // $standardDocuments = StandardDocument::whereIn('id', $standard_section_Ids)->get();

            $questionIds = $questions->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $sections = DocumentRightSection::where('document_id', $document->id)->with('conditions')->get();
            $standard_section_Ids = $sections->pluck('standard_section_id')
                ->filter()
                ->unique()
                ->map(fn($id) => (string) $id);

            $standardDocuments = collect();

            $ids = $standard_section_Ids->toArray();

            if (count($ids) > 0) {
                $standardDocuments = StandardDocument::whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, " . implode(',', $ids) . ")")
                    ->get();
            } else {
                $standardDocuments = collect();
            }

            $resultSections = [];

            foreach ($sections as $section) {
                $content = $section->content;
                preg_match_all('/\{(\d+)\}/', $content, $matches);

                $matchedQids = $matches[1] ?? [];

                $matchedQids = array_filter($matchedQids, function ($qid) use ($questionIds) {
                    return in_array($qid, $questionIds);
                });

                $matchedQuestions = [];
                foreach ($matchedQids as $qid) {
                    $q = $questions->firstWhere('id', (int)$qid);
                    if ($q) {
                        $matchedQuestions[] = [
                            'id'    => $q->id,
                            'questions' => $q,
                        ];
                    }
                }

                if (count($matchedQuestions) > 0) {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                } else {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                }
            }

            $usedQids = collect($resultSections)->pluck('questions')->flatten(1)->pluck('id')->toArray();
            $standaloneQuestions = $questions->whereNotIn('id', $usedQids);

            foreach ($standaloneQuestions as $q) {
                $resultSections[] = [
                    'section_id' => null,
                    'text'       => null,
                    'questions'  => [
                        [
                            'id'    => $q->id,
                            'questions' => $q,
                        ]
                    ],
                    'section_id' => null,
                    'type'       => null,
                    'text_align' => null,
                    'text_alignment' => null,
                    'is_condition' => null,
                    'conditions' => null,
                    'blurr_content' => null,
                    'content2' => null,
                    'content3' => null,
                    'standard_section_id' => null,
                ];
            }

            $types = QuestionType::all();

            DB::commit();

            $html = view('admin.documents.partial.step3', compact(
                'questions',
                'resultSections',
                'types',
                'standardDocuments'
            ))->render();

            return response()->json([
                'status' => true,
                'document_id' => $document->id,
                'message' => 'Contract generated successfully.',
                'html' => $html
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while saving. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generatePDFImage($id)
    {
        $questions = Question::where('document_id', $id)
            ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
            ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
            ->get();

        $total_questions = count($questions);

        $documentContents = DocumentRightSection::where('document_id', $id)
            ->with(['conditions', 'document'])
            ->orderBy('order_id', 'asc')
            ->orderByRaw('order_id IS NULL')
            ->get();

        $alphabet = range('a', 'z');
        $abclistCounters = [];

        foreach ($documentContents as $content) {
            $content->show = true;

            if ($content->is_condition && $content->conditions && count($content->conditions)) {
                $is_elem_show = true;

                foreach ($content->conditions as $condition) {
                    $questionId = $condition->conditional_question_id;
                    $expectedValue = $condition->conditional_question_value;
                    $checkType = $condition->conditional_check;

                    $matchedQuestion = $questions->firstWhere('id', $questionId);
                    $actualValue = $matchedQuestion->questionData->user_answer ?? null;

                    if ($checkType == '1' && $actualValue != $expectedValue) $is_elem_show = false;
                    elseif ($checkType == '2' && $actualValue <= $expectedValue) $is_elem_show = false;
                    elseif ($checkType == '3' && $actualValue >= $expectedValue) $is_elem_show = false;
                    elseif ($checkType == '4' && $actualValue == $expectedValue) $is_elem_show = false;

                    if (!$is_elem_show) break;
                }

                $content->show = $is_elem_show;
            }

            if (!$content->show) continue;

            $content->content = preg_replace_callback(
                '/#(\d+)#|{(\d+)}/',
                fn($matches) => "<span class=\"answered_spns qidtarget-" . (!empty($matches[1]) ? $matches[1] : $matches[2]) . "\">_______</span>",
                $content->content
            );

            if ($content->type == 'signature_field') {
                foreach (['content2', 'content3'] as $field) {
                    $content->$field = preg_replace_callback(
                        '/#(\d+)#|{(\d+)}/',
                        fn($matches) => "<span class=\"answered_spns qidtarget-" . (!empty($matches[1]) ? $matches[1] : $matches[2]) . "\">_______</span>",
                        $content->$field ?? ''
                    );
                }
            }

            $content->content = preg_replace_callback(
                '/{abclist(\d+)}/',
                function ($matches) use (&$abclistCounters, $alphabet) {
                    $listNumber = $matches[1];
                    $index = $abclistCounters[$listNumber] ?? 0;
                    $char = $alphabet[$index] ?? '';
                    $abclistCounters[$listNumber] = $index + 1;
                    return "<span class=\"abclist abclist{$listNumber}\">{$char}</span>";
                },
                $content->content
            );
        }

        $data = [
            'documentContents' => $documentContents,
            'questions' => $questions,
            'id' => $id,
            'total_questions' => $total_questions
        ];

        $pdf = PDF::loadView('users.contracts_pdf.preview_pdf_image', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdfPath = public_path('temp_pdf.pdf');
        $pdf->save($pdfPath);

        $previewDir = public_path('preview_images');
        if (!File::exists($previewDir)) {
            File::makeDirectory($previewDir, 0755, true);
        }

        $thumbnailPath = $previewDir . '/document_' . $id . '.png';

        if (File::exists($thumbnailPath)) {
            File::delete($thumbnailPath);
        }

        $cmd = "pdftoppm -png -f 1 -singlefile -rx 150 -ry 150 -scale-to 400 {$pdfPath} {$previewDir}/document_{$id}";

        exec($cmd, $output, $returnVar);

        Log::debug('pdftoppm output:', $output);
        Log::debug("Return var: $returnVar");
        // \Log::debug("Thumbnail path exists? " . (File::exists($thumbnailPath) ? 'yes' : 'no'));

        // chmod($thumbnailPath, 0644);

        if (File::exists($thumbnailPath)) {
            Log::debug("Thumbnail created: $thumbnailPath");
            chmod($thumbnailPath, 0644);
        } else {
            Log::error("Thumbnail not created at: $thumbnailPath");
        }

        File::delete($pdfPath);

        return response()->file($thumbnailPath);
    }

    private function encryptText($text, $key)
    {
        $cipherMethod = "AES-256-CBC";
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipherMethod)); // Generate a secure IV
        // Encrypt the text
        $encryptedText = openssl_encrypt($text, $cipherMethod, $key, 0, $iv);
        // Encode IV with the encrypted text
        return base64_encode($iv . $encryptedText);
    }

    public function saveGeneratedAiImage(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->id) {
                $document_field = DocumentsField::find($request->id);

                if ($document_field) {
                    if ($document_field->media_id) {
                        $media = Media::find($document_field->media_id);

                        if ($media && File::exists(storage_path('app/public/' . $media->file_path))) {
                            $image_path = storage_path('app/public/' . $media->file_path);
                            $directory_path = dirname($image_path);

                            unlink($image_path);

                            if (is_dir($directory_path) && count(scandir($directory_path)) == 2) {
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id', $document_field->media_id)->delete();
                        $document_field->media_id = null;
                    }

                    if ($request->media_id) {
                        $document_field->media_id = $request->media_id;
                    }

                    $document_field->save();
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'AI-generated image saved successfully.'
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Document field not found.'
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Invalid request. ID is missing.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving AI-generated image: ' . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while saving the image.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function graphicalInterface(Request $request)
    {
        $document_questions = Question::where('document_id', $request->id)
            ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
            ->get();


        $questionIds = $document_questions->pluck('id')->map(fn($id) => (string) $id);

        $contract = DocumentRightSection::where('document_id', $request->id)
            ->pluck('content')
            ->implode("\n");

        preg_match_all('/([^#{}\n]*)[#\{](\d+)[#\}](?!\d)([^#{}\n]*)/', $contract, $matches, PREG_SET_ORDER);

        $questionContractMap = [];

        foreach ($matches as $match) {
            $qid = $match[2];

            if ($questionIds->contains($qid)) {
                $text = trim(($match[1] ?? '') . '{' . $qid . '}' . ($match[3] ?? ''));
                $questionContractMap[$qid] = $text;
            }
        }

        // dd($questionContractMap);

        return view('admin.documents.flowchart', compact('document_questions', 'questionContractMap'));
    }

    public function sendFeedbackToAi(Request $request)
    {
        DB::beginTransaction();
        try {
            $prompt = Prompt::where([['key', 'document_generator'], ['location', 'document']])->first();
            $document_generator_prompt = $prompt?->updated_prompt ?? '';
            $prompt_ai_model = $prompt?->prompt_ai_model ?? '';
            $ai_verification_model = $prompt?->ai_verification_model ?? '';
            $verification_prompt = $request->verification_prompt;

            $globalQuestions = GlobalContractQuestion::with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
                ->get()
                ->map(function ($question) {
                    return json_encode([
                        "QID{$question->id}" => $question->toPromptFormat()
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                })->implode(",\n");


            $globalTexts = GlobalContractText::with('conditions')
                ->orderBy('order_id', 'asc')
                ->get()
                ->map(function ($text) {
                    return json_encode([
                        "TID{$text->id}" => $text->toPromptFormat()
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                })->implode(",\n");

            $id = $request->id;
            $document = Document::find($id);
            $document_name = $document->title;

            $language = web_setting('language')->value;
            $country = web_setting('country')->value;
            // $minimum_requirements = web_setting('minimum_requirements')->value;
            // $validation_rules = web_setting('validation_rules')->value;
            $minPrompt = Prompt::where([['key', 'minimum_requirements'], ['location', 'document']])->first();
            $minimum_requirements = $minPrompt->updated_prompt;
            $validPrompt = Prompt::where([['key', 'validation_rules'], ['location', 'document']])->first();
            $validation_rules = $validPrompt->updated_prompt;


            $finalPrompt = str_replace('{document_name}', $request->document_name, $document_generator_prompt);
            $finalPrompt = str_replace('{global_questions}', $globalQuestions, $finalPrompt);
            $finalPrompt = str_replace('{global_texts}', $globalTexts, $finalPrompt);
            $finalPrompt = str_replace('{language}', $language, $finalPrompt);
            $finalPrompt = str_replace('{country}', $country, $finalPrompt);

            if (!empty($request->additional_information)) {
                $finalPrompt .= "\n\nAdditional Information: " . $request->additional_information;
            }

            if (!empty($minimum_requirements)) {
                $finalPrompt .= "\n\nMinimum Requirements: " . $minimum_requirements;
            }

            if (!empty($validation_rules)) {
                $finalPrompt .= "\n\nValidation Rules: " . $validation_rules;
            }

            $document_questions = Question::where('document_id', $id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->get();

            $document_text = DocumentRightSection::where('document_id', $id)
                ->with(['conditions'])
                ->get();

            if (!empty($request->feedback)) {
                $finalPrompt .= "\n\n### Feedback:\n" . $request->feedback;
            }

            if (!empty($document_questions)) {
                $finalPrompt .= "\n\n### Questionnaire:\n" . $document_questions;
            }

            if (!empty($document_text)) {
                $finalPrompt .= "\n\n### Contract_Text:\n" . $document_text;
            }

            if (!empty($document_name)) {
                $finalPrompt .= "\n\n### Task:\nPlease review the Questionnaire and Contract_Text and make changes based on the following title:\n" . $document_name;
            }

            $aiService = new AIService();
            $result = $aiService->generateQuestionsAndTextByFeedback($finalPrompt);
            $aiOutput = $result;

            $cleanedOutput = trim($aiOutput);
            $cleanedOutput = preg_replace('/^(json|```json|```)\s*/i', '', $cleanedOutput);
            $cleanedOutput = preg_replace('/```$/', '', $cleanedOutput);

            $decoded = json_decode($cleanedOutput, true);

            if (!is_array($decoded) || !isset($decoded['Questionnaire']) || !isset($decoded['Contract_Text'])) {
                saveLog("Error:", "DocumentController",  $aiOutput);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid AI response format. Both Questionnaire and Contract_Text keys are required.',
                    'raw' => $cleanedOutput
                ], 422);
            }

            $is_questions = Question::where('document_id', $id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->get();

            if ($is_questions->isNotEmpty()) {
                $is_questions->each->delete();
            }

            $is_document_right_section = DocumentRightSection::where('document_id', $id)
                ->with('conditions')
                ->get();

            if ($is_document_right_section->isNotEmpty()) {
                $is_document_right_section->each->delete();
            }

            $questionData = $decoded['Questionnaire'] ?? [];

            $qidToRealIdMap = [];
            $questionModels = [];
            $questionDataModels = [];

            foreach ($questionData as $qid => $questions) {

                if (!is_array($questions)) continue;

                if (isset($questions['TYPE'])) {
                    $questions = [$questions];
                }

                foreach ($questions as $question) {

                    $type = match ($question['TYPE']) {
                        'RADIOBUTTON' => 'radio-button',
                        'DATEFIELD' => 'date-field',
                        'NUMBERFIELD' => 'number-field',
                        'PERCENTAGEBOX' => 'percentage-box',
                        default => strtolower($question['TYPE']),
                    };


                    $qId = preg_replace('/[^0-9]/', '', $qid);

                    $questionModel = new Question();
                    $questionModel->document_id = $id;
                    $questionModel->type = $type;

                    $lastOrder = Question::where('document_id', $id)
                        ->orderBy('order_id', 'desc')
                        ->first();
                    $questionModel->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;

                    if (!empty($question['condition_type'])) {
                        $questionModel->is_condition = 1;
                        $questionModel->condition_type = $question['condition_type'];
                    }

                    $questionModel->is_end = isset($question['goto']) && $question['goto'] === 'END' ? 1 : 0;
                    $questionModel->save();

                    $qidToRealIdMap[$qId] = $questionModel->id;
                    $questionModels[$qId] = $questionModel;

                    $questionDataModels[$qId] = [
                        'label' => $question['label'] ?? null,
                        'userinfo' => $question['userinfo'] ?? null,
                        'placeholder' => $question['placeholder'] ?? null,
                        'goto' => $question['goto'] ?? null,
                        'goto_if' => $question['goto_if'] ?? [],
                        'options' => $question['options'] ?? null,
                        'condition_type' => $question['condition_type'] ?? null,
                        'another_go_to_step' => $question['another_go_to_step'] ?? [],
                        'question_label_condition' => $question['question_label_condition'] ?? [],
                        'conditional_go_to_step' => $question['conditional_go_to_step'] ?? null
                    ];
                }
            }

            foreach ($questionDataModels as $qid => $data) {
                try {
                    $goto = $data['goto'] ?? null;
                    $gotoClean = preg_replace('/[^0-9]/', '', $goto);
                    $nextQuestionId = $goto && $goto !== 'END' ? ($qidToRealIdMap[$gotoClean] ?? null) : null;

                    $questionDataModel = new QuestionData();
                    $questionDataModel->question_id = $qidToRealIdMap[$qid];
                    $questionDataModel->question_label = $data['label'];
                    $questionDataModel->question_info_text = $data['userinfo'];
                    $questionDataModel->text_box_placeholder = $data['placeholder'];
                    $questionDataModel->next_question_id = $nextQuestionId;

                    if (!empty($data['options'])) {
                        $order = 1;
                        foreach ($data['options'] as $opt) {
                            $opt = (array)$opt;
                            $option = new MultipleChoiceQuestionOption();
                            $option->question_id = $qidToRealIdMap[$qid];
                            $option->option_label = $opt['option_label'] ?? '';
                            $option->option_value = $opt['option_value'] ?? '';

                            $nextQId = null;
                            if (!empty($opt['go_next_step']) && $opt['go_next_step'] !== 'END') {
                                if (preg_match('/QID(\d+)/', $opt['go_next_step'], $match)) {
                                    $nextQId = $qidToRealIdMap[$match[1]] ?? null;
                                }
                            }

                            $option->next_question_id = $nextQId;
                            $option->order_id = $order++;
                            $option->save();
                        }
                    }

                    if ($data['condition_type'] == "1") {
                        $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                        foreach ($labels as $labelCondition) {
                            $qc = new QuestionCondition();
                            $qc->question_id = $qidToRealIdMap[$qid];
                            $qc->condition_type = 'question_label_condition';
                            $qc->question_label = $labelCondition['label'] ?? '';

                            $condQid = null;
                            if (!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)) {
                                $condQid = $qidToRealIdMap[$match[1]] ?? null;
                            }

                            $qc->conditional_question_id = $condQid;
                            $qc->conditional_question_value = $labelCondition['value'] ?? '';
                            $qc->save();
                        }
                    }

                    if ($data['condition_type'] == "2") {
                        $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                        $goToStepTarget = null;


                        if (isset($data['conditional_go_to_step'])) {
                            if (preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)) {
                                $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                            }
                        }


                        foreach ($gotoIfConditions as $condition) {
                            if (isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)) {
                                $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                                $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                                $checkType = match ($operatorText) {
                                    'is equal to' => 1,
                                    'is greater than' => 2,
                                    'is less than' => 3,
                                    'is not equal to' => 4,
                                    default => 1,
                                };

                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$qid];
                                $qc->condition_type = 'go_to_step_condition';
                                $qc->conditional_question_id = $checkQid;
                                $qc->conditional_question_value = $condition['question_value'] ?? '';
                                $qc->conditional_check = $checkType;
                                $qc->save();
                            }
                        }

                        if ($goToStepTarget) {
                            $questionDataModel->conditional_go_to_step = $goToStepTarget;
                        }

                        $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                        foreach ($another_go_to_step as $index => $cond) {
                            $subGoToStep = null;
                            if (isset($cond['conditional_go_to_step'])) {
                                if (preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)) {
                                    $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                                }
                            }

                            $qc = new QuestionCondition();
                            $qc->question_id = $qidToRealIdMap[$qid];
                            $qc->condition_type = 'another_go_to_step_condition';
                            $qc->go_to_step = $subGoToStep;
                            $qc->save();

                            $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                            foreach ($subConditions as $subC) {
                                $sub = new SubCondition();
                                $sub->question_condition_id = $qc->id;

                                if (isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)) {
                                    $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                                }

                                $sub->conditional_question_value = $subC['question_value'] ?? null;

                                $checkType = match (strtolower(trim($subC['conditions'] ?? 'is equal to'))) {
                                    'is equal to' => 1,
                                    'is greater than' => 2,
                                    'is less than' => 3,
                                    'is not equal to' => 4,
                                    default => 1,
                                };

                                $sub->conditional_check = $checkType;
                                $sub->save();
                            }
                        }
                    }

                    if ($data['condition_type'] == "3") {
                        $labels = is_array($data['question_label_condition']) ? $data['question_label_condition'] : [];

                        foreach ($labels as $labelCondition) {
                            $qc = new QuestionCondition();
                            $qc->question_id = $qidToRealIdMap[$qid];
                            $qc->condition_type = 'question_label_condition';
                            $qc->question_label = $labelCondition['label'] ?? '';

                            $condQid = null;
                            if (!empty($labelCondition['question_id']) && preg_match('/QID(\d+)/', $labelCondition['question_id'], $match)) {
                                $condQid = $qidToRealIdMap[$match[1]] ?? null;
                            }

                            $qc->conditional_question_id = $condQid;
                            $qc->conditional_question_value = $labelCondition['value'] ?? '';
                            $qc->save();
                        }

                        $gotoIfConditions = is_array($data['goto_if']) ? $data['goto_if'] : [];
                        $goToStepTarget = null;


                        if (isset($data['conditional_go_to_step'])) {
                            if (preg_match('/QID(\d+)/', $data['conditional_go_to_step'], $match)) {
                                $goToStepTarget = $qidToRealIdMap[$match[1]] ?? null;
                            }
                        }

                        foreach ($gotoIfConditions as $condition) {
                            if (isset($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $match)) {
                                $checkQid = $qidToRealIdMap[$match[1]] ?? null;

                                $operatorText = strtolower(trim($condition['conditions'] ?? 'is equal to'));
                                $checkType = match ($operatorText) {
                                    'is equal to' => 1,
                                    'is greater than' => 2,
                                    'is less than' => 3,
                                    'is not equal to' => 4,
                                    default => 1,
                                };

                                $qc = new QuestionCondition();
                                $qc->question_id = $qidToRealIdMap[$qid];
                                $qc->condition_type = 'go_to_step_condition';
                                $qc->conditional_question_id = $checkQid;
                                $qc->conditional_question_value = $condition['question_value'] ?? '';
                                $qc->conditional_check = $checkType;
                                $qc->save();
                            }
                        }

                        if ($goToStepTarget) {
                            $questionDataModel->conditional_go_to_step = $goToStepTarget;
                        }

                        $another_go_to_step = is_array($data['another_go_to_step']) ? $data['another_go_to_step'] : [];

                        foreach ($another_go_to_step as $index => $cond) {
                            $subGoToStep = null;
                            if (isset($cond['conditional_go_to_step'])) {
                                if (preg_match('/QID(\d+)/', $cond['conditional_go_to_step'], $match)) {
                                    $subGoToStep = $qidToRealIdMap[$match[1]] ?? null;
                                }
                            }

                            $qc = new QuestionCondition();
                            $qc->question_id = $qidToRealIdMap[$qid];
                            $qc->condition_type = 'another_go_to_step_condition';
                            $qc->go_to_step = $subGoToStep;
                            $qc->save();

                            $subConditions = is_array($cond['subconditions']) ? $cond['subconditions'] : [];

                            foreach ($subConditions as $subC) {
                                $sub = new SubCondition();
                                $sub->question_condition_id = $qc->id;

                                if (isset($subC['question_id']) && preg_match('/QID(\d+)/', $subC['question_id'], $match)) {
                                    $sub->conditional_question_id = $qidToRealIdMap[$match[1]] ?? null;
                                }

                                $sub->conditional_question_value = $subC['question_value'] ?? null;

                                $checkType = match (strtolower(trim($subC['conditions'] ?? 'is equal to'))) {
                                    'is equal to' => 1,
                                    'is greater than' => 2,
                                    'is less than' => 3,
                                    'is not equal to' => 4,
                                    default => 1,
                                };

                                $sub->conditional_check = $checkType;
                                $sub->save();
                            }
                        }
                    }

                    $questionDataModel->save();
                } catch (Exception $e) {
                    Log::error("Error saving Question/QID {$qid}", [
                        'error' => $e->getMessage(),
                        'qData' => $data
                    ]);
                    throw $e;
                }
            }

            $rightContentData = $decoded['Contract_Text'] ?? [];

            foreach ($rightContentData as $tid => $contents) {
                try {
                    if (!is_array($contents)) continue;

                    if (isset($contents['TYPE'])) {
                        $contents = [$contents];
                    }

                    foreach ($contents as $content) {
                        $type = match ($content['TYPE']) {
                            'HEADLINE' => 'content_heading',
                            'CONTENT' => 'content',
                            'SIGNATURE' => 'signature_field',
                            default => strtolower($content['TYPE']),
                        };

                        $text = $content['TEXT'] ?? '';
                        $text = preg_replace_callback('/\{QID(\d+)\}/', function ($matches) use ($qidToRealIdMap) {
                            $originalQid = $matches[1];
                            return isset($qidToRealIdMap[$originalQid]) ? '{' . $qidToRealIdMap[$originalQid] . '}' : $matches[0];
                        }, $text);

                        $secure_blur_content = isset($content['BLUR_CONTENT']) && $content['BLUR_CONTENT'] ? 1 : 0;
                        $is_signature = ($type === 'signature_field') ? 1 : 0;
                        $is_condition = (!empty($content['CONDITIONS'])) ? 1 : 0;

                        $document_right_section = new DocumentRightSection();
                        $document_right_section->type = $type;
                        $document_right_section->document_id = $id;

                        $lastOrder = DocumentRightSection::where('document_id', $id)
                            ->orderBy('order_id', 'desc')
                            ->first();
                        $document_right_section->order_id = $lastOrder ? $lastOrder->order_id + 1 : 1;

                        $document_right_section->content = $text;
                        $document_right_section->text_align = $content['ALIGN_TEXT'] ?? 'left';
                        $document_right_section->is_condition = $is_condition;
                        $document_right_section->signature_field = $is_signature;
                        $document_right_section->secure_blur_content = $secure_blur_content;
                        $document_right_section->save();

                        if (!empty($content['CONDITIONS'])) {
                            foreach ($content['CONDITIONS'] as $condition) {
                                $condition = (array)$condition;

                                $checkType = match (strtolower(trim($condition['conditions'] ?? 'is equal to'))) {
                                    'is equal to' => 1,
                                    'is greater than' => 2,
                                    'is less than' => 3,
                                    'not equal to' => 4,
                                    default => 1,
                                };

                                $questionId = null;
                                if (!empty($condition['question_id']) && preg_match('/QID(\d+)/', $condition['question_id'], $matches)) {
                                    $questionId = $qidToRealIdMap[$matches[1]] ?? null;
                                }

                                $condition_type = match ($type) {
                                    'content' => 'content_condition',
                                    'signature_field' => 'signature_field',
                                    default => 'content_condition',
                                };

                                if ($questionId !== null) {
                                    $documentCondition = new QuestionCondition();
                                    $documentCondition->condition_type = $condition_type;
                                    $documentCondition->document_right_content_id = $document_right_section->id;
                                    $documentCondition->conditional_question_id = $questionId;
                                    $documentCondition->conditional_check = $checkType;
                                    $documentCondition->conditional_question_value = $condition['question_value'] ?? '';
                                    $documentCondition->save();
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    Log::error("Error saving Question/QID {$qid}", [
                        'error' => $e->getMessage(),
                        'qData' => $contents
                    ]);
                    throw $e;
                }
            }
            $document->published = '0';
            $document->update();

            DB::commit();
            return redirect()->back()->with('success', "Document Succesfully Updated");
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function saveRecommendedSection(Request $request)
    {
        $document_id = $request->documentId;
        $document_name = $request->documentName;

        $standardSection = StandardDocument::where('type', 'global')->get();
        $standardSectionName = $standardSection->pluck('title')->map(fn($name) => (string) $name)->toArray();
        $standardSectionId = $standardSection->pluck('id')->map(fn($id) => (string) $id)->toArray();

        $standardSectionNamesStr = implode(", ", $standardSectionName);
        $standardSectionIdsStr = implode(", ", $standardSectionId);

        $prompt = "Analyze the list of available Standard Sections: {$standardSectionNamesStr} and their IDs: {$standardSectionIdsStr}.\n\n";
        $prompt .= "Based on the document name: {$document_name}, recommend the most suitable Standard Sections and determine the correct order in which they should appear.\n\n";
        $prompt .= "Respond in Spanish, and return the result strictly in the following JSON format:\n\n";
        $prompt .= "{'standard_sections_ids': [ ]}";

        $generator_prompt = Prompt::where([['key', 'document_generator'], ['location', 'document']])->first();
        $prompt_ai_model = $generator_prompt?->prompt_ai_model ?? '';

        $aiService = new AIService();

        if ($prompt_ai_model === 'Gemini 2.0') {
            $result = $aiService->recommendedSectionIds($prompt);
        } elseif ($prompt_ai_model === 'chatgpt') {
            $result = $aiService->recommendedSectionIdsWithOpenAI($prompt);
        }
        $aiOutput = $result;
        // return $aiOutput;

        $existingSectionIds = $request->has('recomm_sections_ids')
            ? json_decode($request->recomm_sections_ids, true)
            : [];

        foreach ($aiOutput as $key => $val) {
            if (in_array($val, $existingSectionIds)) {
                continue;
            }

            $recommended = RecommendedSection::where([
                ['document_id', $document_id],
                ['standard_section_id', $val],
            ])->first();

            if ($recommended) {
                $recommended->standard_section_id = $val;
                $recommended->status = 1;
                $recommended->update();
            } else {
                $recommended = new RecommendedSection();
                $recommended->document_id = $document_id;
                $recommended->standard_section_id = $val;
                $recommended->order_id = $key;
                $recommended->status = 1;
                $recommended->save();
            }
        }

        $document_generator = DocumentGenerator::where('document_id', $document_id)->first();

        if ($document_generator) {
            // $document_generator->ai_status = 2;
            $document_generator->update();
        } else {
            $document_generator = new DocumentGenerator;
            $document_generator->document_id = $document_id;
            $document_generator->document_name = $document_name;
            // $document_generator->ai_status = 2;
            $document_generator->save();
        }

        // $recommendedSection = RecommendedSection::where('document_id',$document_id)->with('standard_section')->get();
        $recommendedSection = RecommendedSection::where('document_id', $document_id)
            ->with(['standard_section' => function ($query) {
                $query->where('type', 'global');
            }])
            ->get();

        $recommendedSectionIds = $recommendedSection->pluck('standard_section_id')->map(fn($id) => (string) $id)->toArray();

        return response()->json([
            'status'  => true,
            'message' => 'Recommended section saved successfully.',
            'sections' => $recommendedSection,
            'section_ids' => $recommendedSectionIds,
        ], 200);
    }

    public function updateRecommendedSection(Request $request)
    {
        DB::beginTransaction();
        try {
            $response = [];

            if ($request->type ?? '') {
                $type = $request->type;
                $sectionId = $request->id;
                $documentId = $request->document_id;

                if ($type == 'remove') {
                    $recommendedSection = RecommendedSection::where('standard_section_id', $sectionId)
                        ->where('document_id', $documentId)
                        ->first();

                    if ($recommendedSection) {
                        $recommendedSection->status = 0;
                        $recommendedSection->update();
                    }

                    $response = [
                        'status' => true,
                        'message' => 'Section removed successfully',
                        'section_id' => $sectionId,
                    ];
                } elseif ($type == 'add') {
                    $recommendedSection = RecommendedSection::where('standard_section_id', $sectionId)
                        ->where('document_id', $documentId)
                        ->first();

                    if ($recommendedSection) {
                        $recommendedSection->status = 1;
                        $recommendedSection->update();
                    } else {
                        $maxOrder = RecommendedSection::where('document_id', $documentId)->max('order_id');

                        $recommendedSection = new RecommendedSection;
                        $recommendedSection->document_id = $documentId;
                        $recommendedSection->standard_section_id = $sectionId;
                        $recommendedSection->status = 1;
                        $recommendedSection->order_id = $maxOrder !== null ? $maxOrder + 1 : 0;
                        $recommendedSection->save();
                    }

                    $response = [
                        'status' => true,
                        'message' => 'Section added successfully',
                        'section_id' => $sectionId,
                        'order_id' => $recommendedSection->order_id,
                    ];
                }
            }

            DB::commit();
            return response()->json($response);
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocumentQuestions(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->id) {
                $id = $request->id;
                if ($request->type == 'question') {
                    $question = Question::where('id', $id)->first();
                    if ($question) {
                        // $question->questionData()->delete();
                        // $question->conditions()->delete();
                        // $question->options()->delete();
                        $question->delete();
                    }
                } elseif ($request->type == 'text') {
                    $rightContent = DocumentRightSection::where('id', $id)->first();
                    if ($rightContent) {
                        $rightContent->delete();
                    }
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Successfully deleted',
                ], 200);
            }
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function documentSectionProcess(Request $request)
    {
        $section_prompt = Prompt::where('key', 'section_name_generator')->first();
        $prompt = $section_prompt->updated_prompt;

        $sectionIDs = $request->section_ids;
        if (!is_array($sectionIDs)) {
            $sectionIDs = explode(',', $sectionIDs);
        }

        $standardDocument = StandardDocument::whereIn('id', $sectionIDs)->get();

        $globalSectionCatalog = $standardDocument->map(function ($doc) {
            return [
                'id' => $doc->id,
                'section_name'  => $doc->title,
                'description'   => strip_tags($doc->description ?? '')
            ];
        })->values()->toArray();

        $globalSectionCatalogJson = json_encode($globalSectionCatalog, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $finalPrompt = str_replace('{global_section_catalog}', $globalSectionCatalogJson, $prompt);

        $aiService = new AIService();
        $result = $aiService->generateSectionFromIds($finalPrompt);
        $aiOutput = $result;

        return $aiOutput;
    }

    public function editGeneratedQuestions(Request $request)
    {
        // return $request->all();
        try {

            $questions = Question::find($request->questionID);
            $order_id = $request->orderID;
            $questions->is_condition = $is_condition;
            $questions->condition_type = $condition_type;
            $questions->is_end = $data->is_end;
            $questions->order_id = $order_id;
            $questions->update();

            $question_data = QuestionData::where('question_id', $request->questionID)->first();
            $question_data->question_label = $request->text_qu_label;

            if (isset($request->text_box_placeholder) && $request->text_box_placeholder != null) {
                $question_data->text_box_placeholder = $request->text_box_placeholder;
            }

            $question_data->same_contract_link_label = $request->same_contract_link;

            if (isset($request->go_to_step)) {
                if ($request->go_to_step == "0") {
                    $question_data->next_question_id = null;
                } else {
                    $question_data->next_question_id = $request->go_to_step;
                }
            }
            $question_data->question_info_text = $request->question_info_text;
            $question_data->update();
        } catch (Exception $e) {
            DB::rollBack();
            saveLog("Error:", "DocumentController", $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function checkTitle(Request $request)
    {
        $title = $request->input('title');
        $slug = Str::slug($title);

        $exists = Document::where('slug', $slug)->exists();

        return response()->json(['exists' => $exists]);
    }

    public function saveGeneratorStep(Request $request)
    {
        $document_id = $request->document_id;
        $step = $request->step;

        $document_generator = DocumentGenerator::where('document_id', $document_id)->first();

        if ($document_generator) {
            $document_generator->ai_status = $step;
            $document_generator->update();
        } else {
            $document_generator = new DocumentGenerator;
            $document_generator->document_id = $document_id;
            $document_generator->ai_status = $step;
            $document_generator->save();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Step saved successfully.',
        ], 200);
    }

    public function aiResponse(Request $request, $id)
    {
        $documentGenerator = DocumentGenerator::where('document_id', $id)->first();
        if (!$documentGenerator) {
            $ai_response = null;
        } else {
            $ai_response = $documentGenerator->ai_response;
        }

        return view('admin.documents.ai_response', compact('ai_response'));
    }

    public function checkStatus(Request $request)
    {
        // return $request->all();

        try {
            $documentId = $request->input('document_id');
            $generatorId = $request->input('id');

            $document = Document::find($documentId);
            // Example: Fetch document generation status from DB
            $document_generator = DocumentGenerator::where('document_id', $document->id)
                ->where('id', $generatorId)
                ->first();

            if (!$document_generator) {
                return response()->json([
                    'status' => false,
                    'ai_status' => null,
                    'message' => 'Document not found'
                ]);
            }

            if ($document_generator->ai_status != 2) {
                return response()->json([
                    'status' => false,
                    'ai_status' => $document_generator->ai_status,
                    'message' => 'Document is still being generated'
                ]);
            }
            $documentRight = DocumentRightSection::where('document_id', $document->id)->with('conditions', 'document')->orderBy('order_id', 'asc')->get();

            $questions = Question::where('document_id', $document->id)
                ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
                ->get();

            // $questionIds = $questions->pluck('id')->map(fn($id) => (string) $id)->toArray(); 
            // $sections = DocumentRightSection::where('document_id', $document->id)->with('conditions')->get();
            // $standard_section_Ids = $questions->pluck('standard_section_id')
            // ->merge($sections->pluck('standard_section_id'))
            // ->filter() 
            // ->unique()
            // ->map(fn($id) => (string) $id);

            // $standardDocuments = StandardDocument::whereIn('id', $standard_section_Ids)->get();

            $questionIds = $questions->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $sections = DocumentRightSection::where('document_id', $document->id)->with('conditions')->get();
            $standard_section_Ids = $sections->pluck('standard_section_id')
                ->filter()
                ->unique()
                ->map(fn($id) => (string) $id);

            $standardDocuments = collect();

            $ids = $standard_section_Ids->toArray();

            if (count($ids) > 0) {
                $standardDocuments = StandardDocument::whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, " . implode(',', $ids) . ")")
                    ->get();
            } else {
                $standardDocuments = collect();
            }


            $resultSections = [];

            foreach ($sections as $section) {
                $content = $section->content;
                preg_match_all('/\{(\d+)\}/', $content, $matches);

                $matchedQids = $matches[1] ?? [];

                $matchedQids = array_filter($matchedQids, function ($qid) use ($questionIds) {
                    return in_array($qid, $questionIds);
                });

                $matchedQuestions = [];
                foreach ($matchedQids as $qid) {
                    $q = $questions->firstWhere('id', (int)$qid);
                    if ($q) {
                        $matchedQuestions[] = [
                            'id'    => $q->id,
                            'questions' => $q,
                        ];
                    }
                }

                if (count($matchedQuestions) > 0) {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                } else {
                    $resultSections[] = [
                        'text'       => $content,
                        'questions'  => $matchedQuestions,
                        'section_id' => $section->id,
                        'type'       => $section->type,
                        'text_align' => $section->text_align,
                        'text_alignment' => $section->text_alignment,
                        'is_condition' => $section->is_condition,
                        'conditions' => $section->conditions,
                        'blurr_content' => $section->secure_blur_content,
                        'content2' => $section->content2,
                        'content3' => $section->content3,
                        'standard_section_id' => $section->standard_section_id,

                    ];
                }
            }

            $usedQids = collect($resultSections)->pluck('questions')->flatten(1)->pluck('id')->toArray();
            $standaloneQuestions = $questions->whereNotIn('id', $usedQids);

            foreach ($standaloneQuestions as $q) {
                $resultSections[] = [
                    'section_id' => null,
                    'text'       => null,
                    'questions'  => [
                        [
                            'id'    => $q->id,
                            'questions' => $q,
                        ]
                    ],
                    'section_id' => null,
                    'type'       => null,
                    'text_align' => null,
                    'text_alignment' => null,
                    'is_condition' => null,
                    'conditions' => null,
                    'blurr_content' => null,
                    'content2' => null,
                    'content3' => null,
                    'standard_section_id' => null,
                ];
            }

            $types = QuestionType::all();

            DB::commit();

            $html = view('admin.documents.partial.step3', compact(
                'questions',
                'resultSections',
                'types',
                'standardDocuments'
            ))->render();

            return response()->json([
                'status' => true,
                'ai_status' => $document_generator->ai_status,
                'document_id' => $document->id,
                'message' => 'Contract generated successfully.',
                'html' => $html
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'ai_status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function ceDocumentEditor(Request $request)
    {
        $documents = Document::where('published', 1)->get();

        $questions = collect();
        $document = null;
        $slug = '';
        $documentRight = collect();
        $document_questions = collect();
        $types = QuestionType::all();

        if (isset($request->id) && $request->id != null) {
            $document = Document::find($request->id);
            if ($document) {
                $slug = $document->slug;

                $questions = Question::where('document_id', $request->id)->get();

                $document_questions = Question::where('document_id', $request->id)
                    ->with(['questionData', 'conditions.subconditions', 'options', 'nextQuestion'])
                    ->get();

                $documentRight = DocumentRightSection::where('document_id', $request->id)
                    ->with('conditions', 'document')
                    ->orderBy('order_id', 'asc')
                    ->get();
            }
        }

        return view('admin.documents.document_contract_edit', compact(
            'documents',
            'document',
            'slug',
            'questions',
            'document_questions',
            'documentRight',
            'types'
        ));
    }

public function ceGetQuestions($id)
{
    try {
        $allSections = DocumentRightSection::where('document_id', $id)
            ->pluck('content');

        $usageMap = [];
        foreach ($allSections as $content) {
            preg_match_all('/\{(\d+)\}/', $content ?? '', $m);
            foreach ($m[1] as $qid) {
                $usageMap[$qid] = ($usageMap[$qid] ?? 0) + 1;
            }
        }

        $questions = Question::where('document_id', $id)
            ->with(['questionData', 'options', 'conditions.subconditions'])
            ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
            ->get()
            ->map(function ($q) use ($usageMap) {

                // Show-if conditions (question_label_condition)
                $conditions = $q->conditions
                    ->where('condition_type', 'question_label_condition')
                    ->map(fn($c) => [
                        'label' => $c->question_label             ?? '',
                        'qid'   => (string)($c->conditional_question_id    ?? ''),
                        'value' => $c->conditional_question_value ?? '',
                    ])->values()->toArray();

                // condGoTo: another_go_to_step_condition (complex groups with subconditions)
                $condGoTo = $q->conditions
                    ->where('condition_type', 'another_go_to_step_condition')
                    ->map(fn($c) => [
                        'goto'       => $c->go_to_step !== null ? (string)$c->go_to_step : '',
                        'conditions' => $c->subconditions->map(fn($s) => [
                            'qid'   => (string)($s->conditional_question_id    ?? ''),
                            'type'  => $this->_numericCondToSlug($s->conditional_check),
                            'value' => $s->conditional_question_value ?? '',
                        ])->values()->toArray(),
                    ])
                    ->filter(fn($cg) => $cg['goto'] !== '' && $cg['goto'] !== null)
                    ->values()->toArray();

                // Also include go_to_step_condition entries that have no subconditions
                // (simple option-based gotos stored as go_to_step_condition)
                $simpleCondGoTo = $q->conditions
                    ->where('condition_type', 'go_to_step_condition')
                    ->map(fn($c) => [
                        'goto'       => $c->go_to_step !== null ? (string)$c->go_to_step : '',
                        'conditions' => [[
                            'qid'   => (string)($c->conditional_question_id ?? ''),
                            'type'  => $this->_numericCondToSlug($c->conditional_check),
                            'value' => $c->conditional_question_value ?? '',
                        ]],
                    ])
                    ->filter(fn($cg) => $cg['goto'] !== '' && $cg['goto'] !== null)
                    ->values()->toArray();

                // Merge both condGoTo sources
                $allCondGoTo = array_merge($condGoTo, $simpleCondGoTo);

                return [
                    'id'            => $q->id,
                    'type'          => $q->type,
                    'order_id'      => $q->order_id,
                    'section'       => optional($q->standard_documents)->title ?? '',
                    'go_to'         => optional($q->questionData)->next_question_id ?? null,
                    'used_in_count' => $usageMap[(string)$q->id] ?? 0,
                    'required'      => 1,
                    'questionData'  => $q->questionData ? [
                        'question_label'       => $q->questionData->question_label,
                        'text_box_placeholder' => $q->questionData->text_box_placeholder,
                        'question_info_text'   => $q->questionData->question_info_text,
                    ] : null,
                    'options' => $q->options->map(fn($o) => [
                        'id'           => $o->id,
                        'option_label' => $o->option_label,
                        'option_value' => $o->option_value,
                        'order_id'     => $o->order_id,
                    ])->toArray(),
                    'conditions' => $conditions,
                    'condGoTo'   => $allCondGoTo,
                ];
            });

        return response()->json(['success' => true, 'questions' => $questions]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
 
public function ceGetSections($id)
{
    try {
        $sections = \App\Models\DocumentRightSection::where('document_id', $id)
            ->with(['standardDocument', 'conditions'])
            ->orderBy('order_id', 'asc')
            ->get()
            ->map(fn($s) => [
                'id'                  => $s->id,
                'type'                => $s->type,
                'content'             => $s->content,
                'text_align'          => $s->text_align           ?? 'left',
                'secure_blur_content' => $s->secure_blur_content  ?? 0,
                'order_id'            => $s->order_id,
                // 'section_name'        => optional($s->standardDocument)->title ?? '',
                'section_key'         => optional($s->standardDocument)->slug  ?? '',
                'conditions'          => $s->conditions->map(fn($c) => [
                    'qid'   => $c->conditional_question_id    ?? '',
                    'type'  => $this->_numericCondToSlug($c->conditional_check),
                    'value' => $c->conditional_question_value ?? '',
                ])->values()->toArray(),
            ]);
 
        return response()->json(['success' => true, 'sections' => $sections]);
 
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function ceAiEdit(Request $request)
{
    set_time_limit(180);
    ini_set('max_execution_time', 180);

    try {
        $request->validate([
            'document_id' => 'required|integer|exists:documents,id',
            'prompt'      => 'required|string|max:4000',
            'scope'       => 'required|in:all,questions,sections',
            'questions'   => 'array',
            'sections'    => 'array',
        ]);

        $scopeVal  = $request->scope;
        $qSummary  = $request->questions ?? [];
        $sSummary  = $request->sections  ?? [];

        $trimmedQuestions = array_map(function($q) {
            return [
                'id'    => $q['id']    ?? null,
                'type'  => $q['type']  ?? 'textbox',
                'label' => mb_substr($q['label'] ?? '', 0, 80),
            ];
        }, $qSummary);

        $trimmedSections = array_map(function($s) {
            $rawContent = $s['content'] ?? '';
            $plain      = strip_tags((string)$rawContent);
            $plain      = preg_replace('/\s+/', ' ', $plain);
            return [
                'id'      => $s['id']   ?? null,
                'type'    => $s['type'] ?? 'content',
                'content' => mb_substr(trim($plain), 0, 200),
            ];
        }, $sSummary);

        $trimmedQuestions = array_slice($trimmedQuestions, 0, 50);
        $trimmedSections  = array_slice($trimmedSections,  0, 50);

        if ($scopeVal === 'questions') {
            $trimmedSections = array_map(fn($s) => [
                'id'      => $s['id']   ?? null,
                'type'    => $s['type'] ?? 'content',
                'content' => '[unchanged]',
            ], $trimmedSections);
        } elseif ($scopeVal === 'sections') {
            $trimmedQuestions = array_map(fn($q) => [
                'id'    => $q['id']   ?? null,
                'type'  => $q['type'] ?? 'textbox',
                'label' => '[unchanged]',
            ], $trimmedQuestions);
        }

        $fullPrompt =
            "You are a contract editor AI. Return ONLY a valid JSON object. No markdown, no backticks, no explanation.\n\n"
            . "REQUIRED OUTPUT FORMAT (return exactly this structure):\n"
            . "{\"summary\":\"brief description of changes\",\"questions\":[],\"sections\":[]}\n\n"
            . "Rules:\n"
            . "- Include ALL existing items in questions and sections arrays\n"
            . "- For unchanged items, keep id and set label/content to '[unchanged]'\n"
            . "- For new items, omit the id field\n"
            . "- Do NOT wrap in markdown code blocks\n"
            . "- Return ONLY the JSON, nothing else\n\n";

        if ($scopeVal === 'questions') {
            $fullPrompt .= "SCOPE: questions only. Set all section content to '[unchanged]'.\n\n";
        } elseif ($scopeVal === 'sections') {
            $fullPrompt .= "SCOPE: sections only. Set all question labels to '[unchanged]'.\n\n";
        }

        $fullPrompt .= "USER INSTRUCTION: " . $request->prompt . "\n\n"
            . "CURRENT QUESTIONS: " . json_encode(array_values($trimmedQuestions), JSON_UNESCAPED_UNICODE) . "\n\n"
            . "CURRENT SECTIONS: "  . json_encode(array_values($trimmedSections),  JSON_UNESCAPED_UNICODE) . "\n\n"
            . "OUTPUT JSON:";

        $generatorPrompt = \App\Models\Prompt::where('key', 'document_generator')
            ->where('location', 'document')
            ->first();
        $aiModel = $generatorPrompt->prompt_ai_model ?? '';

        $aiService = new AIService($aiModel);
        $rawOutput = $aiService->generateText($fullPrompt);

        Log::info('ceAiEdit raw output', [
            'length'  => strlen($rawOutput ?? ''),
            'preview' => substr($rawOutput ?? '', 0, 500),
        ]);

        if (empty($rawOutput)) {
            return response()->json([
                'success' => false,
                'message' => 'AI returned an empty response. Please try again.',
            ], 422);
        }

        $parsed = $this->_parseAiJsonResponse($rawOutput);

        if (!is_array($parsed)) {
            Log::error('ceAiEdit: all parse attempts failed', ['raw' => substr($rawOutput, 0, 1000)]);
            return response()->json([
                'success' => false,
                'message' => 'AI returned an unreadable response. Please try a simpler or shorter instruction.',
            ], 422);
        }

        $parsed['questions'] = isset($parsed['questions']) && is_array($parsed['questions'])
            ? $parsed['questions'] : [];
        $parsed['sections']  = isset($parsed['sections'])  && is_array($parsed['sections'])
            ? $parsed['sections']  : [];

        return response()->json([
            'success'   => true,
            'summary'   => $parsed['summary']   ?? 'Changes applied.',
            'questions' => $parsed['questions'],
            'sections'  => $parsed['sections'],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (\Exception $e) {
        saveLog('Error:', 'DocumentController@ceAiEdit', $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'AI edit failed: ' . $e->getMessage(),
        ], 500);
    }
}

private function _parseAiJsonResponse(string $raw): ?array
{
    $cleaned = trim($raw);

    $cleaned = preg_replace('/^```(?:json)?\s*/im', '', $cleaned);
    $cleaned = preg_replace('/```\s*$/m', '', $cleaned);
    $cleaned = trim($cleaned);

    $parsed = json_decode($cleaned, true);
    if (is_array($parsed)) return $parsed;

    if (preg_match('/(\{[\s\S]*\})/s', $cleaned, $matches)) {
        $parsed = json_decode($matches[1], true);
        if (is_array($parsed)) return $parsed;

        $repaired = preg_replace('/,\s*([\]}])/s', '$1', $matches[1]);
        $parsed = json_decode($repaired, true);
        if (is_array($parsed)) return $parsed;
    }

    $start = strpos($cleaned, '{');
    $end   = strrpos($cleaned, '}');
    if ($start !== false && $end !== false && $end > $start) {
        $sub    = substr($cleaned, $start, $end - $start + 1);
        $parsed = json_decode($sub, true);
        if (is_array($parsed)) return $parsed;

        $repaired = preg_replace('/,\s*([\]}])/s', '$1', $sub);
        $parsed   = json_decode($repaired, true);
        if (is_array($parsed)) return $parsed;
    }

    return null;
}

public function ceSave(Request $request)
{
    DB::beginTransaction();

    try {
        $docId = $request->document_id;

        foreach ($request->delete_question_ids ?? [] as $qid) {
            $q = Question::find($qid);
            if ($q && $q->document_id == $docId) {
                $q->questionData()->delete();
                $q->options()->delete();
                $q->conditions()->each(function ($c) {
                    $c->subconditions()->delete();
                    $c->delete();
                });
                $q->delete();
            }
        }

        foreach ($request->delete_section_ids ?? [] as $sid) {
            $s = DocumentRightSection::find($sid);
            if ($s && $s->document_id == $docId) {
                $s->conditions()->delete();
                $s->delete();
            }
        }

        foreach ($request->questions ?? [] as $qData) {

            if (empty($qData['id'])) {
                $q = new Question();
                $q->document_id = $docId;
            } else {
                $q = Question::where('id', $qData['id'])
                    ->where('document_id', $docId)->first();
                if (!$q) continue;
            }

            $q->type     = $qData['type'];
            $q->order_id = $qData['order_id'];
            $q->save();

            $qd = QuestionData::where('question_id', $q->id)->first();
            if (!$qd) {
                $qd = new QuestionData();
                $qd->question_id = $q->id;
            }
            $qd->question_label       = $qData['label']       ?? null;
            $qd->text_box_placeholder = $qData['placeholder'] ?? null;
            $qd->question_info_text   = $qData['info']        ?? null;

            $goTo = $qData['goTo'] ?? $qData['go_to'] ?? null;
            $qd->next_question_id = ($goTo && $goTo !== 'END') ? $goTo : null;
            $qd->save();

            if (isset($qData['options'])) {
                MultipleChoiceQuestionOption::where('question_id', $q->id)->delete();
                foreach ($qData['options'] as $i => $opt) {
                    $o = new MultipleChoiceQuestionOption();
                    $o->question_id  = $q->id;
                    $o->option_label = $opt['label']        ?? $opt['option_label'] ?? '';
                    $o->option_value = $opt['value']        ?? $opt['option_value'] ?? ($opt['label'] ?? '');
                    $o->order_id     = $i + 1;
                    $o->save();
                }
            }

            // Delete existing show-if and condGoTo conditions before re-saving
            $q->conditions()
              ->whereIn('condition_type', [
                  'question_label_condition',
                  'another_go_to_step_condition',
                  'go_to_step_condition',
              ])
              ->each(function ($c) {
                  $c->subconditions()->delete();
                  $c->delete();
              });

            // Save show-if conditions (question_label_condition)
            $hasConditions = false;
            foreach ($qData['conditions'] ?? [] as $cond) {
                if (empty($cond['label']) && empty($cond['qid'])) continue;
                $qc = new QuestionCondition();
                $qc->question_id                = $q->id;
                $qc->condition_type             = 'question_label_condition';
                $qc->question_label             = $cond['label']  ?? '';
                $qc->conditional_question_id    = !empty($cond['qid']) ? $cond['qid'] : null;
                $qc->conditional_question_value = $cond['value']  ?? '';
                $qc->save();
                $hasConditions = true;
            }

            // Save condGoTo groups
            $hasCondGoTo = false;
            foreach ($qData['cond_go_to'] ?? [] as $grp) {
                $gotoVal    = $grp['goto'] ?? '';
                $conditions = $grp['conditions'] ?? [];

                if ($gotoVal === '' || $gotoVal === null) continue;

                $hasCondGoTo = true;
                $isSingleSimpleCond = count($conditions) === 1;

                if ($isSingleSimpleCond) {
                    // Save as go_to_step_condition (simple, one condition row, no subconditions)
                    $sub = $conditions[0];
                    $qc = new QuestionCondition();
                    $qc->question_id                = $q->id;
                    $qc->condition_type             = 'another_go_to_step_condition';
                    $qc->go_to_step                 = ($gotoVal && $gotoVal !== 'END') ? $gotoVal : null;
                    $qc->save();

                    if (!empty($sub['qid'])) {
                        $sc = new SubCondition();
                        $sc->question_condition_id      = $qc->id;
                        $sc->conditional_question_id    = $sub['qid']   ?? null;
                        $sc->conditional_question_value = $sub['value'] ?? '';
                        $sc->conditional_check          = $this->_slugCondToNumeric($sub['type'] ?? '');
                        $sc->save();
                    }
                } else {
                    // Save as another_go_to_step_condition with subconditions
                    $qc = new QuestionCondition();
                    $qc->question_id    = $q->id;
                    $qc->condition_type = 'another_go_to_step_condition';
                    $qc->go_to_step     = ($gotoVal && $gotoVal !== 'END') ? $gotoVal : null;
                    $qc->save();

                    foreach ($conditions as $sub) {
                        if (empty($sub['qid'])) continue;
                        $sc = new SubCondition();
                        $sc->question_condition_id      = $qc->id;
                        $sc->conditional_question_id    = $sub['qid']   ?? null;
                        $sc->conditional_question_value = $sub['value'] ?? '';
                        $sc->conditional_check          = $this->_slugCondToNumeric($sub['type'] ?? '');
                        $sc->save();
                    }
                }
            }

            // Update question flags
            $hasAnyCondition = $hasConditions || $hasCondGoTo;
            if ($hasConditions && $hasCondGoTo) {
                $q->is_condition   = 1;
                $q->condition_type = 3;
            } elseif ($hasConditions) {
                $q->is_condition   = 1;
                $q->condition_type = 1;
            } elseif ($hasCondGoTo) {
                $q->is_condition   = 1;
                $q->condition_type = 2;
            } else {
                $q->is_condition   = 0;
                $q->condition_type = null;
            }
            $q->save();
        }

        // Save sections
        foreach ($request->sections ?? [] as $sData) {

            if (empty($sData['id'])) {
                $s = new DocumentRightSection();
                $s->document_id = $docId;
            } else {
                $s = DocumentRightSection::where('id', $sData['id'])
                    ->where('document_id', $docId)->first();
                if (!$s) continue;
            }

            if (!empty($sData['section_key'])) {
                $stdId = StandardDocument::where('slug', $sData['section_key'])
                    ->where('type', 'global')
                    ->value('id');
                $s->standard_section_id = $stdId ?? null;
            }

            $s->type                = $sData['type'];
            $s->content             = $sData['content']             ?? '';
            $s->text_align          = $sData['text_align']          ?? 'left';
            $s->secure_blur_content = $sData['secure_blur_content'] ?? 0;
            $s->order_id            = $sData['order_id'];
            $s->save();

            // Section conditions
            if (isset($sData['conditions'])) {
                $s->conditions()->delete();
                foreach ($sData['conditions'] as $cond) {
                    if (empty($cond['qid'])) continue;
                    $sc = new QuestionCondition();
                    $sc->condition_type             = 'content_condition';
                    $sc->document_right_content_id  = $s->id;
                    $sc->conditional_question_id    = $cond['qid']   ?? null;
                    $sc->conditional_check          = $this->_slugCondToNumeric($cond['type'] ?? '');
                    $sc->conditional_question_value = $cond['value'] ?? '';
                    $sc->save();
                }
            }
        }
        // dd($request->all());

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Saved successfully']);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
 
private function _numericCondToSlug(?int $check): string
{
    return match ($check) {
        1       => 'is_equal_to',
        2       => 'is_greater_than',
        3       => 'is_less_than',
        4       => 'is_not_equal_to',
        default => '',
    };
}
 

private function _slugCondToNumeric(string $slug): int
{
    return match (strtolower($slug)) {
        'is_equal_to'     => 1,
        'is_greater_than' => 2,
        'is_less_than'    => 3,
        'is_not_equal_to' => 4,
        default           => 1,
    };
}

public function ceGetStandardDocuments(Request $request)
{
    try {
        $perPage = (int) $request->get('per_page', 20);
        $page    = (int) $request->get('page', 1);
        $search  = $request->get('search', '');

        $query = StandardDocument::orderBy('title')->latest();

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $total      = $query->count();
        $totalPages = (int) ceil($total / $perPage);
        $offset     = ($page - 1) * $perPage;

        $documents = $query->skip($offset)->take($perPage)->get()
            ->map(function ($doc) {
                $questionsCount = Question::where('standard_section_id', $doc->id)->count();
                $sectionsCount  = DocumentRightSection::where('standard_section_id', $doc->id)->count();

                return [
                    'id'              => $doc->id,
                    'title'           => $doc->title ?? '',
                    'name'            => $doc->title ?? '',
                    'slug'            => $doc->slug  ?? '',
                    'description'     => $doc->description ?? '',
                    'type'            => $doc->type ?? '',
                    'questions_count' => $questionsCount,
                    'sections_count'  => $sectionsCount,
                ];
            });

        return response()->json([
            'success'     => true,
            'documents'   => $documents,
            'total'       => $total,
            'per_page'    => $perPage,
            'current_page'=> $page,
            'total_pages' => $totalPages,
        ]);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
     
    public function ceGetStandardDocumentDetail($id)
    {
        try {
            $doc = StandardDocument::find($id);
     
            if (!$doc) {
                return response()->json(['success' => false, 'message' => 'Standard clause not found.'], 404);
            }
     
            $questions = \App\Models\Question::where('standard_section_id', $doc->id)
                ->with(['questionData', 'options'])
                ->orderByRaw('CAST(order_id AS UNSIGNED) ASC')
                ->get()
                ->map(function ($q) {
                    return [
                        'id'          => $q->id,
                        'type'        => $q->type,
                        'label'       => $q->questionData->question_label       ?? '',
                        'info'        => $q->questionData->question_info_text   ?? '',
                        'placeholder' => $q->questionData->text_box_placeholder ?? '',
                        'options'     => $q->options->map(fn($o) => [
                            'id'           => $o->id,
                            'option_label' => $o->option_label,
                            'option_value' => $o->option_value,
                        ])->toArray(),
                    ];
                });
     
            $sections = \App\Models\DocumentRightSection::where('standard_section_id', $doc->id)
                ->orderBy('order_id', 'asc')
                ->get()
                ->map(function ($s) {
                    return [
                        'id'                  => $s->id,
                        'type'                => $s->type,
                        'content'             => $s->content,
                        'text_align'          => $s->text_align          ?? 'left',
                        'secure_blur_content' => $s->secure_blur_content ?? 0,
                        'section_key'         => $s->type                ?? '',
                    ];
                });
     
            return response()->json([
                'success'   => true,
                'clause'    => ['id' => $doc->id, 'title' => $doc->title ?? ''],
                'questions' => $questions,
                'sections'  => $sections,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
