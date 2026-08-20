<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FileUploadService;
use Illuminate\Support\Str;
use App\Models\QuestionAnswer;
use App\Models\PrivacyPolicy;
use App\Models\LegalNotice;
use App\Models\User;
use App\Models\Order;
use App\Models\FaqCategory;
use App\Models\WhoWeAre;
use App\Models\OurVision;
use App\Models\Media;
use App\Models\BillingAdress;
use App\Models\FreeSubscription;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Document;
use DB;
use Illuminate\Pagination\Paginator;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Carbon\Carbon;
use Imagick;
use Illuminate\Support\Facades\Log;

use Hash;
use File;
use App\Services\DocxToPagesService;
use App\Models\GrantedDocument;
use App\Models\UserCredit;
use App\Models\Subscription;
use App\Models\Plans;
use App\Models\FreeGrantAccess;
use Illuminate\Support\Facades\Storage;


class AllPagesController extends Controller
{

    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService){
        $this->fileUploadService = $fileUploadService;
    }

    public function faqCategory(){
        return view('admin.site_meta.faqs.add_faq_category');
    }

    public function addCategory(Request $request)
    {
        $rules = [
            'name' => 'required',
            'slug' => 'required|unique:faq_categories,slug',
            'description'=>'required',
        ];

        if ($request->id) {
            $rules['slug'] = 'required|unique:faq_categories,slug,' . $request->id;
        }

        $request->validate($rules);

        $faqCategory = $request->id ? FaqCategory::find($request->id) : new FaqCategory;

        $faqCategory->category_name = $request->name;
        $faqCategory->slug = $request->slug;
        $faqCategory->description = $request->description;
        $faqCategory->save();

        return redirect()->route('admin.dashboard.faq_category')->with(
            'success',
            $request->id ? 'FAQ category updated' : 'FAQ category added'
        );
    }


    public function allFaqCategory(){
        $faqCategory = FaqCategory::all();
        return view('admin.site_meta.faqs.faq_category',compact('faqCategory'));
    }

    public function editFaqCategory($slug){
        $faqCategory = FaqCategory::where('slug',$slug)->first();
        return view('admin.site_meta.faqs.add_faq_category',compact('faqCategory'));
    }

    public function deleteFaqCategory($slug){
        $faqCategory = FaqCategory::where('slug',$slug)->first();
        if (!$faqCategory) {
            return redirect()->back()->with('error', 'FAQ category not found.');
        }

        $faqCategory->delete(); // correct instance method

        return redirect()->back()->with('success', 'FAQ category deleted successfully.');
    }

     public function faq(){
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'meta_title',
            'meta_description'
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
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $faqCategory = FaqCategory::all();
        $faqs = QuestionAnswer::where('key','faq')->with('category')->get();

        return view('admin.site_meta.faqs.faqs',compact('faqCategory','data','faqs'));
    }

        public function uploadEditorImage(Request $request)
    {

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // Optional: customize filename
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Store file in storage/app/public/editor-images
            $path = $file->storeAs('editor-images', $filename, 'public');

            // Create public URL: /storage/editor-images/filename.jpg
            $url = asset('storage/' . $path);

            return response()->json(['url' => $url]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
    public function faqAdd(Request $request){
        try{
            if($request->hasFile('background_image')){
                $faq = QuestionAnswer::where('key','background_image')->first();

                $background_image = $request->file('background_image');
                $directory = "public/faq_images";
                $filename = generateFileName($background_image);
                $filepath = $background_image->storeAs($directory, $filename);
                $faq->value = $filename;
                $faq->file_path = $filepath;
                $faq->update();
            }

            if($request->hasFile('banner_image')){
                $faq = QuestionAnswer::where('key','banner_image')->first();

                $banner_image = $request->file('banner_image');
                $directory = "public/faq_images";
                $filename = generateFileName($banner_image);
                $filepath = $banner_image->storeAs($directory, $filename);

                $faq->value = $filename;
                $faq->file_path = $filepath;
                $faq->update();
            }

            if($request->has('new_question')){
                for($i=0; $i<count($request->new_question); $i++){
                    $question = $request->new_question[$i];

                    if($request->has('new_answer')){
                        $answer = $request->new_answer[$i];
                    }

                    if($request->has('new_category')){
                        $category = $request->new_category[$i];
                    }

                    $faq = new QuestionAnswer;
                    $faq->key = 'faq';
                    $faq->question = $question;
                    $faq->answer = $answer;
                    $faq->category_id = $category;
                    $faq->save();
                }
            }

            if($request->has('category')){
                foreach($request->category as $index=>$value){
                    $faq = QuestionAnswer::find($index);
                    $faq->category_id = $value;
                    $faq->update();
                }

                foreach($request->question as $key=>$val){
                    $faq = QuestionAnswer::find($key);
                    $faq->question = $val;
                    $faq->update();
                }

                foreach($request->answer as $idx=>$vlu){
                    $faq = QuestionAnswer::find($idx);
                    $faq->answer = $vlu;
                    $faq->update();
                }
            }

            $fields = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description'
            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $faq = QuestionAnswer::where('key', $key)->first();
                    if($faq){
                        $faq->value = $request->$input;
                        $faq->update();
                    }else{
                        $faq = new QuestionAnswer;
                        $faq->key = $key;
                        $faq->value = $request->$input;
                        $faq->save();
                    }
                }
            }

            if($request->removefaq != null){
                $deleteIds = explode(',', $request->removefaq);
                foreach($deleteIds as $id){
                    $remove_faq = QuestionAnswer::find($id);
                    if($remove_faq){
                        $remove_faq->delete();
                    }
                }
            }

            if($request->bg_image_id != null){
                $faq = QuestionAnswer::where('id',$request->bg_image_id)->first();
                $file_path = getFilePath($faq->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $faq->value = null;
                $faq->file_path = null;
                $faq->save();
            }

            if($request->baner_image_id != null){
                $faq = QuestionAnswer::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($faq->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $faq->value = null;
                $faq->file_path = null;
                $faq->save();
            }

            return redirect()->back()->with("success", "Data successfully updated.");

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function legalNotice()
    {
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title',
            'meta_description'
        ];

        $results = LegalNotice::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'bg_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' => $results['banner_image']->id ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];
        $legal_notice = LegalNotice::where('key','legal_notices')->get();

        return view('admin.site_meta.terms_and_conditions.legal_notice',compact('data','legal_notice'));
    }
    public function addLegalNotice(Request $request){
        try{
            if($request->hasFile('background_image')){
                $file = $request->file('background_image');

                $filename = generateFileName($file);

                $directory = 'public/legal_notices';
                $path = $file->storeAs($directory, $filename);

                $legalNotice = LegalNotice::where('key','background_image')->first();

                $legalNotice->value = $filename;

                $legalNotice->file_path = $path;

                $legalNotice->update();
            }

            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $filename = generateFileName($file);
                $directory = 'public/legal_notices';
                $path = $file->storeAs($directory, $filename);

                $legalNotice = LegalNotice::where('key','banner_image')->first();
                $legalNotice->value = $filename;
                $legalNotice->file_path = $path;
                $legalNotice->update();
            }

            if($request->has('terms')){
                foreach($request->terms as $index=>$value){
                    $legalNotice = LegalNotice::find($index);
                    $legalNotice->terms = $value;
                    $legalNotice->update();
                }

                foreach($request->condition as $key=>$val){
                    $legalNotice = LegalNotice::find($key);
                    $legalNotice->condition = $val;
                    $legalNotice->update();
                }
            }

            if($request->has('new_terms')){
                for($i=0; $i<count($request->new_terms); $i++){
                    $new_terms = $request->new_terms[$i];

                    $legal_notice = new LegalNotice;
                    $legal_notice->key = 'legal_notices';
                    $legal_notice->type = 'terms';
                    $legal_notice->terms = $new_terms;

                    if($request->new_condition != null){
                        $new_condition = $request->new_condition[$i];
                    }
                    $legal_notice->condition = $new_condition;
                    $legal_notice->save();
                }
            }

            $keys = [
                'title' => 'title',
                'main_heading' => 'main_heading',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
            ];

            foreach($keys as $key=>$input){
                if($request->has($input)){
                    $legal_notice = LegalNotice::where('key', $key)->first();
                    if($legal_notice){
                        $legal_notice->value = $request->$input;
                        $legal_notice->update();
                    }else{
                        $legal_notice = new LegalNotice;
                        $legal_notice->key = $key;
                        $legal_notice->value = $request->$input;
                        $legal_notice->save();
                    }
                }
            }

            if($request->bg_img_id != null){
                $legal_notice = LegalNotice::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($legal_notice->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $legal_notice->value = null;
                $legal_notice->file_path = null;
                $legal_notice->update();
            }

            if($request->baner_image_id != null){
                $legal_notice = LegalNotice::where( 'id',$request->baner_image_id)->first();
                $file_path = getFilePath($legal_notice->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $legal_notice->value = null;
                $legal_notice->file_path = null;
                $legal_notice->update();
            }

            if($request->remove_ids != null){
                $removeIds = explode(',', $request->remove_ids);
                $legalNotice = LegalNotice::whereIn('id',$removeIds)->delete();
            }

            return redirect()->back()->with('success','Legal Notice successfully saved');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function privecyPolicy()
    {
        $keys = [
            'title',
            'background_image',
            'banner_title',
            'banner_description',
            'banner_image',
            'main_heading',
            'meta_title',
            'meta_description'
        ];

        $results = PrivacyPolicy::whereIn('key',$keys)->get()->keyBy('key');
        $data = [
            'title_name' => $results['title']->value ?? null,
            'bg_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' => $results['banner_image']->id ?? null,
            'banner_image' => str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'main_heading' => $results['main_heading']->value ?? null,
            'meta_title' => $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $privacy_policy = PrivacyPolicy::where('key','privacy_policies')->get();

        return view('admin.site_meta.terms_and_conditions.privacy_policy',compact('data','privacy_policy'));
    }

    public function addPrivacyPolicy(Request $request){
        try{
            if($request->hasFile('background_image')){
                $file = $request->file('background_image');

                $filename = generateFileName($file);

                $directory = 'public/privacy_and_policy';
                $path = $file->storeAs($directory, $filename);

                $privacyPolicy = PrivacyPolicy::where('key','background_image')->first();

                $privacyPolicy->value = $filename;

                $privacyPolicy->file_path = $path;

                $privacyPolicy->update();
            }

            if($request->hasFile('banner_image')){
                $file = $request->file('banner_image');
                $filename = generateFileName($file);
                $directory = 'public/privacy_and_policy';
                $path = $file->storeAs($directory, $filename);

                $privacyPolicy = PrivacyPolicy::where('key','banner_image')->first();
                $privacyPolicy->value = $filename;
                $privacyPolicy->file_path = $path;
                $privacyPolicy->update();
            }

            if($request->has('terms')){
                foreach($request->terms as $index=>$value){
                    $privacyPolicy = PrivacyPolicy::find($index);
                    $privacyPolicy->terms = $value;
                    $privacyPolicy->update();
                }

                foreach($request->condition as $key=>$val){
                    $privacyPolicy = PrivacyPolicy::find($key);
                    $privacyPolicy->condition = $val;
                    $privacyPolicy->update();
                }
            }

            if($request->has('new_terms')){
                for($i=0; $i<count($request->new_terms); $i++){
                    $new_terms = $request->new_terms[$i];

                    $privacy_and_policy = new PrivacyPolicy;
                    $privacy_and_policy->key = 'privacy_policies';
                    $privacy_and_policy->type = 'terms';
                    $privacy_and_policy->terms = $new_terms;

                    if($request->new_condition != null){
                        $new_condition = $request->new_condition[$i];
                    }
                    $privacy_and_policy->condition = $new_condition;
                    $privacy_and_policy->save();
                }
            }

            $keys = [
                'title' => 'title',
                'main_heading' => 'main_heading',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description'
            ];

            foreach($keys as $key=>$input){
                if($request->has($input)){
                    $privacy_and_policy = PrivacyPolicy::where('key', $key)->first();
                    if($privacy_and_policy){
                        $privacy_and_policy->value = $request->$input;
                        $privacy_and_policy->update();
                    }else{
                        $privacy_and_policy = new PrivacyPolicy;
                        $privacy_and_policy->key = $key;
                        $privacy_and_policy->value = $request->$input;
                        $privacy_and_policy->save();
                    }
                }
            }

            if($request->bg_img_id != null){
                $privacy_and_policy = PrivacyPolicy::where('id',$request->bg_img_id)->first();
                $file_path = getFilePath($privacy_and_policy->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $privacy_and_policy->value = null;
                $privacy_and_policy->file_path = null;
                $privacy_and_policy->update();
            }

            if($request->baner_image_id != null){
                $privacy_and_policy = PrivacyPolicy::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($privacy_and_policy->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $privacy_and_policy->value = null;
                $privacy_and_policy->file_path = null;
                $privacy_and_policy->update();
            }

            if($request->remove_ids != null){
                $removeIds = explode(',', $request->remove_ids);
                $privacyPolicy = PrivacyPolicy::whereIn('id',$removeIds)->delete();
            }

            return redirect()->back()->with('success','Privacy And Policy successfully saved');
        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function removePolicy(Request $request){
        $ids = $request->ids;
        $deletePolicy = PrivacyPolicy::whereIn('id', $ids)->delete();

        // Return success or error based on deletion result
        if($deletePolicy > 0){
            return response()->json(['success' => true, 'message' => 'Privacy Policy deleted successfully!']);
        }else{
            return response()->json(['success' => false, 'message' => 'Privacy Policy not found.']);
        }
    }

    public function allUsers(){
        $users = User::all();
        // dd($users);
        return view('admin.users.all_users',compact('users'));
    }


    public function orders(){
        $orders = Order::orderBy('created_at', 'desc')->Paginate(50);

        return view('admin.orders.orders',compact('orders'));
    }

    public function addOrder(){
        return view('admin.orders.add_order');
    }

    public function ordersDetail($id){
        $order = Order::where('order_num',$id)->first();
        // $userAddress = $order->user->addresses->first();
        $userAddress = $order->user && $order->user->addresses ? $order->user->addresses->first() : null;
        $grantedDocument = GrantedDocument::where([['user_id',$order->user?->id],['document_id',$order->document?->id],['order_id',$order?->id]])->first();
        // $grantedDocIds = $grantedDocument ? json_decode($grantedDocument->granted_document_id, true) : [];
        // $grantedDocIds = $grantedDocument->granted_document_id;
        if ($grantedDocument && !empty($grantedDocument->granted_document_id)) {
            $decoded = json_decode($grantedDocument->granted_document_id, true);

            // If it's valid JSON and gives an array
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $grantedDocIds = $decoded;
            } else {
                // Otherwise treat it as a single value
                $grantedDocIds = [$grantedDocument->granted_document_id];
            }
        } else {
            $grantedDocIds = [];
        }


        $months = $grantedDocument?->free_interval;
        $free_interval = str_replace('months', '', $months);
        // dd($free_interval);
        $plan_id = $grantedDocument?->plan_id;
        
        $plans = Plans::all();
        $free_grant = web_setting('free_grant_expiration')->value;

        $totalOrder = Order::where([['user_id',$order->user_id],['status',1]])->count();
        $totalRevenue = Order::where([['user_id',$order->user_id],['status',1]])
        ->with(['transactions' => function ($query) {
            $query->where('status', 'succeeded');
        }])->get()
        ->sum(function ($sum) {
            return $sum->transactions->total_amount ?? 0;
        });

        $averageOrderValue = $totalOrder > 0 ? $totalRevenue / $totalOrder : 0;

        Carbon::setLocale('en');
        $date = Carbon::parse($order->transactions->created_at ?? '');
        $formattedDate=$date->translatedFormat('F d, Y');

        $documents = Document::where('published','1')->get();

        // check the order payment method

        $payment = $order->transactions;

        $freeGrant = FreeGrantAccess::where('order_id', $order?->id)->where('is_granted',1)->with('grantedDocument','freeSubscription')->first();

        if($payment?->type=="stripe"){
            // Stripe::setApiKey(env('STRIPE_SECRET'));
            try{
                $paymentIntent = PaymentIntent::retrieve($order->transactions->payment_intent);
                $paymentMethodId = $paymentIntent->payment_method;
                $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);

                $cardDetails = [
                    'brand' => $paymentMethod->card->brand, // Visa, MasterCard, etc.
                    'last4' => $paymentMethod->card->last4, // Last 4 digits of the card
                    'exp_month' => $paymentMethod->card->exp_month,
                    'exp_year' => $paymentMethod->card->exp_year,
                ];

                $subscription = null;
                if($order->order_type == 'subscription'){
                    $subscription = Subscription::where('stripe_subscription_id',$order?->stripe_subscription_id)->with('plan')->first();
                }

            }
            catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            //  dd($cardDetails);
            return view('admin.orders.order_detail',compact('id','order','totalOrder','totalRevenue','averageOrderValue','payment','paymentIntent','cardDetails','formattedDate','userAddress','documents','grantedDocIds','subscription','plans','free_interval','plan_id','free_grant','freeGrant'));

        }
        elseif( $payment?->type=="paypal"){
            $paymentMethod = $order->paypal_order_id;
            //   dd( $paymentMethod);
            return view('admin.orders.order_detail',compact('id','order','totalOrder','totalRevenue','averageOrderValue','payment','paymentMethod','formattedDate','userAddress','documents','grantedDocIds' ,'freeGrant','plans','free_interval','plan_id'));
        }
        else {
            dd("Something went wrong...");
        }

    }

    public function updateCustomerInformation(Request $request, $id){
        // dd($request->all());

        $order = Order::where('order_num',$id)->first();
        $user = $order->user;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->update();

        return redirect()->back()->with('success', 'Customer Information Updated Successfully');
    }

    public function updateBillingDetails (Request $request, $id){
        // return $request->all;

        $order = Order::where('order_num',$id)->first();
        $userAddress = $order->user->addresses->first();
        if (!$userAddress) {
            $userAddress = new BillingAdress();
            $userAddress->user_id = $order->user->id;
            $userAddress->country =$request->country;
            $userAddress->address =$request->address;
            $userAddress->company =$request->company;

            $userAddress->company_2 =$request->company_2;

            $userAddress->city =$request->city;
            $userAddress->postal_code =$request->postal_code;
            $userAddress->state =$request->state;

            $userAddress->save();
        }else {
            $userAddress = BillingAdress::where('user_id',$order->user->id)->first();
            $userAddress->country =$request->country;
            $userAddress->address =$request->address;
            $userAddress->company =$request->company;

            $userAddress->company_2 =$request->company_2;

            $userAddress->city =$request->city;
            $userAddress->postal_code =$request->postal_code;
            $userAddress->state =$request->state;

            $userAddress->update();
        }

        return redirect()->back()->with('success', 'Billing Details Updated Successfully');
    }

    public function updateOrdersDetail(Request $request, $id)
    {
        // return $request->all();

        $order = Order::where('order_num', $id)->firstOrFail();
        $created_at = $request->created_at_date . ' ' . $request->created_at_time . ':00';

        DB::beginTransaction();

        try {
            if ($order->transactions) {
                $order->transactions->payment_type = $request->payment_type;
                $order->transactions->save();
            } else {
                $order->transactions()->create([
                    'payment_type' => $request->payment_type,
                    'order_id' => $order->id,
                ]);
            }

            $order->status = $request->status;

            $documentIds = [];

            if(is_array($request->free_grant_access) && in_array('all', $request->free_grant_access)) {
                $documentIds = Document::where('published', '1')->pluck('id')->toArray();
                $documentIds = array_map('strval',  $documentIds);
            }else{
                $documentIds = $request->free_grant_access;
            }

            $documentIdsJson = json_encode($documentIds);

            $plan_id = $request->grant_Free_Subscription;
            

            $userCredit = UserCredit::firstOrNew(['user_id' => $order->user->id]);
            $userCredit->balance += $free_credit;
            $userCredit->save();

            $order->save();

            DB::commit();

            return redirect()->back()->with('success', 'Data updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function saveFreeGrantDocument(Request $request){
        // return $request->all();
        try{
            $order_id = $request->order_id;
            $documentIds = is_array($request->document_id) ? $request->document_id : [];

            $free_interval = $request->duration_days;
            $interval_type = 'month';
            
            $order = Order::where('order_num', $order_id)->firstOrFail();
            $document_id = $order->document_id;
 
            $freeGrant = FreeGrantAccess::where([['order_id', $order->id], ['grant_type', 'document']])->first();
            if($freeGrant){
                $freeGrant->order_id = $order->id;
                $freeGrant->grant_type = 'document';
                $freeGrant->is_granted = 1;
            }else{
                $freeGrant = new FreeGrantAccess;
                $freeGrant->order_id = $order->id;
                $freeGrant->grant_type = 'document';
                $freeGrant->is_granted = 1;
                $freeGrant->save();
            }
           
            $granted_document = GrantedDocument::where([['user_id', $order->user->id],['document_id', $document_id]])->first();
    
            if($granted_document){
                $granted_document->grant_access_id = $freeGrant->id;
                $granted_document->granted_document_id = json_encode($documentIds);
                $granted_document->order_id = $order->id;
                $granted_document->free_interval = $free_interval;
                $granted_document->interval_type = $interval_type;
                $granted_document->start_date = Carbon::now()->toDateString(); 
                $granted_document->update();
            }else{
                $granted_document = new GrantedDocument();
                $granted_document->grant_access_id = $freeGrant->id;
                $granted_document->user_id = $order->user->id;
                $granted_document->order_id = $order->id;
                $granted_document->document_id = $document_id;
                $granted_document->granted_document_id = json_encode($documentIds);
                $granted_document->free_interval = $free_interval;
                $granted_document->interval_type = $interval_type;
                $granted_document->start_date = Carbon::now()->toDateString();
                $granted_document->save();
            }

            return response()->json(['success' => true, 'message' => 'Granted document saved successfully.']);
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to save granted document: ' . $e->getMessage());
        }
    }

    public function addSubscriptionToOrder(Request $request){
        try{
            $order_id = $request->order_id;
            $plan_id = $request->plan_id;

            $order = Order::where('order_num', $order_id)->firstOrFail();

            $document_id = $order->document_id;
 
            $freeGrant = FreeGrantAccess::where([['order_id', $order->id], ['grant_type', 'subscription']])->first();
            if($freeGrant){
                $freeGrant->order_id = $order->id;
                $freeGrant->grant_type = 'subscription';
                $freeGrant->is_granted = 1;
            }else{
                $freeGrant = new FreeGrantAccess;
                $freeGrant->order_id = $order->id;
                $freeGrant->grant_type = 'subscription';
                $freeGrant->is_granted = 1;
                $freeGrant->save();
            }

            $plan = Plans::findOrFail($plan_id);

            if(!$plan->stripe_product_id || !$plan->stripe_price_id){
                throw new \Exception('Invalid plan or missing Stripe product/price ID.');
            }

            $user = $order?->user;
            $userId = $user?->id;

            $order_type = $order?->order_type;
            $customer_id = $order?->user?->stripe_cus_id;
            
            $price_id = $plan?->stripe_price_id;
            $document_limit = web_setting('fair_use_document_limit')->value;
            $interval = $plan?->number_of_months;
            $interval_type = 'month';

            $stripe_secret_key = web_setting('stripe_secret_key')->value;
            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            if($order_type == 'subscription'){
                $activeSubscription = Subscription::where('stripe_customer_id', $customer_id)
                ->where('status', 'active')
                ->first();

                return $activeSubscription;
                
                if(!$activeSubscription){
                    throw new \Exception('No active subscription found.');
                }
                      
                $subscription = $stripe->subscriptions->retrieve($activeSubscription->stripe_subscription_id, []);
                $current_period_end = $subscription['current_period_end']; 

                $free_trial_start = Carbon::createFromTimestamp($current_period_end);
                $trial_end = $free_trial_start->copy()->addMonths($interval);

                $stripe->subscriptions->update($activeSubscription->stripe_subscription_id, [
                    'pause_collection' => [
                        'behavior' => 'void', 
                        'resumes_at' => $trial_end->timestamp,
                    ],
                    'metadata' => [
                        'order_id' => $order->id,
                        'pause_reason' => 'Admin granted free subscription',
                        'resumes_at_date' => $trial_end->toDateString(),
                    ],
                ]);

                $free_subscription = FreeSubscription::where('user_id', $userId)->where('order_id', $order->id)->first();

                if($free_subscription){
                    $free_subscription->update(
                        ['type' => 'subscription']
                    );
                }else{
                    $free_subscription = new FreeSubscription;
                    $free_subscription->grant_access_id = $freeGrant->id;
                    $free_subscription->user_id = $userId;
                    $free_subscription->order_id = $order->id;
                    $free_subscription->type = 'subscription';
                    $free_subscription->save();
                }

            }else if($order_type == 'one_time'){
                $trial_end = Carbon::now()->addMonths($interval);

                if(!$customer_id){
                    $customer = $stripe->customers->create([
                        'email' => $order?->user->email,
                        'name'  => $order?->user->first_name,
                    ]);
                    $customer_id = $customer->id;
                    $order->user->stripe_cus_id = $customer_id;
                    $order->user->save();
                }
            
                $trial_end = Carbon::now()->addMonths($interval);
            
                $stripeSubscription = $stripe->subscriptions->create([
                    'customer' => $customer_id,
                    'items' => [
                        ['price' => $price_id],
                    ],
                    'trial_end' => $trial_end->timestamp,
                    'metadata' => [
                        'order_id' => $order->id,
                        'free_grant_reason' => 'Admin granted free subscription',
                    ],
                ]);

                $order->stripe_subscription_id = $stripeSubscription->id;
                $free_subscription = FreeSubscription::where('user_id', $userId)->where('order_id', $order->id)->first();

                if($free_subscription){
                    $free_subscription->update(
                        ['type' => 'subscription']
                    );
                }else{
                    $free_subscription = new FreeSubscription;
                    $free_subscription->grant_access_id = $freeGrant->id;
                    $free_subscription->user_id = $userId;
                    $free_subscription->order_id = $order->id;
                    $free_subscription->type = 'one_time';
                    $free_subscription->save();
                }
            }
            $order->plan_id = $plan->id;
            $order->save();

            return response()->json(['success' => true, 'message' => 'Subscription plan added to order successfully.']);
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to add subscription to order: ' . $e->getMessage());
        }
    }

    public function print($orderId)
    {
        $order = Order::with(['user.addresses', 'document'])->findOrFail($orderId);

        $address = optional($order->user->addresses->first());
        $document = $order->document;

        $price = $order->amount ?? 0;

        $data = [
            'customer_name'    => $order->user->first_name,
            'customer_address' => $address->address ?? 'N/A',
            'customer_email'   => $address->user->email ?? 'N/A',
            'invoice_id'       => $order->order_num,
            'invoice_date'     => $order->created_at->format('d M, Y'),

            'items' => [[
                'image'       => $order->document->document_image,
                'description' => $document->title ?? 'Document',
                'price'       => $price,
                'quantity'    => 1,
            ]],

            'subtotal'       => $price,
            'processing_fee' => $order->processing_fee ?? 0,
            'tax'            => $order->tax ?? 0,
        ];

        $data['total'] = $data['subtotal'] + $data['processing_fee'] + $data['tax'];

        return view('admin.orders.print_invoice', $data);
    }

    public function downloadInvoice($id, DocxToPagesService $DocxToPagesService) {
        $view = 'admin.orders.Invoice_pdf_template'; // Blade view for PDF
        $order = Order::with(['user.addresses', 'document'])->findOrFail($id);

        $address = optional($order->user->addresses->first());
        $document = $order->document;
        $price = $order->amount ?? 0;

        // Get the image path from the document
        $originalPath = $order->document->document_image;
        \Log::info("Original image path: {$originalPath}");

        // Clean the path: Remove domain and protocol if it's a URL
        $cleanPath = $originalPath;

        // Check if this is a URL (starts with http:// or https://)
        if (preg_match('|^https?://|', $cleanPath)) {
            // Extract just the path part after the domain
            $parsedUrl = parse_url($cleanPath);
            if (isset($parsedUrl['path'])) {
                // Remove the leading slash if present
                $cleanPath = ltrim($parsedUrl['path'], '/');

                // If the path contains 'assets/img', only keep that part and what follows
                if (preg_match('|(assets/img/.*)|', $cleanPath, $matches)) {
                    $cleanPath = $matches[1];
                }
            }
        }

        \Log::info("Cleaned path: {$cleanPath}");

        // Now get the absolute file path for this image
        $absolutePath = public_path($cleanPath);
        \Log::info("Absolute path: {$absolutePath}");

        // Let's try different approaches for the SVG
        $svgApproaches = [
            'base64' => null,
            'data_uri' => null,
            'public_url' => null,
            'direct_embed' => null
        ];

        if (file_exists($absolutePath) && strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) === 'svg') {
            // 1. Base64 approach
            $svgContent = file_get_contents($absolutePath);
            $svgApproaches['base64'] = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

            // 2. Data URI with URL encoding
            $svgApproaches['data_uri'] = 'data:image/svg+xml;charset=utf-8,' . urlencode($svgContent);

            // 3. Public URL - this will be an absolute URL to the image
            $svgApproaches['public_url'] = url($cleanPath);

            // 4. Direct embed (with some cleaning)
            $cleanedSvg = preg_replace('/<\?xml.*?\?>/', '', $svgContent);
            // Make sure SVG has explicit dimensions
            if (!preg_match('/width=/', $cleanedSvg)) {
                $cleanedSvg = preg_replace('/<svg/', '<svg width="60" height="60"', $cleanedSvg);
            }
            $svgApproaches['direct_embed'] = $cleanedSvg;
        } else {
            \Log::warning("SVG file not found: {$absolutePath}");
        }

        $data = [
            'customer_name'    => $order->user->first_name,
            'customer_address' => $address->address ?? 'N/A',
            'customer_email'   => $address->user->email ?? 'N/A',
            'invoice_id'       => $order->order_num,
            'invoice_date'     => $order->created_at->format('d M, Y'),

            'items' => [[
                'image_approaches' => $svgApproaches,
                'image_path' => $absolutePath,
                'description' => $document->title ?? 'Document',
                'price'       => $price,
                'quantity'    => 1,
            ]],

            'subtotal'       => $price,
            'processing_fee' => $order->processing_fee ?? 0,
            'tax'            => $order->tax ?? 0,
        ];

        $data['total'] = $data['subtotal'] + $data['processing_fee'] + $data['tax'];

        return $DocxToPagesService->generatePDF($view, $data, "invoice-order-{$order->id}.pdf");
    }

    public function showAllOrder($id){
        $orders = Order::where('user_id',$id)->get();
        // $payment =Transaction::where('order_id',$orders->id)->first();

        // dd($orders);
        return view('admin.orders.show_order',compact('orders'));
    }


    public function editUser(Request $request){
        $id = $request->id;
        $user = User::find($id);
    
        return view('admin.users.edit_user',compact('user'));
    }

    public function updateUser(Request $request){
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->id,
        ]);

        // $role = '';
        // if($request->is_admin == 0){
        //     $role = 'user';
        // }elseif($request->is_admin == 1){
        //     $role = 'admin';
        // }elseif($request->is_admin == 2){
        //     $role = 'contract_reviewer';
        // }elseif($request->is_admin == 3){
        //     $role = 'support_agent';
        // }
        // 
        if($request->id){
            $user = User::find($request->id);

            if ($user) {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->email = $request->email;
                if($request->password){
                    $user->password = Hash::make($request->password);
                }
                $user->is_admin = $request->is_admin;
                // $user->role = $role;
                $user->save();

                return redirect()->route('all.users')->with('success', 'User information updated successfully.');
            } else {
                return redirect()->back()->with('error', 'User not found.');
            }
        }else{
            $request->validate([
                'password' => 'required',
            ]);

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->is_admin = $request->is_admin;
            // $user->role = $role;
            $user->save();

            return redirect()->route('all.users')->with('success', 'User Created successfully.');
        }
    }

    public function deleteUser($id){
        $user = User::find($id);
        if($user){
            $user->delete();
            return redirect()->back()->with('success', 'User deleted successfully.');
        }else{
            return redirect()->back()->with('error', 'User not found.');
        }
    }

    public function aboutUs(){
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
            'meta_title',
            'meta_description'
        ];

        $results = WhoWeAre::whereIn('key', $keys)->get()->keyBy('key');
        $data = [
            'title' => $results['title']->value ?? null,
            'background_image_id' => $results['background_image']->id ?? null,
            'background_image' => str_replace('public/', '', $results['background_image']->file_path ?? null),
            'banner_title' => $results['banner_title']->value ?? null,
            'banner_description' => $results['banner_description']->value ?? null,
            'banner_image_id' =>  $results['banner_image']->id ?? null,
            'banner_image' =>  str_replace('public/', '', $results['banner_image']->file_path ?? null),
            'image_id' => $results['image']->id ?? null,
            'image' => str_replace('public/', '', $results['image']->file_path ?? null),
            'heading' => $results['heading']->value ?? null,
            'description' => $results['description']->value ?? null,
            'offer_image_id' => $results['offer_image']->id ?? null,
            'offer_image' => str_replace('public/', '', $results['offer_image']->file_path ?? null),
            'offer_heading' =>  $results['offer_heading']->value ?? null,
            'offer_description' =>  $results['offer_description']->value ?? null,
            'meta_title' =>  $results['meta_title']->value ?? null,
            'meta_description' => $results['meta_description']->value ?? null,
        ];

        $visions = OurVision::with('media')->get();
        $offers = WhoWeAre::where('key','offer')->get();

        return view('admin.site_meta.about_us.who_we_are',compact('data','visions','offers'));
    }

    public function whoWeAre(Request $request){
        try{
            if($request->hasFile('background_image')){
                $who = WhoWeAre::where('key','background_image')->first();

                $background_image = $request->file('background_image');
                $directory = "public/who_we_images";
                $filename = generateFileName($background_image);
                $filepath = $background_image->storeAs($directory, $filename);

                $who->value = $filename;
                $who->file_path = $filepath;
                $who->update();
            }

            if($request->hasFile('banner_image')){
                $who = WhoWeAre::where('key','banner_image')->first();

                $banner_image = $request->file('banner_image');
                $directory = "public/who_we_images";
                $filename = generateFileName($banner_image);
                $filepath = $banner_image->storeAs($directory, $filename);

                $who->value = $filename;
                $who->file_path = $filepath;
                $who->update();
            }

            if($request->hasFile('image')){
                $who = WhoWeAre::where('key','image')->first();

                $image = $request->file('image');
                $directory = "public/who_we_images";
                $filename = generateFileName($image);
                $filepath = $image->storeAs($directory, $filename);

                $who->value = $filename;
                $who->file_path = $filepath;
                $who->update();
            }

            if($request->hasFile('offer_image')){
                $who = WhoWeAre::where('key','offer_image')->first();

                $offer_image = $request->file('offer_image');
                $directory = "public/who_we_images";
                $filename = generateFileName($offer_image);
                $filepath = $offer_image->storeAs($directory, $filename);

                $who->value = $filename;
                $who->file_path = $filepath;
                $who->update();
            }

            if($request->has('old_vision_heading')){
                foreach($request->old_vision_heading as $idx=>$vlu){
                    $vision = OurVision::find($idx);
                    $vision->heading = $vlu;
                    $vision->update();
                }
            }

            if($request->has('old_vision_description')){
                foreach($request->old_vision_description as $key=>$val){
                    $vision = OurVision::find($key);
                    $vision->description = $val;
                    $vision->update();
                }
            }

            if($request->hasFile('icon')){
                $icon = $request->file('icon');
                for($i=0; $i<count($icon); $i++){
                    $file = $icon[$i];

                    if($request->has('vision_heading')){
                        $vision_heading = $request->vision_heading[$i];
                    }

                    if($request->has('vision_description')){
                        $vision_description = $request->vision_description[$i];
                    }

                    $directory = "public/who_we_images";
                    $fileupload = $this->fileUploadService->upload($file, $directory);
                    $fileuploadData = $fileupload->getData();

                    if(isset($fileuploadData) && $fileuploadData->status == '200') {
                        $vision = new OurVision;
                        $vision->media_id = $fileuploadData->id;
                        $vision->heading = $vision_heading;
                        $vision->description = $vision_description;
                        $vision->save();
                    }elseif($fileuploadData->status == '400') {
                        return redirect()->back()->with('error', $fileuploadData->error);
                    }
                }
            }

            if($request->has('of_heading')){
                foreach($request->of_heading as $index=>$value){
                    $who = WhoWeAre::find($index);
                    $who->offer_section_heading = $value;
                    $who->update();
                }
            }

            if($request->has('of_description')){
                foreach($request->of_description as $key=>$val){
                    $who = WhoWeAre::find($key);
                    $who->offer_section_description = $val;
                    $who->update();
                }
            }

            if($request->has('of_new_heading')){
                for($i=0; $i<count($request->of_new_heading); $i++){
                    $heading = $request->of_new_heading[$i];

                    if($request->has('of_new_description')){
                        $description = $request->of_new_description[$i];
                    }

                    $who = new WhoWeAre;
                    $who->key = 'offer';
                    $who->offer_section_heading = $heading;
                    $who->offer_section_description = $description;
                    $who->save();
                }
            }

            $fields = [
                'title' => 'title',
                'banner_title' => 'banner_title',
                'banner_description' => 'banner_description',
                'heading' => 'heading',
                'description' => 'description',
                'offer_heading' => 'offer_heading',
                'offer_description' => 'offer_description',
                'meta_title' => 'meta_title',
                'meta_description' => 'meta_description',
            ];

            foreach($fields as $key=>$input){
                if($request->has($input)) {
                    $who = WhoWeAre::where('key', $key)->first();
                    if($who){
                        $who->value = $request->$input;
                        $who->update();
                    }else{
                        $who = new WhoWeAre;
                        $who->key = $key;
                        $who->value = $request->$input;
                        $who->save();
                    }
                }
            }

            if($request->bg_image_id != null){
                $who = WhoWeAre::where('id',$request->bg_image_id)->first();
                $file_path = getFilePath($who->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $who->value = null;
                $who->file_path = null;
                $who->save();
            }

            if($request->baner_image_id != null){
                $who = WhoWeAre::where('id',$request->baner_image_id)->first();
                $file_path = getFilePath($who->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $who->value = null;
                $who->file_path = null;
                $who->save();
            }

            if($request->legal_image_id != null){
                $who = WhoWeAre::where('id',$request->legal_image_id)->first();
                $file_path = getFilePath($who->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $who->value = null;
                $who->file_path = null;
                $who->save();
            }

            if($request->offer_id != null){
                $who = WhoWeAre::where('id',$request->offer_id)->first();
                $file_path = getFilePath($who->file_path);
                if(File::exists($file_path)) {
                    $directory_path = dirname($file_path);
                    unlink($file_path);
                    if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                        rmdir($directory_path);
                    }
                }
                $who->value = null;
                $who->file_path = null;
                $who->save();
            }

            if($request->vision_image_id != null){
                $deleteIds = explode(',', $request->vision_image_id);
                foreach($deleteIds as $id){
                    $our_vision = OurVision::where('id',$id)->with('media')->first();
                    if($our_vision->media){
                        $image_path = getFilePath($our_vision->media->file_path);
                        if (File::exists($image_path)) {
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$our_vision->media_id)->delete();

                        $our_vision->media_id = null;
                        $our_vision->update();
                    }
                }
            }

            if($request->removeOffer != null){
                $deleteIds = explode(',', $request->removeOffer);
                foreach($deleteIds as $id){
                    $who = WhoWeAre::find($id);
                    if($who){
                        $who->delete();
                    }
                }
            }

            if($request->removeVision != null){
                $deleteIds = explode(',', $request->removeVision);
                foreach($deleteIds as $id){
                    $our_vision = OurVision::where('id',$id)->with('media')->first();
                    if($our_vision->media){
                        $image_path = getFilePath($our_vision->media->file_path);
                        if(File::exists($image_path)){
                            $directory_path = dirname($image_path);
                            unlink($image_path);
                            if(is_dir($directory_path) && count(scandir($directory_path)) == 2){
                                rmdir($directory_path);
                            }
                        }
                        Media::where('id',$our_vision->media_id)->delete();
                        $our_vision->delete();
                    }

                }
            }

            return redirect()->back()->with('success','Data successfully updated.');

        }catch(Exception $e){
            saveLog("Error:", "SiteMetaController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updateVisionImage(Request $request){
        if($request->image_id != null){
            $id = $request->image_id;
            if($request->hasFile('image')) {
                $file = $request->file('image');
                $directory = "public/who_we_images";
                $fileupload = $this->fileUploadService->upload($file, $directory);
                $fileuploadData = $fileupload->getData();

                $vision = OurVision::find($id);
                if(isset($fileuploadData) && $fileuploadData->status == '200'){
                    $vision->media_id = $fileuploadData->id;
                    $vision->update();

                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'success',
                    ];

                }elseif($fileuploadData->status == '400') {
                    $response = [
                        'code' => $fileuploadData->status,
                        'status' => 'fail',
                    ];
                }

                return response()->json($response);

            }else{
                return response()->json([
                    'code' => '400',
                    'status' => 'fail',
                    'message' => 'No file uploaded',
                ], 400);
            }
        }
    }
}
