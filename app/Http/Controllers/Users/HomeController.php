<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeContent;
use App\Models\HomeCategories;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Review;
use App\Models\Question;
use App\Models\Setting;
use App\Models\DocumentRightSection;
use App\Models\GeneralSection;
use App\Models\ArticleSection;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function home(){


        
        $keys = [
            'title',
            'favicon',
            'background_image',
            'banner_image',
            'banner_title',
            'banner_description',
            'banner_placeholder',
            'button_name',
            'most_popular_title',
            'most_popular_btn_text',
            'most_popular_ryt_doc_text',
            'popular',
            'bottom_heading',
            'bottom_subheading',
            'bottom_button_label',
            'bottom_button_link',
            'bottom_banner_image',
            'category_title',
            'category_btn_arrow_img',
            'join_us_text',
            'reviews_heading',
            'reviews_sub_heading',
            'review_left_arrow',
            'review_right_arrow',
            'home_text_google',
            'home_text_facebook',
            'home_text_email',
            'home_text_register',
            'meta_title'
        ];

        $results = HomeContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'favicon' => $results['favicon']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_placeholder' => $results['banner_placeholder']->value ?? null,
            'button_name' => $results['button_name']->value ?? null,
            'most_popular_title' => $results['most_popular_title']->value ?? null,
            'most_popular_btn_text' => $results['most_popular_btn_text']->value ?? null,
            'most_popular_ryt_doc_text' => $results['most_popular_ryt_doc_text']->value ?? null,
            'popular' => $results['popular']->value ?? null,
            'bottom_heading' => $results['bottom_heading']->value ?? null,
            'bottom_subheading' => $results['bottom_subheading']->value ?? null,
            'bottom_button_label' => $results['bottom_button_label']->value ?? null,
            'bottom_button_link' => $results['bottom_button_link']->value ?? null,
            'bottom_banner_image' => str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
            'category_title' => $results['category_title']->value ?? null,
            'join_us_text' => $results['join_us_text']->value ?? null,
            'reviews_heading' => $results['reviews_heading']->value ?? null,
            'reviews_sub_heading' => $results['reviews_sub_heading']->value ?? null,
            'review_left_arrow' => str_replace('public/', '', $results['review_left_arrow']->file_path ?? null),
            'review_right_arrow' => str_replace('public/', '', $results['review_right_arrow']->file_path ?? null),
            'home_text_email' => $results['home_text_email']->value ?? null,
            'home_text_facebook' => $results['home_text_facebook']->value ?? null,
            'home_text_google' => $results['home_text_google']->value ?? null,
            'home_text_register' => $results['home_text_register']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];

        $home_category = HomeCategories::with('media','category')->get();

        $alldocuments = Document::where('published',1)->get();



        // $reviews =  Review::where([['status','1'],['rating','5']])->with('user')->orderBy('created_at', 'desc')->get();
        $minReviews = 5;

        // Step 1: Get ALL 5-star reviews WITH profile image (no limit)
        $withImageReviews = Review::where('status', '1')
            ->where('rating', '5')
            ->whereHas('user', function ($query) {
                $query->whereNotNull('file_path'); // User has profile image
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Step 2: If less than 5, get enough WITHOUT profile image to make total at least 5
        if ($withImageReviews->count() < $minReviews) {
            $remaining = $minReviews - $withImageReviews->count();

            $noImageReviews = Review::where('status', '1')
                ->where('rating', '5')
                ->whereHas('user', function ($query) {
                    $query->whereNull('file_path'); // User has no profile image
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->take($remaining)
                ->get();
                // dd($noImageReviews);
            $reviews = $withImageReviews->merge($noImageReviews);
        } else {
            $reviews = $withImageReviews; // Already at least 5 reviews with images
        }

        // Final result: $reviews contains at least 5 reviews, possibly more if more with images exist
        // dd($reviews);
    
        $popular_ids = json_decode($data['popular'], true);
        $popular_categories = DocumentCategory::where('is_deleted', 0)
            ->whereIn('id', $popular_ids)
            ->with(['documents' => function($query) {
                $query->where('published', 1);
            }])
            ->get();
      
        return view('users.home.home',compact('data','home_category','reviews','popular_categories'));
    }

    public function getDocument($slug){
        $document = Document::where('slug',$slug)->where('published','1')->with(['documentAgreement.media','documentField.media','documentGuide','relatedDocuments','documentFaq'])->first();

        if (empty($document)) {
            abort(404);
        }
        $reviews = Review::where([['status','1'],['document_id',$document->id]])->with('media')->get();

        
        $showReviews = Review::where('status', 1)
            ->where('document_id', $document->id)
            ->where('rating', 5)    
            ->where('type', 'custom')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();



        $keys = [
            'reviews_heading',
            'reviews_sub_heading',
        ];

        $results = HomeContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'reviews_heading' => $results['reviews_heading']->value ?? null,
            'reviews_sub_heading' => $results['reviews_sub_heading']->value ?? null,
        ];

        $keys2 = [
            'guide_heading',
            'guide_button',
            'rating_text',
            'valid_in',
            'applicable_in',
            'related_heading',
            'related_description',
            'detail_page_letter_now_btn',
            'detail_page_job_recommend_btn',
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

        $results2 = GeneralSection::whereIn('key', $keys2)->get()->keyBy('key');
        $data2 = [
            'guide_heading' => $results2['guide_heading']->value ?? null,
            'guide_button' => $results2['guide_button']->value ?? null,
            'rating_text' => $results2['rating_text']->value ?? null,
            'valid_in' => $results2['valid_in']->value ?? null,
            'applicable_in' => $results2['applicable_in']->value ?? null,
            'related_heading' => $results2['related_heading']->value ?? null,
            'related_description' => $results2['related_description']->value ?? null,
            'detail_page_letter_now_btn' => $results2['detail_page_letter_now_btn']->value ?? null,
            'detail_page_job_recommend_btn' => $results2['detail_page_job_recommend_btn']->value ?? null,
            'document_faq_heading' => $results2['document_faq_heading']->value ?? null,
            'ultima_revision_text' => $results2['ultima_revision_text']->value ?? null,
            'formatos_disponibles_text' => $results2['formatos_disponibles_text']->value ?? null,
            'formatos_disponibles_data_text' => $results2['formatos_disponibles_data_text']->value ?? null,
            'aplicable_en_text' => $results2['aplicable_en_text']->value ?? null,
            'descargas_text' => $results2['descargas_text']->value ?? null,
            'descargas_data_text' => $results2['descargas_data_text']->value ?? null,
            'open_review_modal_button_text' => $results2['open_review_modal_button_text']->value ?? null,
            'review_modal_publicamente_text' => $results2['review_modal_publicamente_text']->value ?? null,
            'review_modal_nombre_publico_placeholder' => $results2['review_modal_nombre_publico_placeholder']->value ?? null,
            'review_modal_description_placeholder' => $results2['review_modal_description_placeholder']->value ?? null,
            'review_modal_not_login_message_text' => $results2['review_modal_not_login_message_text']->value ?? null,
            'review_modal_hace_text' => $results2['review_modal_hace_text']->value ?? null,
            'agreement_headline' => $results2['agreement_headline']->value ?? null,
            'agreement_short_description' => $results2['agreement_short_description']->value ?? null,

        ];

        $legal_section = GeneralSection::where('key','legal_section_heading')->with('media')->first();
        $agreements = GeneralSection::where('key','agreement')->with('media')->get();
        $legals = GeneralSection::where('key','legal')->with('media')->get();
        $guides = GeneralSection::where('key','guide_section')->get();
        $article_sections = ArticleSection::where('key','article')->get();
        $keys = [
            'example_section_heading',
            'example_section_description1',
            'example_section_description2',

        ];

        $article_results = ArticleSection::whereIn('key', $keys)->get()->keyBy('key');
        $article_data = [
            'example_section_heading' => $article_results['example_section_heading']->heading ?? null,
            'example_section_description1' => $article_results['example_section_description1']->description ?? null,
            'example_section_description2' => $article_results['example_section_description2']->description ?? null,
        ];
        $_Reviews = \App\Models\Review::latest()->get();



       $currentUrl = url()->current(); 
       $documentTitle = $document->title ?? 'Check this out!';

        // Social Media Links
        $dataLinks = [
            'fb_link' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($currentUrl),
            'pinterest_link' => 'https://www.pinterest.com/pin/create/button/?url=' . urlencode($currentUrl) . '&description=' . urlencode($documentTitle),
            'twitter_link' => 'https://twitter.com/intent/tweet?url=' . urlencode($currentUrl) . '&text=' . urlencode($documentTitle),
            'linkedin_link'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($currentUrl),
            'whatsapp_link'  => 'https://api.whatsapp.com/send?text=' . urlencode($documentTitle . ' ' . $currentUrl),
            // 'copy_link' => 'javascript:void(0)',
        ];

        return view('users.contracts.contract_details',compact('document','reviews','showReviews','data','data2','_Reviews','agreements','guides','article_sections','article_data','legal_section','legals','dataLinks', 'currentUrl', 'documentTitle'));
    }

    public function addReview(Request $request,$id){

        try{
            $user = auth()->user();
            $review = new Review();
            $review->document_id = $id;
            $review->user_id = auth()->user()->id;
            $review->city = $request->city;
            $review->description = $request->description;
            $review->rating = $request->rating;
            $review->is_show = 1;
            //$review->first_name = $request->first_name;

            $review->date = Carbon::parse($request->date)->format('Y-m-d');

            if(auth()->user()->is_admin == 1){
                $type = "custom";
                $review->status = 1;
            }
            else{
                $type = "user";
                $review->status = 0;
            }

            $review->type = $type;
            $review->save();
             if (!empty($request->public_name)) {
                $user->public_name = $request->public_name;
                $user->save();
            }
            return redirect()->back()->with('success', 'Review added successfully!');
        }catch(Exception $e){
            saveLog("Error:", "HomeController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    // This is the testing for the questions
    public function question_testing(){
        $questions = Question::with(['questionData', 'conditions', 'options', 'nextQuestion'])->get();
        $documentContents = DocumentRightSection::where('document_id', 3)->get();

        // Process each content and replace placeholders
        foreach ($documentContents as $content) {
            // Match and replace all #{number}# patterns
            $content->content = preg_replace_callback(
                '/#(\d+)#/',
                function ($matches) {
                    $classNumber = $matches[1];
                    return "<span class=\"answered_spns qidtarget-$classNumber\"></span>";
                },
                $content->content
            );

            if($content->secure_blur_content){
                $content->content= $this->encryptText($content->content, "nik");
            }
        }

        // dd($documentContents);

        return view('users.contracts.questions', compact('questions', 'documentContents'));
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
}
