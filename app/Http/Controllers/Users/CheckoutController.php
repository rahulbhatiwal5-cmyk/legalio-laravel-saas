<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\ContractContent;
use App\Models\BillingAdress;
use App\Models\Plans;
use App\Models\PlanIncludedDocument;
use App\Models\UserPlan;
use App\Models\UserCredit;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use App\Models\Discount;
use Exception;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    

public function checkout(Request $request)
{
    $document_id = $request->get('document_id')
                ?? '';

    $document = Document::find($document_id);

    // if (!$document_id || !$document) {
    //     return redirect()->back()->with('error', "Ningún documento seleccionado");
    // }

//     if ($document_id && !$document) {

//     return redirect()->back()->with(
//         'error',
//         "Ningún documento seleccionado"
//     );

// }

    $title          = $document->title ?? 'Subscription Checkout';
    $document_image = $document->document_file_path ?? '';

   
    $default_price = optional(web_setting('default_document_price'))->value ?? 39.90;
    $price         = $document->doc_price ?? $default_price;

 
    $checkout_type   = $request->get('type',     'sub');
    $url_plan_id     = $request->get('plan_id',   null);
    $selected_months = (int) $request->get('months', 24);

    
    $plans = Plans::all();

    if ($url_plan_id) {
        $plan = Plans::find($url_plan_id);
    }

    if (empty($plan)) {
        $plan = Plans::where('number_of_months', 24)->first();
    }

    if (!$plan) {
        abort(404, 'Plan not found');
    }

    $plan_id      = $plan->id;
    $month_price  = $plan->price         ?? 0;
    $no_of_months = $plan->number_of_months ?? 0;

   
    if ($no_of_months == 12) {
        $discount_price = optional(web_setting('12_month_price'))->value ?? $month_price;
    } elseif ($no_of_months == 24) {
        $discount_price = optional(web_setting('24_month_price'))->value ?? $month_price;
    } else {
        $discount_price = $month_price;
    }

    $save_price    = $month_price - $discount_price;
    $total_savings = $save_price * $no_of_months;

    $user       = auth()->user();
    $full_name  = $user->full_name ?? "Not Found";
    // $payment_des = "Payment for the Document ID : $document->id of Rupees $price";

    $payment_des = $document
    ? "Payment for the Document ID : {$document->id} of Rupees $price"
    : "Subscription Payment of Rupees $price";

    $intent = PaymentIntent::create([
        'currency'             => 'usd',
        'amount'               => $price * 100,
        'payment_method_types' => ['card'],
        'description'          => $payment_des,
        'metadata'             => [
            'customer_name'  => $full_name,
            'customer_email' => $user->email,
            'customer_id'    => $user->id,
        ],
        'shipping' => [
            'name'    => $full_name,
            'address' => [
                'line1'       => 'Address not provided',
                'city'        => 'City not provided',
                'state'       => 'State not provided',
                'postal_code' => 'Postal code not provided',
                'country'     => 'MX',
            ],
        ],
    ]);

    $clientSecret = $intent->client_secret;
    $public_key   = optional(web_setting('stripe_public_key'))->value ?? '';

  
    $currency_symbol = optional(web_setting('country_currency_symbol'))->value ?? '$';

    return view('users.checkout.checkout', compact(
        'title',
        'price',
        'intent',
        'clientSecret',
        'document',
        'public_key',
        'default_price',
        'document_image',
        'plans',
        'month_price',
        'discount_price',
        'total_savings',
        'no_of_months',
        'plan_id',
        'currency_symbol',
        'checkout_type',        
        'selected_months',      
    ))->with('success', 'Tu documento esta listo.');
}

    public function order_confirm(Request $request)
    {
         saveLog(
                    "Stripe Payment Confirm Error:",
                    "CheckoutController",
                    $request->input('payment_intent')
                );
        if ($request->payment_type === "stripe") {

            $request->validate([
                'payment_method' => 'required|string',
                'payment_intent'  => 'required|string',
            ]);

            $paymentIntentId = $request->input('payment_intent');

             saveLog(
                    "Stripe Payment Confirm Error:",
                    "CheckoutController",
                    $paymentIntentId
                );

            try {       

                if ($paymentIntentId === 'trial') {

                    $trial = 1;

                    if($request->document_data == 1){


                                $document = 'NA';

                                return view('users.checkout.thanku', compact('document', 'trial'));

                    }
                                return view('users.checkout.thanku', compact('trial'));

                            }

                

                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                if ($paymentIntent->status === 'succeeded') {

                    if ($request->document_data == 1) {

                        $document = 'NA';

                        return view('users.checkout.thanku', compact('document'));

                        // return redirect()->route('user.home');
                    }

                    return view('users.checkout.thanku');

                    // return view('users.checkout.order_confirmation');

                } else {

                    return redirect()->back()->withErrors([
                        'stripe' => 'El pago aún no se ha completado. Es posible que sea necesario tomar medidas adicionales.',
                    ]);
                }

            } catch (\Exception $e) {

                saveLog(
                    "Stripe Payment Confirm Error:",
                    "CheckoutController",
                    $e->getMessage()
                );

                return redirect()->back()->withErrors([
                    'stripe' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

        return redirect()->back()->with('error', "Something went wrong...");
    }

    public function placeOrder(Request $request){

        try{
            $content_id = Session::get('content_id') ?? '';

            $order = new Order();
            $order->document_id = $request->document_id ?? null;
            $order->quantity = 0;

            if(Auth::check()){
                $user = User::find(Auth::user()->id);

                if($request->is_advertising){
                    $user->is_advertising = $request->is_advertising;
                }
                $user->update();

                $address = $user->addresses()->first() ?? new BillingAdress;
                $address->user_id = $user->id;
                $address->company = $request->company;
                $address->company_2 = $request->company_2;

                $address->address = $request->address;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->postal_code = $request->postal_code;
                $address->country = $request->country;
                $address->save();


                if($content_id){
                    $contract_content = ContractContent::find($content_id);
                    if($contract_content){
                        $contract_content->user_id = $user->id;
                        $contract_content->save();
                    }
                }
                $order->user_id = $user->id;

            }else{
                $user = User::where('email', $request->email)->first();

                if(!$user){
                    $user = new User;
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->email = $request->email;
                    $user->password = Hash::make($request->password);
                    $user->is_admin = 0;
                    if($request->is_advertising){
                        $user->is_advertising = $request->is_advertising;
                    }
                    

                    if($user->save()){
                        Auth::login($user);

                        $address = new BillingAdress;
                        $address->user_id = $user->id;
                        $address->company = $request->company;
                        $address->company_2 = $request->company_2;
                        $address->address = $request->address;
                        $address->city = $request->city;
                        $address->state = $request->state;
                        $address->postal_code = $request->postal_code;
                        $address->country = $request->country;
                        $address->save();
                    }

                    if($content_id){
                        $contract_content = ContractContent::find($content_id);
                        if($contract_content){
                            $contract_content->user_id = $user->id;
                            $contract_content->save();
                        }
                    }

                }else{
                    if(!Hash::check($request->password, $user->password)) {
                        return response()->json([
                            'success' => false,
                            'data' => 'La contraseña no coincide',
                        ]);
                    }else{
                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        if($request->is_advertising){
                            $user->is_advertising = $request->is_advertising;
                        }
                       
                        if($user->update()){
                            Auth::login($user);
                            $address = $user->addresses()->first() ?? new BillingAdress;
                            $address->user_id = $user->id;
                            $address->company = $request->company;
                            $address->company_2 = $request->company_2;
                            $address->address = $request->address;
                            $address->city = $request->city;
                            $address->state = $request->state;
                            $address->postal_code = $request->postal_code;
                            $address->country = $request->country;
                            $address->save();

                        }

                        if($content_id){
                            $contract_content = ContractContent::find($content_id);
                            if($contract_content){
                                $contract_content->user_id = $user->id;
                                $contract_content->save();
                            }
                        }

                    }
                }
                $order->user_id = $user->id;
            }

            
            $type = 'one_time';
            
            $order->order_type = $type;
            $order->amount = $request->price / 100;
            $order->discount_amount = 0;
            $order->total_amount = $order->amount - $order->discount_amount;
            $order->status = 0;
            $order->save();
            $order->order_num = "MX" . $order->id ;
            $order->save();

            if($content_id){
                $contract_content = ContractContent::find($content_id);
                if($contract_content){
                    $contract_content->order_id = $order->id;
                    $contract_content->status = 1;
                    $contract_content->type = 'original';
                    $contract_content->update();
                }
            }

            $trans = new Transaction();
            $trans->order_id = $order->id;
            $trans->payment_intent = $request->payment_intent ?? "";
            $trans->type = 'stripe';
            $trans->pay_type = $type;
            $trans->save();

                // ✅ Free Trial cancel karo

                $freeTrial = \App\Models\FreeTrail::where('user_id', Auth::id())
                                ->where('status', 'active')
                                ->first();

                if ($freeTrial) {
                    $freeTrial->update([
                        'status'   => 'converted',
                        'end_date' => now(),
                    ]);
                }

            return response()->json([
                'success' => true,
                'data' => $request->all(),
            ]);
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function createStripeSubscription(Request $request)
    {   

        try {  
            $content_id = Session::get('content_id') ?? '';
            saveLog("Content id", "CheckoutController", $content_id);
            $user = Auth::user();
            $plans = Plans::findOrFail($request->plan_id);
            $price_id = $plans->stripe_price_id;
            $price = $request->price;

            
            $document_limit = optional(web_setting('fair_use_document_limit'))->value ?? 0;
            $stripe_secret_key = optional(web_setting('stripe_secret_key'))->value ?? '';

            $stripe = new \Stripe\StripeClient($stripe_secret_key);

            if(Auth::check()){
                $user = Auth::user();
                $user->is_advertising = $request->is_advertising ?? $user->is_advertising;
                $user->update();
            }else{
                $user = User::where('email', $request->email)->first();

                if(!$user){
                    $user = new User();
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->email = $request->email;
                    $user->password = Hash::make($request->password);
                    $user->is_admin = 0;
                    $user->is_advertising = $request->is_advertising ?? 0;

                    $user->save();
                    Auth::login($user);
                }else{
                    if(!Hash::check($request->password, $user->password)){
                        return response()->json([
                            'success' => false,
                            'data' => 'La contraseña no coincide',
                        ]);
                    }

                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->is_advertising = $request->is_advertising ?? $user->is_advertising;
                    $user->update();

                    Auth::login($user);
                }
            }

            $address = $user->addresses()->first() ?? new BillingAdress;
            $address->user_id = Auth::user()->id;
            $address->company = $request->company;
            $address->company_2 = $request->company_2;
            $address->address = $request->address;
            $address->city = $request->city;
            $address->state = $request->state;
            $address->postal_code = $request->postal_code;
            $address->country = $request->country;
            $address->save();

            if(!$user->stripe_cus_id){
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ]);
                $user->stripe_cus_id = $customer->id;
                $user->save();
            }
            
            $stripe->paymentMethods->attach(
                $request->payment_method,
                ['customer' => $user->stripe_cus_id]
            );
            
            $stripe->customers->update($user->stripe_cus_id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method,
                ],
            ]);
            
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->document_id = $request->document_id;
            $order->order_type = 'subscription';
            $order->amount = $request->is_trial == 1
            ? 0
            : ($request->price / 100);
            $order->discount_amount = 0;
            $order->total_amount = $request->is_trial == 1
                ? 0
                : ($request->price / 100);
            $order->status = 0;
            $order->save();
        
            if(!empty($content_id)){
                $contract_content = ContractContent::find($content_id);
                if($contract_content){
                    $contract_content->user_id = Auth::user()->id;
                    $contract_content->order_id = $order->id;
                    $contract_content->type = 'original';
                    $contract_content->update();
                }
            }

            $plan = Plans::where('price', 49.90)
            ->where('number_of_months', 1)
            ->first();

                $stripe_price_id = $plan?->stripe_price_id;

            if ($request->is_trial == 1) {

                // 7 Days Trial Subscription

                $subscription = $stripe->subscriptions->create([
                    'customer' => $user->stripe_cus_id,
                    'items' => [[
                        'price' => $stripe_price_id
                    ]],
                    'default_payment_method' => $request->payment_method,
                    'trial_period_days' => 7,

                    'metadata' => [
                        'document_id' => $request->document_id,
                        'plan_id'     => 0,
                        'order_id'    => $order->id ?? null,
                        'price'       => 0.00,
                        'credit'      => $document_limit,
                        'is_trial'    => 1,
                    ],
                ]);

            } else {

                // Normal Paid Subscription

                $subscription = $stripe->subscriptions->create([
                    'customer' => $user->stripe_cus_id,
                    'items' => [[
                        'price' => $price_id
                    ]],
                    'default_payment_method' => $request->payment_method,
                    'payment_behavior' => 'default_incomplete',
                    'expand' => ['latest_invoice.payment_intent'],

                    'metadata' => [
                        'document_id' => $request->document_id,
                        'plan_id'     => $request->plan_id,
                        'order_id'    => $order->id ?? null,
                        'price'       => $request->price / 100,
                        'credit'      => $document_limit,
                    ],
                ]);
            }



            $order->stripe_subscription_id = $subscription->id;
            $order->order_num = "MX" . $order->id;
            $order->save();


            $trans = new Transaction();
            $trans->order_id = $order->id;
            $trans->payment_intent = $request->is_trial == 1 ? null
                : ($subscription->latest_invoice->payment_intent->id ?? '');
            $trans->type = 'stripe';
            $trans->pay_type = 'subscription';
            $trans->save();

            $db_Subscription = Subscription::where('stripe_subscription_id',  $subscription->id)->first();
            if(!$db_Subscription){
                $db_Subscription = new Subscription();
            }
            
            $db_Subscription->order_id = $order->id;
            $db_Subscription->user_id = $user->id;
            $db_Subscription->stripe_subscription_id = $subscription->id;
            $db_Subscription->stripe_customer_id = $user->stripe_cus_id;
            $db_Subscription->plan_id = $request->is_trial == 1 ? 0 : $request->plan_id;
            $db_Subscription->status = $subscription->status;
            $db_Subscription->stripe_status = $subscription->status;
            $db_Subscription->start_date = Carbon::createFromTimestamp($subscription->start_date);
            $db_Subscription->current_period_start_date = Carbon::createFromTimestamp($subscription->current_period_start);
            $db_Subscription->current_period_end_date = Carbon::createFromTimestamp($subscription->current_period_end);
            $db_Subscription->save();

            SubscriptionLog::create([
                'subscription_id' => $db_Subscription->id,
                'user_id' => $user->id,
                'plan_id' => $request->is_trial == 1 ? 0 : $request->plan_id,
                'event_type' => 'Subscription created',
                'description' => 'Subscription created',
                'status' => $db_Subscription->status,
            ]);
                // ✅ Free Trial cancel karo agar paid subscription le raha hai
                $freeTrial = \App\Models\FreeTrail::where('user_id', $user->id)
                                ->where('status', 'active')
                                ->first();

                if ($freeTrial) {
                    $freeTrial->update([
                        'status'   => 'converted',
                        'end_date' => now(),    
                    ]);

                    // User trial_ends_at clear karo
                    $user->update(['trial_ends_at' => null]);

                    saveLog("Free trial cancelled for user:", "CheckoutController", $user->id);
                }

                if ($request->is_trial == 1) {

                        return response()->json([
                            'success' => true,
                            'is_trial' => true,
                            'subscription_status' => $subscription->status,
                            'subscription_id' => $subscription->id,
                        ]);

                    } else {

                        return response()->json([
                            'success' => true,
                            'subscription_status' => $subscription->status,
                            'subscription_id' => $subscription->id,
                            'payment_intent_client_secret' => $subscription->latest_invoice->payment_intent->client_secret ?? null,
                               
                        ]);
                    }

        
            
        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getPrice(Request $request){
        try{
            $number_of_month = (int) $request->number_of_months;    
            $plan_id = $request->plan_id;

            $price = $request->price;

            if($number_of_month == 12){                
                // $discount_price = optional(web_setting('12_month_price'))->value ?? $price;
                $discount_price = 9.90;

            } 
            elseif($number_of_month == 24){
                $discount_price = optional(web_setting('24_month_price'))->value ?? $price;
            } 
            else {
                $discount_price = $price;
            }
           

            $save_price = $price - $discount_price;
            $total_savings = $save_price * $number_of_month;

            return response()->json([
                'success' => true,
                'price' => $price,
                'discount_price' => round($discount_price, 2),
                'total_savings' => round($total_savings, 2),
            ]);

        }catch(Exception $e){
            saveLog("Get discount price:", "CheckoutController", $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paypalSuccess(Request $request, PayPalClient $paypal)
    {

        // dd(3432);
        $paypal_order_id = $request->token;
        
        if (!$paypal_order_id) {

            return redirect()->route('paypal.cancel')->with('error', 'Invalid PayPal response.');

        }
        
        $order = Order::where('paypal_order_id', $paypal_order_id)->first();
        
        if (!$order) {
            return redirect()->route('paypal.cancel')->with('error', 'Order not found.');
        }
        
        if ($order->status == 1) {
            return redirect()->route('user.home')->with('info', 'Order already processed.');
        }

        try {
            $orderData = $paypal->showOrderDetails($paypal_order_id);

            if (!$orderData) {
                $order->markFailed();
                return redirect()->route('paypal.cancel')->with('error', 'Could not retrieve PayPal order details.');
            }

            if (isset($orderData['status']) && $orderData['status'] === 'APPROVED') {
                $captureResponse = $paypal->capturePaymentOrder($paypal_order_id);
                
                if ($captureResponse && isset($captureResponse['status']) && $captureResponse['status'] === 'COMPLETED') {
                    $order->markSuccess('paypal');
                    $this->updateContractContent($order);
                    
                    return view('users.checkout.order_confirmation', ['order' => $order]);
                } else {
                    $order->markFailed();
                    return redirect()->route('paypal.cancel')->with('error', 'Payment capture failed.');
                }
            }

            if (isset($orderData['status']) && $orderData['status'] === 'COMPLETED') {
                $order->markSuccess('paypal');
                $this->updateContractContent($order);
                
                return view('users.checkout.order_confirmation', ['order' => $order]);
            }

            $order->markFailed();
            return redirect()->route('paypal.cancel')->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            Log::error('PayPal Success Error: ' . $e->getMessage());
            $order->markFailed();
            return redirect()->route('paypal.cancel')->with('error', 'PayPal Error: ' . $e->getMessage());
        }
    }

   

    private function updateContractContent($order)
    {
        $contract_content = ContractContent::where('user_id', $order->user_id)
                                            ->where('document_id', $order->document_id)
                                            ->whereNull('order_id')
                                            ->first();

        if (!$contract_content) {
            $contract_content = ContractContent::where('user_id', $order->user_id)
                                                ->where('document_id', $order->document_id)
                                                ->latest()
                                                ->first();
        }

        if ($contract_content) {
            if ($contract_content->order_id != null) {
                $new_contract = $contract_content->replicate();
                $new_contract->order_id = $order->id;
                $new_contract->status = 1;
                $new_contract->save();
            } else {
                $contract_content->order_id = $order->id;
                $contract_content->status = 1;
                $contract_content->save();
            }
            
            Session::forget('content_id');
        } else {
            Log::error("Critical: No content record found for Order #{$order->id}");
        }
    }

    public function paypalFailed(Request $request ){
        return view('users.checkout.payment_failed');

    }

    public function paypalCheckout(Request $request, PayPalClient $paypal)
    {
        // dd($request->all());
        if ($request->payment_method == "paypal") {


            $doc_id = $request->document_id;
            $document = getDocument($doc_id) ?? null;
            
           

            $content_id = Session::get('content_id') ?? '';
                       
            if ($request->purchase_type === 'one-time') {
                
                $default_price = web_setting('default_document_price'); 
                $amount = $default_price->value ?? 39.90;

                try {
                    $paypalOrder = $paypal->createOrder([
                        "intent" => "CAPTURE",
                        "purchase_units" => [
                            [
                                "amount" => [
                                    "currency_code" => "USD",
                                    "value" => $amount,
                                ],
                            ],
                        ],
                        "application_context" => [
                            "return_url" => route('paypal.success'),
                            "cancel_url" => route('paypal.cancel'),
                        ],
                    ]);
                } catch (\Exception $e) {                                        
                    return redirect()->back()->with('error', 'PayPal Connection Error: ' . $e->getMessage());
                }

                if (!isset($paypalOrder['id'])) {
                    $errorMsg = $paypalOrder['message'] ?? 'Unknown error occurred with PayPal.';
                    return redirect()->back()->with('error', 'PayPal Error: ' . $errorMsg);
                }

                $order = new Order();
                $order->document_id = $document->id ?? null;

                if (Auth::check()) {

                    $user = User::find(Auth::user()->id);
                    $address = $user->addresses()->first() ?? new BillingAdress;
                    $address->user_id = $user->id;
                    $address->company = $request->company;
                    $address->company_2 = $request->company_2;
                    $address->address = $request->address;
                    $address->city = $request->city;
                    $address->state = $request->state;
                    $address->postal_code = $request->postal_code;
                    $address->country = $request->country;
                    $address->save();

                    if ($content_id) {                        
                        $contract_content = ContractContent::find($content_id);
                        if ($contract_content) {
                            $contract_content->user_id = $user->id;
                            $contract_content->save();
                        }
                    }
                    $order->user_id = $user->id;

                } else {
                    $user = User::where('email', $request->email)->first();

                    if (!$user) {
                        $user = new User;
                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        $user->email = $request->email;
                        $user->password = Hash::make($request->password);
                        $user->is_admin = 0;

                        if ($user->save()) {
                            Auth::login($user);

                            $address = new BillingAdress;
                            $address->user_id = $user->id;
                            $address->company = $request->company;
                            $address->company_2 = $request->company_2;
                            $address->address = $request->address;
                            $address->city = $request->city;
                            $address->state = $request->state;
                            $address->postal_code = $request->postal_code;
                            $address->country = $request->country;
                            $address->save();
                        }

                        if ($content_id) {
                            $contract_content = ContractContent::find($content_id);
                            if ($contract_content) {
                                $contract_content->user_id = $user->id;
                                $contract_content->save();
                            }
                        }

                    } else {
                        if (!Hash::check($request->password, $user->password)) {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Password does not match our records.');
                        } else {

                            $user->first_name = $request->first_name;
                            $user->last_name = $request->last_name;

                            if ($user->update()) {
                                Auth::login($user);
                                $address = $user->addresses()->first() ?? new BillingAdress;
                                $address->user_id = $user->id;
                                $address->company = $request->company;
                                $address->company_2 = $request->company_2;
                                $address->address = $request->address;
                                $address->city = $request->city;
                                $address->state = $request->state;
                                $address->postal_code = $request->postal_code;
                                $address->country = $request->country;
                                $address->save();
                            }

                            if ($content_id) {
                                $contract_content = ContractContent::find($content_id);
                                if ($contract_content) {
                                    $contract_content->user_id = $user->id;
                                    $contract_content->save();
                                }
                            }
                        }
                    }
                    $order->user_id = $user->id;
                }

                $type = 'one_time';            
                $order->order_type = $type;

                $order->paypal_order_id = $paypalOrder['id'];
                $order->quantity = 1; 
                $order->amount = $amount;
                $order->discount_amount = 0;
                $order->total_amount = $order->amount - $order->discount_amount;
                $order->status = 0;
                $order->save();
                $order->order_num = "MX" . $order->id;              
                $order->save();

                $trans = new Transaction();
                $trans->order_id = $order->id;
                $trans->payment_intent = $paypalOrder['id'];
                $trans->type = 'paypal';
                $trans->status = 'pending'; 
                $trans->pay_type = $type;
                $trans->save();

                $freeTrial = \App\Models\FreeTrail::where('user_id', Auth::id())
                ->where('status', 'active')
                ->first();

                    if ($freeTrial) {
                        $freeTrial->update([
                            'status'   => 'converted',
                            'end_date' => now(),
                        ]);
                    }

                foreach ($paypalOrder['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }

                return redirect()->back()->with('error', 'Unable to create PayPal order link.');
            }

            // SUBSCRIPTION
            if ($request->purchase_type === 'recurring') {
                // Validate plan_id is provided
                // if (!$request->has('plan_id')) {
                //     return redirect()->back()->with('error', 'Please select a subscription plan.');
                // }
                $plans = Plans::findOrFail($request->plan_id);
                $paypal_plan_id = $plans->paypal_plan_id;
                // dd($paypal_plan_id);
                
                
               

                $document_limit = web_setting('fair_use_document_limit')->value;

                // Handle user authentication/creation
                if (Auth::check()) {
                    $user = Auth::user();
                    $user->is_advertising = $request->is_advertising ?? $user->is_advertising;
                    $user->update();
                } else {
                    $user = User::where('email', $request->email)->first();

                    if (!$user) {
                        $user = new User();
                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        $user->email = $request->email;
                        $user->password = Hash::make($request->password);
                        $user->is_admin = 0;
                        $user->is_advertising = $request->is_advertising ?? 0;
                        $user->save();
                        Auth::login($user);
                    } else {
                        if (!Hash::check($request->password, $user->password)) {
                            return redirect()->back()
                                ->withInput()
                                ->with('error', 'Password does not match our records.');
                        }
                        $user->first_name = $request->first_name;
                        $user->last_name = $request->last_name;
                        $user->is_advertising = $request->is_advertising ?? $user->is_advertising;
                        $user->update();
                        Auth::login($user);
                    }
                }

                // Save/update billing address
                $address = $user->addresses()->first() ?? new BillingAdress;
                $address->user_id = $user->id;
                $address->company = $request->company;
                $address->company_2 = $request->company_2;
                $address->address = $request->address;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->postal_code = $request->postal_code;
                $address->country = $request->country;
                $address->save();

                // Create order record
                $order = new Order();
                $order->user_id = $user->id;
                $order->document_id = $document->id ?? null;
                $order->order_type = 'subscription';
                $order->amount = $request->price ?? $plans->price;
                $order->discount_amount = 0;
                $order->total_amount = $order->amount;
                $order->status = 0;
                $order->save();

                $order->order_num = "MX" . $order->id;
                $order->save();

                // Link contract content if exists
                
                if (!empty($content_id)) {
                    $contract_content = ContractContent::find($content_id);
                    if ($contract_content) {
                        $contract_content->user_id = $user->id;
                        $contract_content->order_id = $order->id;
                        $contract_content->type = 'original';
                        $contract_content->update();
                    }
                }


                try {
                    // Create PayPal subscription
                    $subscriptionData = [
                        "plan_id" => $paypal_plan_id,
                        "subscriber" => [
                            "name" => [
                                "given_name" => $user->first_name,
                                "surname" => $user->last_name
                            ],
                            "email_address" => $user->email
                        ],
                        "application_context" => [
                            "brand_name" => config('app.name'),
                            "locale" => "en-US",
                            "shipping_preference" => "NO_SHIPPING",
                            "user_action" => "SUBSCRIBE_NOW",
                            "return_url" => route('paypal.success'),
                            "cancel_url" => route('paypal.cancel')
                        ],
                        "custom_id" => $order->id . '|' . $plans->id . '|' . ($document->id ?? 0) . '|' . $document_limit
                    ];

                    $response = $paypal->createSubscription($subscriptionData);

                } catch (\Exception $e) {

                    return redirect()->back()->with('error', 'PayPal Subscription Error: ' . $e->getMessage());
                }

                // dd($response);

                if (!isset($response['id'])) {
                    $errorMsg = $response['message'] ?? 'Unknown error occurred with PayPal subscription.';
                    return redirect()->back()->with('error', 'PayPal Error: ' . $errorMsg);
                }
                    // dd(43);

            // dd($paypal_plan_id);

                // Update order with PayPal subscription ID
                // $order->paypal_subscription_id = $response['id'];
                // $order->save();

                // Create transaction record
                $trans = new Transaction();
                $trans->order_id = $order->id;
                $trans->payment_intent = $response['id'];
                $trans->type = 'paypal';
                $trans->status = 'pending';
                $trans->pay_type = 'subscription';
                $trans->save();

                // Create subscription record
                $db_Subscription = new Subscription();
                $db_Subscription->order_id = $order->id;
                $db_Subscription->user_id = $user->id;
                $db_Subscription->paypal_subscription_id = $response['id'];
                $db_Subscription->paypal_plan_id = $paypal_plan_id;
                $db_Subscription->plan_id = $plans->id;
                $db_Subscription->status = $response['status'] ?? null;
                $db_Subscription->stripe_status = null; // Not applicable for PayPal
                $db_Subscription->start_date = null;
                $db_Subscription->current_period_start_date = null;
                $db_Subscription->current_period_end_date = null; // Adjust based on plan
                $db_Subscription->save();

                // Log subscription creation
                SubscriptionLog::create([
                    'subscription_id' => $db_Subscription->id,
                    'user_id' => $user->id,
                    'plan_id' => $plans->id,
                    'event_type' => 'Subscription created',
                    'description' => 'PayPal subscription created',
                    'status' => $db_Subscription->status,
                ]);

                // Redirect to PayPal approval URL
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return redirect()->away($link['href']);
                    }
                }

                return redirect()->back()->with('error', 'Unable to create PayPal subscription link.');
            }   

            // If purchase_type is neither 'one-time' nor 'recurring'
            return redirect()->back()->with('error', 'Invalid purchase type selected.');
        }
        
        return redirect()->back()->with('error', 'Invalid payment method.');
    }



    public function saveBillingInfo(Request $request)
    {
        $billingAddress = BillingAdress::where('user_id', Auth::id())->first();

        if ($billingAddress) {

            $billingAddress->address = $request->address ?: 'Not Filled';
            $billingAddress->city = $request->city ?: 'Not Filled';
            $billingAddress->state = $request->state ?: 'Not Filled';
            $billingAddress->postal_code = $request->zip_code ?: 'Not Filled';

            $billingAddress->save();

        } else {

            BillingAdress::create([
                'user_id'     => Auth::id(),
                'address'     => $request->address ?: 'Not Filled',
                'city'        => $request->city ?: 'Not Filled',
                'state'       => $request->state ?: 'Not Filled',
                'postal_code' => $request->zip_code ?: 'Not Filled',
            ]);
        }

            if($request->document == 'NA'){

                            return redirect()->route('user.home');



            }


            return view('users.checkout.order_confirmation');


    }

    

}

