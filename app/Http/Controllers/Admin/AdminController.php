<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiAssistantChat;
use App\Models\Discount;
use App\Models\Document;
use App\Models\EmailRecoveryPassword;
use App\Models\ErrorMessage;
use App\Models\Order;
use App\Models\Tag;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\MediaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(){
        $totalSales = Order::where('status',1)->sum('total_amount');
        $order = Order::all();
        $document =Document::all();
        $user =User::all();
 
        return view('admin.index.index',compact('totalSales','order','document','user'));
    }

    public function filter(Request $request){
        $start_date = Carbon::parse($request->start_date)->startOfDay();
        $end_date = Carbon::parse($request->end_date)->endOfDay();

        $filterTotalSales = Order::where('status', 1)
        ->whereBetween('created_at', [$start_date, $end_date])
        ->sum('total_amount');


        $filterOrders = Order::where('status', 1)
        ->whereBetween('created_at', [$start_date, $end_date])
        ->count();

        $filterDocument = Document::whereBetween('created_at', [$start_date, $end_date])
        ->count();

        $filterUsers = User::whereBetween('created_at', [$start_date, $end_date])
        ->count();

        // ========== This Month ==========
        $thisMonthSales = Order::where('status', 1)
        ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->sum('total_amount');

        $thisMonthOrders = Order::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->count();

        $thisMonthDocuments = Document::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->count();

        $thisMonthUsers = User::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
        ->count();

            // ========== This Week ==========
        $thisWeekSales = Order::where('status', 1)
        ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->sum('total_amount');

        $thisWeekOrders = Order::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();

        $thisWeekDocuments = Document::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();

        $thisWeekUsers = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->count();






        return response()->json([
            'totalSales' => number_format($filterTotalSales, 2),
            'totalDocument' => $filterDocument,
            'totalOrder' => $filterOrders,
            'totaluser' => $filterUsers,
        

            'thisMonthSales' => $thisMonthSales,
            'thisMonthOrders' => $thisMonthOrders,
            'thisMonthDocuments' => $thisMonthDocuments,
            'thisMonthUsers' => $thisMonthUsers,

            'thisWeekSales' => $thisWeekSales,
            'thisWeekOrders' => $thisWeekOrders,
            'thisWeekDocuments' => $thisWeekDocuments,
            'thisWeekUsers' => $thisWeekUsers,
        ]);
    }

    public function country(){
        return view('admin.country.countries');
    }

    public function messages(){
        $login_keys = [
            'incorrect_username',
            'incorrect_password',
            'google_login_error',
            'fb_login_error',
            'login_success_message',
        ];

        $login_results = ErrorMessage::whereIn('error_key', $login_keys)->where('page_type','login')->get()->keyBy('error_key');
        $login_data = [
            'incorrect_username' => $login_results['incorrect_username']->error_value ?? null,
            'incorrect_password' => $login_results['incorrect_password']->error_value ?? null,
            'google_login_error' => $login_results['google_login_error']->error_value ?? null,
            'fb_login_error' => $login_results['fb_login_error']->error_value ?? null,
            'login_success_message' =>  $login_results['login_success_message']->error_value ?? null,
        ];

        $register_keys = [
            'required_field_msg',
            'invalid_email_error',
            'unique_email',
            'register_success_msg',
        ];

        $register_results = ErrorMessage::whereIn('error_key', $register_keys)->where('page_type','register')->get()->keyBy('error_key');
        $register_data = [
            'required_field_msg' => $register_results['required_field_msg']->error_value ?? null,
            'invalid_email_error' => $register_results['invalid_email_error']->error_value ?? null,
            'unique_email' => $register_results['unique_email']->error_value ?? null,
            'register_success_msg' =>  $register_results['register_success_msg']->error_value ?? null,
        ];

        $contact_keys = [
            'required_field',
            'recaptcha_error',
            'contact_success_msg',
        ];

        $contact_results = ErrorMessage::whereIn('error_key', $contact_keys)->where('page_type','contact')->get()->keyBy('error_key');
        $contact_data = [
            'required_field' => $contact_results['required_field']->error_value ?? null,
            'recaptcha_error' => $contact_results['recaptcha_error']->error_value ?? null,
            'contact_success_msg' =>  $contact_results['contact_success_msg']->error_value ?? null,
        ];

        return view('admin.configurations.messages',compact('login_data','register_data','contact_data'));
    }

    public function saveMesage(Request $request){
        try{
            if($request->has('page_type') != null){
                
                if($request->has('incorrect_username') != null){
                    $error_messages = ErrorMessage::where('error_key','incorrect_username')->first();
                    if($error_messages != null){
                        $error_messages->error_value = $request->incorrect_username;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'incorrect_username';
                        $error_messages->error_value = $request->incorrect_username;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                    
                }

                if($request->has('incorrect_password') != null){
                    $error_messages = ErrorMessage::where('error_key','incorrect_password')->first();
                    if($error_messages != null){
                        $error_messages->error_value = $request->incorrect_password;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'incorrect_password';
                        $error_messages->error_value = $request->incorrect_password;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                   
                }

                if($request->has('google_login_error') != null){
                    $error_messages = ErrorMessage::where('error_key','google_login_error')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->google_login_error;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'google_login_error';
                        $error_messages->error_value = $request->google_login_error;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('fb_login_error') != null){
                    $error_messages = ErrorMessage::where('error_key','fb_login_error')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->fb_login_error;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'fb_login_error';
                        $error_messages->error_value = $request->fb_login_error;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('login_success_message') != null){
                    $error_messages = ErrorMessage::where('error_key','login_success_message')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->login_success_message;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'login_success_message';
                        $error_messages->error_value = $request->login_success_message;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('required_field_msg') != null){
                    $error_messages = ErrorMessage::where('error_key','required_field_msg')->first();
                    if($error_messages != null){
                        $error_messages->error_value = $request->required_field_msg;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'required_field_msg';
                        $error_messages->error_value = $request->required_field_msg;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                    
                }

                if($request->has('invalid_email_error') != null){
                    $error_messages = ErrorMessage::where('error_key','invalid_email_error')->first();
                    if($error_messages != null){
                        $error_messages->error_value = $request->invalid_email_error;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'invalid_email_error';
                        $error_messages->error_value = $request->invalid_email_error;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                   
                }

                if($request->has('unique_email') != null){
                    $error_messages = ErrorMessage::where('error_key','unique_email')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->unique_email;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'unique_email';
                        $error_messages->error_value = $request->unique_email;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('register_success_msg') != null){
                    $error_messages = ErrorMessage::where('error_key','register_success_msg')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->register_success_msg;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'register_success_msg';
                        $error_messages->error_value = $request->register_success_msg;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('required_field') != null){
                    $error_messages = ErrorMessage::where('error_key','required_field')->first();
                    if($error_messages != null){
                        $error_messages->error_value = $request->required_field;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'required_field';
                        $error_messages->error_value = $request->required_field;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                    
                }

                if($request->has('recaptcha_error') != null){
                    $error_messages = ErrorMessage::where('error_key','recaptcha_error')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->recaptcha_error;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'recaptcha_error';
                        $error_messages->error_value = $request->recaptcha_error;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }

                if($request->has('contact_success_msg') != null){
                    $error_messages = ErrorMessage::where('error_key','contact_success_msg')->first();
                    if($error_messages){
                        $error_messages->error_value = $request->contact_success_msg;
                        $error_messages->update();
                    }else{
                        $error_messages = new ErrorMessage;
                        $error_messages->error_key = 'contact_success_msg';
                        $error_messages->error_value = $request->contact_success_msg;
                        $error_messages->page_type = $request->page_type;
                        $error_messages->save();
                    }
                }
                return redirect()->back()->with('success', 'Data successfully saved.')->withInput()->with('page_type', $request->input('page_type'));
            }
        }catch(Exception $e){
            saveLog("Error:", "AdminController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
        
    }

    public function support(){
        $tickets=Ticket::whereHas('messages')->with('messages')->get();
        // dd($tickets);
        return view('admin.support_ticket.support',compact('tickets'));
    }
    public function supportView($id){
        $ticket=Ticket::where('ticket_id',$id)->with('messages')->first();
        
        return view('admin.support_ticket.support_ticket_view' ,compact('ticket'));
    }
    public function adminReply(Request $request, $ticketId, MediaService $mediaService)
    {
        $request->validate([
            'message' => 'nullable|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);
        // dd($request->all());

        $ticket = Ticket::findOrFail($ticketId);
        $mediaId = null;

        if ($request->hasFile('media')) {
            $media = $mediaService->uploadMedia($request->file('media'), 'tickets');
            $mediaId = $media->id;
        }

        $message = new TicketMessage();
        $message->ticket_id = $ticket->id;
        $message->user_id = auth()->id(); // assuming admin is also authenticated
        $message->sent_by = 'admin'; // note this is different than 'user'
        $message->message = $request->input('message');
        $message->media_id = $mediaId;
        $message->seen_status = false;
        $message->save();

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    public function toggleStatus($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();

        $ticket->status = $ticket->status === 'closed' ? 'open' : 'closed';
        $ticket->save();

        return back()->with('success', 'Ticket status updated successfully.');
    }

    public function AiFAQ()
    {
        // Fetch all FAQs to display in the list
        $faqs = AiFaq::with('tags')->get();
        // $faqs = AiFaq::all();

        return view('admin.ai_assistant.ai_faq', compact('faqs'));
    }
    
    public function AddAIFaq($id = null)
    {
        // Fetch FAQ by ID if it exists, for editing purposes
        $faq = $id ? AiFaq::findOrFail($id) : null;
    
        $tags = Tag::all();

        // Get array of tag IDs currently associated with this FAQ
        $selectedTagIds = $id ? $faq->tags->pluck('id')->toArray() : null;

        // Pass the FAQ data to the view (null for new FAQ)
        return view('admin.ai_assistant.add_ai_faq', compact('faq' , 'tags' , 'selectedTagIds'));
    }
    
    public function StoreAIFaq(Request $request)
    {
        
        // Validation rules
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'status' => 'required|boolean',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id'
        ]);
        
        // Check if it's an update or new record
        if ($request->has('faq_id')) {
            $faq = AiFaq::findOrFail($request->faq_id);

        } else {
            $faq = new AiFaq();
        }
    
        // Set the FAQ data
        $faq->question = $request->input('question');
        $faq->answer = $request->input('answer');
        $faq->status = $request->input('status');
    
        // Save the FAQ
        $faq->save();

        // faq tag mapping
        $faq->tags()->sync($request->tags);
    
        // Return success message
        return redirect()->route('admin.dashboard.ai.FAQ')->with('success', 'FAQ ' . ($request->has('faq_id') ? 'updated' : 'added') . ' successfully!');
    }
    
    
    public function destroyAIFaq($id)
    {
        // Find the FAQ by ID and delete it
        $faq = AiFaq::findOrFail($id);
        $faq->delete();
    
        // Redirect back to FAQ list with success message
        return redirect()->route('admin.dashboard.ai.FAQ')->with('success', 'FAQ deleted successfully!');
    }


    // ***************** Ai FaQ Tags *******************
        public function AiTag(){

            $tags = Tag::all();

            return view('admin.ai_assistant.ai_tags' , compact('tags'));
        }

        public function AddAiTags($id = null){
            $tag = $id ? Tag::findOrFail($id) : null;

            return view('admin.ai_assistant.add_ai_tags' , compact('tag'));
        }

        public function StoreAiTag(Request $request){

            // Validation rules
            if($request->has('tag_id')){
                $request->validate([
                    'name' => 'required|string|max:255',
                ]);
            }else{
                $request->validate([
                    'name' => 'required|string|max:255|unique:tags,name',
                ]);
            }

            $slug = Str::slug($request->name);

            $request['slug'] = $slug;

            if ($request->has('tag_id')) {
                // in case of edit
                $tag = Tag::findOrFail($request->tag_id);
                $tag->name = $request->name;
                $tag->slug = $request->slug;

                $tag->save();

            } else {
                // In case of new tag
                Tag::create([
                    'name' => $request->name,
                    'slug' => $request->slug
                ]);
            }

            return redirect()->route('admin.dashboard.ai.FAQ.tags')->with('success', 'Tag ' . ($request->has('tag_id') ? 'updated' : 'added') . ' successfully!');

        }

        public function destroyAiTag($id)
        {
            // Find the FAQ by ID and delete it
            $tag = Tag::findOrFail($id);
            $tag->delete();
        
            // Redirect back to FAQ list with success message
            return redirect()->route('admin.dashboard.ai.FAQ.tags')->with('success', 'Tag deleted successfully!');
        }

        
        public function getPendingFaq(){
            $chats = AiAssistantChat::all();            
            return view('admin.ai_assistant.pending_ai_faq' , compact('chats'));
        }


        public function answerAiFaq($id = null){

            $chat = $id ? AiAssistantChat::findOrFail($id) : null;       

            $tags = Tag::all();

            return view('admin.ai_assistant.answer_ai_faq', compact('chat' , 'tags'));
        }

        public function destroyPendingFaq($id)
        {            
            $userQuestion = AiAssistantChat::findOrFail($id);
            $userQuestion->delete();
            return redirect()->route('admin.dashboard.ai.pending.FAQ')->with('success', 'Question deleted successfully!');
        }

        public function StoreAIFaqAnswer(Request $request)
        {
            // Validation rules
            $request->validate([
                'question' => 'required|string|max:255',
                'answer' => 'required|string',
                'status' => 'required|boolean',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id'
            ]);            
            
            $faq = new AiFaq();
        
            $faq->question = $request->input('question');
            $faq->answer = $request->input('answer');
            $faq->status = $request->input('status');
            $faq->save();
            $faq->tags()->sync($request->tags);

            $chat = AiAssistantChat::findOrFail($request->chat_id);
            $chat->delete();   

            return redirect()->route('admin.dashboard.ai.FAQ')->with('success', 'FAQ added successfully!');
        }


    public function recoveryPassword($type)
    {
        $template=EmailRecoveryPassword::where('email_type' , $type)->first();
        // Check if template is found or if we are creating a new one
        return view('admin.emails.recover_password_mail', compact('template'));
    }
    
    

    public function storeRecoveryPassword(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'heading' => 'required|string',
            'body' => 'required|string',
            'button_text' => 'nullable|string',
            'footer' => 'nullable|string',
        ]);

        // If updating, find by ID; otherwise create a new instance
        $template = EmailRecoveryPassword::updateOrCreate(
            ['id' => $request->template_id], // Check if we are updating an existing template
            $validated
        );

        return redirect()
            ->route('admin.dashboard.recovery.password.email', $template->email_type)
            ->with('success', 'Email template saved successfully.');
    }

    public function adminChnagePassword(){
        // dd('chnage password form goes here');
        $email = Auth::user()->email;
        return view('admin.change_password.form' , compact('email'));
    }

    public function storeAdminChnagePassword(Request $request){

        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed|different:current_password',
        ]);

        $user = Auth::user();

        // Current Password does not match
        if(!Hash::check($request->current_password , $user->password)){
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->email = $request->email;
        $user->password = Hash::make($request->new_password);

        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully!');

    }


    public function plans(){
        $plans = Plans::all();

        return view('admin.orders.all_plans',compact('plans'));
    }

    public function subscriptionPlans(Request $request){
        $id = null;
        $plan = null;

        if($request->id){
            $id = $request->id;
            $plan = Plans::find($id);
        }
       
        return view('admin.orders.plans',compact('id', 'plan'));
    }

    public function addSubscriptionPlan(Request $request){
        // return $request->all();
        try {
            $stripe_secret_key = web_setting('stripe_secret_key')->value;
            $currency = web_setting('country_currency')->value;

            $now = Carbon::now();
            $start_date = $now->format("Y-m-d");
            $end_date = $now->copy()->addMonths($request->number_of_months)->format("Y-m-d");

            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            if($request->filled('plan_id')){
                $plan = Plans::find($request->plan_id);

                if(!$plan || !$plan->stripe_product_id || !$plan->stripe_price_id){
                    return redirect()->back()->with('error', 'Invalid plan or missing Stripe product/price ID.');
                }

                $newPrice = $stripe->prices->create([
                    'unit_amount' => $request->plan_amount * 100,
                    'currency' => $currency,
                    'recurring' => ['interval' => 'month'],
                    'product' => $plan->stripe_product_id,
                    'metadata' => [
                        'plan_id' => $request->plan_id,
                    ]
                ]);

                $stripe->prices->update($plan->stripe_price_id, [
                    'active' => false
                ]);

                $plan->price = $request->plan_amount;
                $plan->currency = $currency;
                $plan->number_of_months = $request->number_of_months;
                $plan->stripe_price_id = $newPrice->id;
                $plan->status = 'active';
                $plan->allowed_users = 1;
                $plan->trial_days = 0;
                $plan->start_date = $start_date;
                $plan->end_date = $end_date;
                $plan->created_by = auth()->user()->id;
                $plan->save();

            }else{
                $plan = new Plans();
                $plan->price = $request->plan_amount;
                $plan->currency = $currency;
                $plan->number_of_months = $request->number_of_months;
                $plan->stripe_price_id = null;
                $plan->stripe_product_id = null;
                $plan->status = 'active';
                $plan->allowed_users = 1;
                $plan->trial_days = 0;
                $plan->start_date = $start_date;
                $plan->end_date = $end_date;
                $plan->created_by = auth()->user()->id;
                $plan->save();

                $product = $stripe->products->create([
                    'name' => 'Document Subscription Plan'
                ]);

                $price = $stripe->prices->create([
                    'currency' => $currency,
                    'unit_amount' => $request->plan_amount * 100,
                    'recurring' => ['interval' => 'month'],
                    'product' => $product->id,
                    'metadata' => [
                        'plan_id' => $plan->id,
                    ]
                ]); 
                $plan->stripe_product_id = $product->id;
                $plan->stripe_price_id = $price->id;
                $plan->save();
            }
            return redirect()->route('admin.subscription.plans')->with('success', 'Plan Created successfully.');
        } catch (\Exception $e) {
            saveLog("Error:", "AdminController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function deleteSubscriptionPlan(Request $request){
        try {
            $stripe_secret_key = web_setting('stripe_secret_key')->value;
            $stripe = new \Stripe\StripeClient($stripe_secret_key);
    
            if ($request->filled('id')) {
                $plan = Plans::find($request->id);
    
                if (!$plan || !$plan->stripe_product_id || !$plan->stripe_price_id) {
                    return redirect()->back()->with('error', 'Invalid plan or missing Stripe product/price ID.');
                }
    
                $subscriptions = $stripe->subscriptions->all([
                    'limit' => 10,
                    'status' => 'active',
                ]);
    
                foreach ($subscriptions->data as $subscription) {
                    foreach ($subscription->items->data as $item) {
                        if ($item->price->id === $plan->stripe_price_id) {
                            return redirect()->back()->with('error', 'Cannot delete this plan because it is used in an active subscription.');
                        }
                    }
                }
    
                try {
                    $stripe->products->update($plan->stripe_product_id, ['active' => false]);
                } catch (\Exception $e) {
                    saveLog("Stripe Product Archive Error", "AdminController", $e->getMessage());
                }
    
                $plan->delete();
    
                return redirect()->back()->with('success', 'Subscription plan deleted successfully.');
            }
    
            return redirect()->back()->with('error', 'Plan ID is required.');
        } catch (\Exception $e) {
            saveLog("General Error", "AdminController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
    
    public function allDiscount(){
        $discount = Discount::all();
        return view('admin.orders.all_discount',compact('discount'));
    }

    public function addDiscount(Request $request){

        $id = null;
        $discount = null;

        if($request->id){
            $id = $request->id;
            $discount = Discount::find($id);
        }
        
        return view('admin.orders.discount',compact('discount','id'));
    }

    public function addDiscountProcc(Request $request){
        // return $request->all();
        try{
            $discountName = $request->discount_name ?? '';
            $percentage = $request->discount_percent ?? '';
            $start_date = $request->start_date ?? '';
            $end_date = $request->end_date ?? '';

            if($request->id){
                $discount = Discount::find($request->id);
                $discount->name = $discountName;
                $discount->percentage = $percentage;
                $discount->start_date = $start_date;
                $discount->end_date = $end_date;
                $discount->update();
            }else{
                $discount = new Discount;
                $discount->name = $discountName;
                $discount->percentage = $percentage;
                $discount->start_date = $start_date;
                $discount->end_date = $end_date;
                $discount->save();
            }
            
            return redirect()->route('admin.discount')->with('success','Discount Created Successfully');
            
        }catch(Exception $e){
            saveLog("Error:", "AdminController", $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

    }

}
