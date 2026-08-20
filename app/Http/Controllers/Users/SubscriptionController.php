<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\FreeTrail;
use App\Models\UserPlan;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // ✅ Subscription Details Page
    public function index()
    {
        return view('user_dashboard.subscription_details.subscription_details');
    }

    // ✅ Cancel Subscription
    public function cancel(Request $request)
    {
        try {
            $user         = auth()->user();
            $subscription = Subscription::where('id', $request->subscription_id)
                                ->where('user_id', $user->id)
                                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription not found.'
                ]);
            }

            // ✅ Stripe cancel
            if ($subscription->stripe_subscription_id) {
                $stripe_secret = optional(web_setting('stripe_secret_key'))->value;
                $stripe = new \Stripe\StripeClient($stripe_secret);

                $stripe->subscriptions->cancel(
                    $subscription->stripe_subscription_id
                );
            }
             // ✅ PayPal cancel
        if ($subscription->paypal_subscription_id) {

            $mode          = optional(web_setting('paypal_mode'))->value ?? 'sandbox';
            $client_id     = optional(web_setting('paypal_client_id'))->value;
            $client_secret = optional(web_setting('paypal_secret_key'))->value;

            $baseUrl = $mode === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            // ✅ Step 1: Access token lo
            $tokenResponse = \Illuminate\Support\Facades\Http::withBasicAuth(
                    $client_id,
                    $client_secret
                )
                ->asForm()
                ->post($baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$tokenResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal authentication failed.'
                ]);
            }

            $accessToken = $tokenResponse->json()['access_token'] ?? null;

            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal access token not received.'
                ]);
            }

            // ✅ Step 2: Subscription cancel karo
            $cancelResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post(
                    $baseUrl . '/v1/billing/subscriptions/' .
                    $subscription->paypal_subscription_id . '/cancel',
                    ['reason' => 'Cancelled by user request']
                );

            // PayPal 204 return karta hai success pe
            if (!$cancelResponse->successful() && $cancelResponse->status() !== 204) {
                saveLog(
                    "PayPal cancel failed:",
                    "SubscriptionController",
                    $cancelResponse->body()
                );
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal cancellation failed. Please try again.'
                ]);
            }

            saveLog(
                "PayPal subscription cancelled:",
                "SubscriptionController",
                $subscription->paypal_subscription_id
            );
        }

            

            sleep(3);

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.'
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // ✅ Cancel Free Trial
    public function cancelFreeTrial(Request $request)
    {

                // dd($request);

        try {

            $user      = auth()->user();
            $freeTrial = FreeTrail::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->first();

           

            if (!$freeTrial) {
            return response()->json([
                'success' => false,
                'message' => 'No active free trial found.',
                'redirect' => route('subscription.details')
            ]);
        }

            $freeTrial->update([
                'status'   => 'expired',
                'end_date' => Carbon::now(),
            ]);

            User::where('id', $user->id)->update([
                'is_subscribed' => 0,
                'trial_ends_at' => null,
            ]);

            UserPlan::where('user_id', $user->id)
                    ->update(['status' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Free trial cancelled successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}