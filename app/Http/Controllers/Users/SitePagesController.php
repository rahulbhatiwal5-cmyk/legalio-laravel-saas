<?php

namespace App\Http\Controllers\Users;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HowItWork;
use App\Models\Work;
use App\Models\QuestionAnswer;
use App\Models\FreeTrail;
use App\Models\TermsAndCondition;
use App\Models\PrivacyPolicy;
use App\Models\LegalNotice;
use App\Models\PricesContent;
use App\Models\Document;
use App\Models\Media;
use App\Models\Setting;
use App\Models\FaqCategory;
use App\Models\HomeContent;
use App\Models\Review;
use App\Models\UserCredit;
use App\Models\HelpCenter;
use App\Models\HelpYou;
use App\Models\WhoWeAre;
use App\Models\OurVision;
use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseArticle;
use App\Models\Plans;
use App\Services\MediaService;
use App\Models\Discount;
use Exception;

class SitePagesController extends Controller
{

    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }



    public function howItWork(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'short_description',
            'second_banner_img',
            'second_banner_heading',
            'second_banner_sub_heading',
            'button_label',
            'button_link',
            'meta_title'
        ];

        $results = HowItWork::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'short_description' => $results['short_description']->value ?? null,
            'second_banner_img' => str_replace('public/', '', $results['second_banner_img']->file_path ?? null),
            'second_banner_heading' => $results['second_banner_heading']->value ?? null,
            'second_banner_sub_heading' => $results['second_banner_sub_heading']->value ?? null,
            'button_label' => $results['button_label']->value ?? null,
            'button_link' => $results['button_link']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];

        $works = Work::with('media')->get();

        return view('users.site_meta.how_it_works',compact('data','works'));
    }

    public function Faq(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'meta_title'
        ];

        $results = QuestionAnswer::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' =>  $results['banner_image']->id ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'meta_title' => $results['meta_title']->value ?? null,
        ];

        $keys2 = [
            'reviews_heading',
            'reviews_sub_heading',
            'review_left_arrow',
            'review_right_arrow',
        ];

        $results2 = HomeContent::whereIn('key', $keys2)->get()->keyBy('key');
        $data2 = [
            'reviews_heading' => $results2['reviews_heading']->value ?? null,
            'reviews_sub_heading' => $results2['reviews_sub_heading']->value ?? null,
            'review_left_arrow' => str_replace('public/', '', $results2['review_left_arrow']->file_path ?? null),
            'review_right_arrow' => str_replace('public/', '', $results2['review_right_arrow']->file_path ?? null),
        ];

        $faqCategory = FaqCategory::all();

        // $faqs1 = QuestionAnswer::where([['key','faq'],['category_id','10']])->with('category')->get();
        // /
        // $faqs2 = QuestionAnswer::where([['key','faq'],['category_id','10']])->with('category')->get();
        // $faqs3 = QuestionAnswer::where([['key','faq'],['category_id','10']])->with('category')->get();
        $faqByCategory = QuestionAnswer::where('key', 'faq')
        ->with('category')
        ->get()
        ->groupBy('category_id');
        // $reviews = Review::where('status',1)->with('media')->get();

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


        return view('users.site_meta.faq',compact('data','faqCategory','faqByCategory','data2','reviews'));
    }

    public function termsAndConditions(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title'
        ];

        $results = TermsAndCondition::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];
        $termsAndCondition = TermsAndCondition::where('key','terms_and_condition')->get();

        return view('users.site_meta.terms_and_conditions',compact('termsAndCondition','data'));
    }


    public function privacyNotice(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title'
        ];

        $results = PrivacyPolicy::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];
        $policys = PrivacyPolicy::where('key','privacy_policies')->get();
        return view('users.site_meta.privacy_policy',compact('policys','data'));
    }
    public function legalNotice(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title'
        ];

        $results = LegalNotice::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];
        $legalNotices = LegalNotice::where('key','legal_notices')->get();

        return view('users.site_meta.legal_notice',compact('legalNotices','data'));
    }


    public function upload(Request $request){
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = generateFileName($file);
            $path = $file->storeAs('public', $filename);
            $url = asset('storage/' .$filename);

            return response()->json([
                'uploaded' => 1,
                'url' => $url
            ]);
        }
        return response()->json(['uploaded' => 0, 'error' => ['message' => 'File upload failed.']], 400);
    }

    public function prices(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'document_heading',
            'description_heading',
            'price_heading',
        ];

        $results = PricesContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'document_heading' => $results['document_heading']->value ?? null,
            'description_heading' => $results['description_heading']->value ?? null,
            'price_heading' => $results['price_heading']->value ?? null,
        ];

        $document = Document::where('published',1)->take(4)->get();
        $document_price_description = PricesContent::where('key', 'price_content')->with('documentname')->get();

        return view('users.site_meta.prices',compact('data','document','document_price_description'));
    }

    public function priceSubscription(Request $request){

          $userId = auth()->id();



            
    
            $document_id = $request->document_id;
            $check = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
                

            if ($check && $document_id) {

                
            //   $userCredit = UserCredit::where('user_id', $userId)
            //     ->where('balance', '>', 0)
            //     ->first();

            // if ($userCredit) {

            //     $userCredit->decrement('balance', 1);

                return view('users.checkout.order_confirmation');

            // }

                    
            }

             // Free trial check

              $trial = DB::table('subscriptions')
            ->where('user_id', $userId)
            ->where('status', 'trialing')
            ->exists();
                
            // $free_trail_check = FreeTrail::where('user_id', $userId)
            //             ->where('status', 'active')
            //             ->where('end_date', '>=', now())
            //             ->exists();

                        if($trial && $document_id){

                         return view('users.checkout.order_confirmation');

                        }
                        
    // $document_id = Session::get('document_id') ?? '';
    // $document = $document_id ? \App\Models\Document::find($document_id) : null;

       // ✅ NEW Check 3: Free trial expired + No paid subscription
    $expired_trial = FreeTrail::where('user_id', $userId)
                        ->where('status', 'expired')
                        ->exists();

    $has_paid_sub = DB::table('subscriptions')
                        ->where('user_id', $userId)
                        ->where('status', 'active')
                        ->exists();

    $show_upgrade_popup = $expired_trial && !$has_paid_sub ? 1 : 0;



        $keys = [
            'meta_title',
            'faq_heading',
            'faq_description',
            'subscription_title',
            'subscription_heading',
            'recommended_text',
            'subscription_description',
            'monthly_text',
            'yearly_text',
            'ahorra_text',
            'subscription_note',
            'per_month_text',
            'per_year_text',
            'subscription_btn_text',
            'one_time_heading',
            'one_time_description',
            'one_time_price_note',
            'one_time_btn_text',
        ];
        $results = PricesContent::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'meta_title' => $results['meta_title']->value ?? null,
            'faq_heading' => $results['faq_heading']->value ?? null,
            'faq_description' => $results['faq_description']->value ?? null,
            'subscription_title' => $results['subscription_title']->value ?? null,
            'subscription_heading' => $results['subscription_heading']->value ?? null,
            'recommended_text' => $results['recommended_text']->value ?? null,
            'subscription_description' => $results['subscription_description']->value ?? null,
            'monthly_text' => $results['monthly_text']->value ?? null,
            'yearly_text' => $results['yearly_text']->value ?? null,
            'ahorra_text' => $results['ahorra_text']->value ?? null,
            'subscription_note' => $results['subscription_note']->value ?? null,
            'per_month_text' => $results['per_month_text']->value ?? null,
            'per_year_text' => $results['per_year_text']->value ?? null,
            'subscription_btn_text' => $results['subscription_btn_text']->value ?? null,
            'one_time_heading' => $results['one_time_heading']->value ?? null,
            'one_time_description' => $results['one_time_description']->value ?? null,
            'one_time_price_note' => $results['one_time_price_note']->value ?? null,
            'one_time_btn_text' => $results['one_time_btn_text']->value ?? null,      
        ];
        $documents = Document::where('published',1)->get();
        $first_publish_document = Document::where('published',1)->first();
        if($first_publish_document){
            $first_document_price = $first_publish_document->doc_price;
        }
       
        $setting = web_setting('default_document_price');

        $currency_data = web_setting('country_currency_symbol');
        $currency_symbol = $currency_data->value;

        $default_price = $setting ? $setting->value : 0;

        $monthly_plans = Plans::where('interval', 'monthly')->orderBy('credit', 'asc')->get();
        $annual_plans = Plans::where('interval', 'yearly')->orderBy('credit', 'asc')->get();
        $price_faq = PricesContent::where('key', 'faq')->get();

        $plans = Plans::all();
        // $month_price = Plans::where('number_of_months', 24)->first()->price;
        // $no_of_months = Plans::where('number_of_months', 24)->first()->number_of_months;
        // $discounts = Discount::where('is_active',1)->first();
        // $default_discount = $discounts->percentage;
        // $discount_price = $month_price - ($month_price * ($default_discount / 100));
        // $save_price = $month_price - $discount_price;
        // $total_savings = $save_price * $no_of_months; 

        $discounts = Discount::where('is_active', 1)->first();
        $default_discount = $discounts?->percentage ?? 0;

        $plan = Plans::where('number_of_months', 24)->first();
        $month_price = $plan->price;
        $no_of_months = $plan->number_of_months;
        if ($no_of_months == 12) {
            $priceData = web_setting('12_month_price');
            $discount_price = $priceData->value;
        } elseif ($no_of_months == 24) {
            $priceData = web_setting('24_month_price');
            $discount_price = 9.90;
        } else {
            $discount_price = $month_price - ($month_price * ($default_discount / 100));
        }

        $save_price = $month_price - $discount_price;
        $total_savings = $save_price * $no_of_months;

        return view('users.site_meta.pricio',compact('documents','default_price','monthly_plans','annual_plans','data','first_document_price','price_faq','plans','discounts','default_discount', 'currency_symbol', 'show_upgrade_popup'));
    }

    public function getDocumnetPrice(Request $request){
        $documentId = $request->input('id');
        $document = Document::find($documentId);
        $setting = web_setting('default_document_price');
        $default_price = $setting ? $setting->value : 39.90;
       

        $price = $default_price;

        if($document && $price){
            return response()->json([
                'success' => true,
                'doc_price' => $price,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Document not found or price content not available.'], 404);
    }

    public function HelpCenter(Request $request){
        // category
        $category = KnowledgeBaseCategory::all();

        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_placeholder',
            'banner_image',
            'main_title',
            'sub_title',
            'faq_heading',
            'faq_description',
            'bottom_banner_image',
            'banner_heading',
            'banner_description',
            'button_text',
            'meta_title'
        ];

        $results = HelpCenter::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_placeholder' => $results['banner_placeholder']->value ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_title' => $results['main_title']->value ?? null,
            'sub_title' => $results['sub_title']->value ?? null,
            'faq_heading' => $results['faq_heading']->value ?? null,
            'faq_description' => $results['faq_description']->value ?? null,
            'bottom_banner_image' =>  str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
            'banner_heading' => $results['banner_heading']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'button_text' => $results['button_text']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
        ];

        $faqs = HelpCenter::where('key','faq')->get();
        // $help_you = HelpYou::limit(3)->with('media')->get();
        $category =KnowledgeBaseCategory::limit(3)->get();

        $mediaUrl = [];

        foreach($category as $cat){
            if ($cat && $cat->image) {
                $media = Media::where('id', $cat->image)->first();
                $mediaUrl[$cat->id] = $media ? $this->mediaService->getMediaUrl($media) : null;
            } else {
                $mediaUrl[] = null;
                $media = null;
            }
        }
        // dd($mediaUrl);

        return view('users.site_meta.support.support',compact('data','faqs','category','media','mediaUrl'));
    }

    public function knowledgeCategory($slug){

        //  $category = KnowledgeBaseCategory::all();

        // dd($article->article->title);
         $keys = [
             'title',
             'background_image',
             'banner_title',
             'banner_placeholder',
             'banner_image',
             'main_title',
             'sub_title',
             'faq_heading',
             'faq_description',
             'bottom_banner_image',
             'banner_heading',
             'banner_description',
             'button_text',
         ];

         $results = HelpCenter::whereIn('key', $keys)->get()->keyBy('key');
         $data = [
             'title' => $results['title']->value ?? null,
             'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
             'banner_title' => $results['banner_title']->value ?? null,
             'banner_placeholder' => $results['banner_placeholder']->value ?? null,
             'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
             'main_title' => $results['main_title']->value ?? null,
             'sub_title' => $results['sub_title']->value ?? null,
             'faq_heading' => $results['faq_heading']->value ?? null,
             'faq_description' => $results['faq_description']->value ?? null,
             'bottom_banner_image' =>  str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
             'banner_heading' => $results['banner_heading']->value ?? null,
             'banner_description' => $results['banner_description']->value ?? null,
             'button_text' => $results['button_text']->value ?? null,
         ];

         $faqs = HelpCenter::where('key','faq')->get();
         // $help_you = HelpYou::limit(3)->with('media')->get();
         $category =KnowledgeBaseCategory::all();
         $article = KnowledgeBaseCategory::where('slug', $slug)->with('article')->first();





        return view('users.site_meta.support.knowledgebase_category',compact('data','faqs','category','article'));

    }

    public function knowledgeArticle($slug){
         $keys = [
             'title',
             'background_image',
             'banner_title',
             'banner_placeholder',
             'banner_image',
             'main_title',
             'sub_title',
             'meta_title',
             'faq_heading',
             'faq_description',
             'bottom_banner_image',
             'banner_heading',
             'banner_description',
             'button_text',
         ];

         $results = HelpCenter::whereIn('key', $keys)->get()->keyBy('key');
         $data = [
             'title' => $results['title']->value ?? null,
             'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
             'banner_title' => $results['banner_title']->value ?? null,
             'banner_placeholder' => $results['banner_placeholder']->value ?? null,
             'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
             'main_title' => $results['main_title']->value ?? null,
             'sub_title' => $results['sub_title']->value ?? null,
             'faq_heading' => $results['faq_heading']->value ?? null,
             'faq_description' => $results['faq_description']->value ?? null,
             'bottom_banner_image' =>  str_replace('public/', '', $results['bottom_banner_image']->file_path ?? null),
             'banner_heading' => $results['banner_heading']->value ?? null,
             'banner_description' => $results['banner_description']->value ?? null,
             'button_text' => $results['button_text']->value ?? null,
             'meta_title' => $results['meta_title']->value ?? null,
         ];

         $faqs = HelpCenter::where('key','faq')->get();
         // $help_you = HelpYou::limit(3)->with('media')->get();
         $category =KnowledgeBaseCategory::all();
         $article = KnowledgeBaseArticle::where('slug', $slug)->with('category')->first();
         $articlecontent = KnowledgeBaseArticle::where('slug', $slug)->with('contents')->first();
        //  dd($articlecontent);
        // dd($article->heading);

        return view('users.site_meta.support.knowledgebase_article',compact('data','faqs','category','article','articlecontent'));

    }

    public function whoWeAre(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'image',
            'heading',
            'description',
            'offer_image',
            'offer_heading',
            'offer_description',
            'meta_title'
        ];

        $results = WhoWeAre::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'image' => str_replace('public/', '', $results['image']->file_path ?? null),
            'heading' => $results['heading']->value ?? null,
            'description' => $results['description']->value ?? null,
            'offer_image' => str_replace('public/', '', $results['offer_image']->file_path ?? null),
            'offer_heading' =>  $results['offer_heading']->value ?? null,
            'offer_description' =>  $results['offer_description']->value ?? null,
            'meta_title' =>  $results['meta_title']->value ?? null,

        ];

        $keys2 = [
            'reviews_heading',
            'reviews_sub_heading',
            'review_left_arrow',
            'review_right_arrow',
        ];

        $results2 = HomeContent::whereIn('key', $keys2)->get()->keyBy('key');
        $data2 = [
            'reviews_heading' => $results2['reviews_heading']->value ?? null,
            'reviews_sub_heading' => $results2['reviews_sub_heading']->value ?? null,
            'review_left_arrow' => str_replace('public/', '', $results2['review_left_arrow']->file_path ?? null),
            'review_right_arrow' => str_replace('public/', '', $results2['review_right_arrow']->file_path ?? null),
        ];

        $visions = OurVision::with('media')->get();
        $offers = WhoWeAre::limit(2)->where('key','offer')->get();

        // $reviews = Review::where('status',1)->with('media')->get();


        // $settings = Setting::where('type', 'review')->pluck('value', 'key')->toArray();
        // // dd($settings);
        // $minNumReviews = (int) ($settings['min_num_reviews_for_publish'] ?? 0);
        // $minAvgRating = (float)  ($settings['min_avg_rating_for_publish'] ?? 0);
        // $minRatingAutoPublish = (float)  ($settings['min_rating_for_auto_publish'] ?? 0);
        // // dd($minRatingAutoPublish);
        // $reviews = Review::where('status', '1')
        //     ->where('rating', '>=', $minRatingAutoPublish)
        //     ->with('user')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        //     // dd($reviews);
        // if ($reviews->count() < $minNumReviews && $reviews->avg('rating') < $minAvgRating) {
        //     $reviews = collect();
        // }

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
        // dd($reviews);

        return view('users.site_meta.who_we_are',compact('data','visions','offers','reviews','data2'));
    }

    // public function getPlanPrice(Request $request){
    //     try{
    //         $number_of_month = $request->number_of_months;
    //         $plan_id = $request->plan_id;
    //         $price = $request->price;

    //         $discounts = Discount::where('is_active',1)->first();
    //         $default_discount = $discounts->percentage;
    //         $discount_price = $price - ($price * ($default_discount / 100));
    
    //         return response()->json([
    //             'success' => true,
    //             'price' => $price,
    //             'discount_price' => $discount_price,
    //         ]);
 
    //     }catch(Exception $e){
    //         saveLog("Get plan price:", "CheckoutController", $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function getPlanPrice(Request $request){
        try{
            $number_of_month = (int) $request->number_of_months;
            $plan_id = $request->plan_id;
            $price = (float) $request->price;


            if($number_of_month == 12){
                $discount_price = 9.90;
            } 
            elseif($number_of_month == 24){
                $discount_price = 9.90;
            } 
            else {
                $discount_price = $price;
            }

            return response()->json([
                'success' => true,
                'price' => $price,
                'discount_price' => $discount_price,
            ]);
        }catch(Exception $e){
            saveLog("Get plan price:", "CheckoutController", $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
