<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FreeTrail;
use App\Models\Order;
use App\Models\Plans;
use App\Models\UserPlan;
use App\Models\User;
use App\Models\BillingAdress;
use Carbon\Carbon;

use App\Models\Document;
use App\Models\Transaction;
use App\Models\ContractContent;
use App\Models\PlanIncludedDocument;
use App\Models\UserCredit;
use App\Models\Subscription;
use App\Models\SubscriptionLog;
use Stripe\PaymentIntent;
use App\Models\Discount;
use Exception;
use Stripe\Stripe;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FreeTrialController extends Controller
{
    //  Free Trial
    public function startFreeTrial(Request $request)
    {

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login first'
                ]);
            }

            //  Already active free trial check
            $existingTrial = FreeTrail::where('user_id', $user->id)
                                ->where('status', 'active')
                                ->where('end_date', '>=', now())
                                ->first();

            $redirectUrl = $request->document_id
             ? route('order.confirmation')
             : route('user.home');

            if ($existingTrial) {
                return response()->json([   
                    'success'      => false,
                    'message'      => 'You already have an active free trial',
                    'redirect_url' => route('order.confirmation')
                ]);
            }

            //  Already paid subscription check
            $paidSub = \App\Models\Subscription::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->first();

            if ($paidSub){
                return response()->json([
                    'success'      => false,
                    'message'      => 'You already have an active subscription',
                    'redirect_url' => route('order.confirmation')
                ]);
            }

            //  Billing address save 
            $address = $user->addresses()->first() ?? new BillingAdress;
            $address->user_id     = $user->id;
            // $address->first_name  = $request->first_name ?? $user->first_name;
            // $address->last_name   = $request->last_name ?? $user->last_name;
            $address->company     = $request->company ?? '';
            $address->address     = $request->address ?? '';
            $address->city        = $request->city ?? '';
            $address->state       = $request->state ?? '';
            $address->postal_code = $request->postal_code ?? '';
            $address->country     = $request->country ?? '';
            $address->save();

            //  Order create  (free)
            $order             = new Order();
            $order->user_id    = $user->id;
            $order->document_id = $request->document_id;
            // $order->order_type = 'free_trial';
            $order->amount     = 0;
            $order->total_amount = 0;
            $order->status     = 1;
            $order->save();
            $order->order_num  = "MX" . $order->id;
            $order->save();

            //  Free Subscription record 
            FreeTrail::create([
                'user_id'    => $user->id,
                'order_id'   => $order->id,
                'status'     => 'active',
                'start_date' => now(),
                'end_date'   => now()->addDays(7),
            ]);

            //  User update karo
            $user->update([
                'is_subscribed' => 1,
                'trial_ends_at' => now()->addDays(7),
            ]);

            //  UserPlan update 
            $plan = Plans::where('number_of_months', 1)->first();
            if ($plan) {
                UserPlan::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'plan_id'    => $plan->id,
                        'status'     => 1,
                        'start_date' => now(),
                        'end_date'   => now()->addDays(7),
                    ]
                );
            }

            return response()->json([
                'success'      => true,
                'message'      => 'Free trial started successfully!',
                'redirect_url' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    //  Free Trial Cancel (jab paid subscription lo)
    
    public function cancelFreeTrial($userId)
    {
        $freeTrial = FreeTrail::where('user_id', $userId)
                        ->where('status', 'active')
                        ->first();

        if ($freeTrial) {
            $freeTrial->update([
                'status'   => 'converted',
                'end_date' => now(),
            ]);

            User::where('id', $userId)->update([
                'trial_ends_at' => null,
            ]);

            return true;
        }

        return false;
    }

    //  Check Free Trial Status
    public function checkFreeTrial()
    {


        $user = auth()->user();

        $freeTrial = FreeTrail::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->where('end_date', '>=', now())
                        ->first();

        return response()->json([
            'has_free_trial' => $freeTrial ? true : false,
            'ends_at'        => $freeTrial?->end_date,
        ]);
    }

    public function orderConfirmation()
    {
                    return view('users.checkout.order_confirmation');
    }

    //  View Document (Free Trial)
        public function viewDocument($slug)
        {
            $user = auth()->user();

            // Free trial check
            $freeTrial = \App\Models\Subscription::where('user_id', $user->id)
                            ->where('status', 'trialing')
                            ->first();

        
            // Free trial nahi hai → redirect
            if (!$freeTrial) {
                return redirect()->route('user.home')
                                ->with('error', 'No active free trial found.');
            }

            $document = \App\Models\Document::where('slug', $slug)->firstOrFail();

            // Contract content lo
            $contractContent = \App\Models\ContractContent::where('user_id', $user->id)
                                    ->where('document_id', $document->id)
                                    ->latest()
                                    ->first();


            return view('users.checkout.view_document', compact(
                'document',
                'contractContent',
                'freeTrial'
            ));
        }

}