<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\Plans;
use App\Models\PlanIncludedDocument;
use App\Models\UserPlan;
use App\Models\UserCredit;
use App\Models\CreditTransaction;
use App\Models\SubscriptionLog;
use App\Models\FreeSubscription;
use App\Models\PaypalWebhookLog;
use App\Models\User;
use Stripe;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;
 

class WebhookController extends Controller
{

    public function handleStripeWebhook(Request $request)
    {
        try {
            $endpointSecret = web_setting('STRIPE_WEBHOOK_SECRET', true);
            $payload = @file_get_contents('php://input');
            $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

            if (!$sigHeader) {
                saveLog("Missing Stripe signature header", "WebhookController");
                return response()->json(['error' => 'Missing signature'], 400);
            }

            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            $type = $event->type;
            $object = $event->data->object;

            saveLog("Webhook received:", "WebhookController", $type);
            saveLog("Webhook object:", "WebhookController", $object);

            switch ($type) {
                case 'customer.subscription.created':
                    $subscriptionObj = $object;
                    $customerId = $subscriptionObj->customer ?? null;
                    $priceId = $subscriptionObj->items->data[0]->price->id ?? null;
                    $status = $subscriptionObj->status ?? 'incomplete';
                    $trial_end = Carbon::createFromTimestamp($subscriptionObj->trial_end);
                    $metadata = $subscriptionObj->metadata ?? [];

                    if(!$customerId || !$priceId){
                        saveLog("Missing customer or price ID in subscription.created", "WebhookController");
                        break;
                    }

                    $existing = Subscription::where('stripe_subscription_id', $subscriptionObj->id)->first();
                    if($existing){
                        saveLog("Subscription already exists", "WebhookController", $subscriptionObj->id);
                        break;
                    }

                    $user = User::where('stripe_cus_id', $customerId)->first();
                    $user->update([
                        'is_subscribed' => 1,
                        'trial_ends_at' => $trial_end
                    ]);

                    $plan = Plans::where('stripe_price_id', $priceId)->first();

                    if(!$user || !$plan){
                        saveLog("User or plan not found in subscription.created", "WebhookController");
                        break;
                    }

                    $orderId = $subscriptionObj->metadata->order_id ?? null;
                    saveLog("order_id", "WebhookController", $orderId);

                    $grantReason = $subscriptionObj->metadata->free_grant_reason ?? null;

                    if(!$orderId){
                        saveLog("Missing order_id in metadata", "WebhookController", $subscriptionObj->id);
                    }

                    $newSubscription = Subscription::create([
                        'order_id' => $orderId,
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'stripe_subscription_id' => $subscriptionObj->id,
                        'stripe_customer_id' => $customerId,
                        'status' => 'active',
                        'stripe_status' => $status,
                        'start_date' => Carbon::createFromTimestamp($subscriptionObj->start_date),
                        'end_date' => Carbon::createFromTimestamp($subscriptionObj->ended_at ?? $subscriptionObj->current_period_end),
                        'current_period_start_date' => Carbon::createFromTimestamp($subscriptionObj->current_period_start),
                        'current_period_end_date' => Carbon::createFromTimestamp($subscriptionObj->current_period_end),
                    ]);

                  
                    $freeSub = FreeSubscription::where('user_id', $user->id)
                            ->where('order_id', $orderId)
                            ->where('status', 'active')
                            ->first();
                
                    saveLog("Free subscription found:", "WebhookController", $freeSub);

                    if($freeSub){
                        $freeSub->update(
                            ['subscription_id' => $newSubscription->stripe_subscription_id],
                            ['start_date' => Carbon::createFromTimestamp($subscriptionObj->start_date)],
                            ['end_date' => Carbon::createFromTimestamp($subscriptionObj->ended_at ?? $subscriptionObj->current_period_end)],
                        );
                    }
                    
                
                    UserPlan::updateOrCreate(
                        ['user_id' => $user->id],
                        ['plan_id' => $plan->id],
                    );

                    SubscriptionLog::create([
                        'subscription_id' => $newSubscription->id,
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'event_type' => 'customer.subscription.created',
                        'description' => $grantReason ? "Free subscription created: $grantReason" : "Subscription created via webhook",
                        'payload' => json_encode($event),
                        'status' => $newSubscription->status,
                    ]);
                    break;
    
                case 'invoice.paid':
                    try {
                        $object = $event->data->object; // ensure you already set $event earlier
                        saveLog("invoice.paid received raw object", "WebhookController", json_encode($object));
                
                        // Try multiple places for subscription id
                        $subscriptionId = $object->parent->subscription_details->subscription
                                        ?? $object->subscription
                                        ?? ($object->lines->data[0]->parent->subscription_item_details->subscription ?? null);

                                        
                        saveLog("Processing invoice.paid for subscription", "WebhookController", $subscriptionId);
                
                        if (!$subscriptionId) {
                            saveLog("Missing subscription ID in invoice.paid", "WebhookController", json_encode($object));
                            break;
                        }
                
                        // Try to find Subscription model, fallback to Order -> subscription
                        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();
                
                        if (!$subscription) {
                            saveLog("Subscription not found by stripe_subscription_id, trying Order lookup", "WebhookController", $subscriptionId);
                            $order = Order::where('stripe_subscription_id', $subscriptionId)->first();
                            if ($order) {
                                // If you have relation set up (order->subscription), try to use it; else create/find subscription by order
                                $subscription = $order->subscription ?? null;
                                // Or create a local subscription record if you want:
                                // if (!$subscription) { /* create local Subscription from order and continue */ }
                            }
                        } else {
                            $order = $subscription->order ?? null;
                        }
                
                        if (!$subscription && !$order) {
                            saveLog("No subscription or order found for subscriptionId", "WebhookController", $subscriptionId);
                            break;
                        }
                
                        // Ensure we have $user and $plan via either subscription or order
                        $user = $subscription->user ?? $order->user ?? null;
                        saveLog("User found for invoice.paid:", "WebhookController", $user ? $user->stripe_cus_id : 'null');
                        $plan = $subscription->plan ?? $order->plan ?? null;
                
                        if (!$user || !$plan) {
                            saveLog("User or Plan not found (subscriptionId)", "WebhookController", $subscriptionId);
                            break;
                        }
                
                        $documentId = $order->document_id ?? null;
                        $orderId = $order->id ?? null;
                        saveLog("Order ID for invoice.paid:", "WebhookController", $orderId);
                
                        // Period start/end: try line item first, fallback to top-level invoice fields
                        $periodStartTs = $object->lines->data[0]->period->start ?? $object->period_start ?? null;
                        $periodEndTs   = $object->lines->data[0]->period->end   ?? $object->period_end   ?? null;
                
                        $periodStart = $periodStartTs ? Carbon::createFromTimestamp(intval($periodStartTs)) : now();
                        $periodEnd   = $periodEndTs ? Carbon::createFromTimestamp(intval($periodEndTs)) : now()->addMonth();
                
                        $documentLimit = optional(web_setting('fair_use_document_limit'))->value ?? 50;
                
                        $userCredit = UserCredit::where('user_id', $user->id)
                            ->where('document_id', $documentId)
                            ->first();
                
                        $previousBalance = $userCredit->balance ?? 0;
                        $carryForward = $previousBalance;
                        $newCredits = $documentLimit;
                        $totalBalance = $carryForward + $newCredits;
                
                        $userCredit = UserCredit::updateOrCreate(
                            ['user_id' => $user->id, 'document_id' => $documentId],
                            ['balance' => $totalBalance]
                        );
                
                        if($carryForward > 0){
                            CreditTransaction::create([
                                'user_id'         => $user->id,
                                'document_id'     => $documentId,
                                'plan_id'         => $plan->id,
                                'subscription_id' => $subscription->id ?? null,
                                'order_id'        => $orderId,
                                'carry_forward'   => $carryForward,
                                'used_amount'     => 0,
                                'amount'          => $carryForward,
                                'type'            => 0,
                                'transaction_date'=> now(),
                                'period_start'    => $periodStart,
                                'period_end'      => $periodEnd,
                                'description'     => 'Carried forward unused credits'
                            ]);
                        }else{
                            CreditTransaction::create([
                                'user_id'         => $user->id,
                                'document_id'     => $documentId,
                                'plan_id'         => $plan->id,
                                'subscription_id' => $subscription->id ?? null,
                                'order_id'        => $orderId,
                                'carry_forward'   => $carryForward,
                                'used_amount'     => 0,
                                'amount'          => $newCredits,
                                'type'            => 1,
                                'transaction_date'=> now(),
                                'period_start'    => $periodStart,
                                'period_end'      => $periodEnd,
                                'description'     => 'New billing cycle credits added'
                            ]);
                        }
                
                        // Update subscription / order / transactions safely
                        if ($subscription) {
                            $subscription->update([
                                'status' => 'active',
                                'stripe_status' => $object->status ?? 'active',
                                'current_period_start_date' => $periodStart,
                                'current_period_end_date' => $periodEnd,
                            ]);
                        }
                
                        $order?->update(['status' => 1]);
                
                        $transaction = Transaction::where('order_id', $order?->id)
                        ->update([
                            'stripe_customer_id' => $user?->stripe_cus_id,
                            'amount' => $plan->price ?? 0,
                            'status' => 'succeeded',
                        ]);

                        saveLog("Transactions updated for order:", "WebhookController", $transaction);

                        UserPlan::updateOrCreate(
                            ['user_id' => $user->id], 
                            [
                                'plan_id' => $plan->id,
                                'status' => 1,
                                'start_date' => $periodStart,
                                'end_date' => $periodEnd,
                            ]
                        );
                
                        $user->update(['is_subscribed' => 1]);
                
                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id ?? null,
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'event_type' => 'invoice.paid',
                            'description' => "Credits refreshed (carry: {$carryForward}, new: {$newCredits}, total: {$totalBalance})",
                            'payload' => json_encode($event),
                            'status' => 'active',
                        ]);
                
                        saveLog("invoice.paid processed successfully", "WebhookController", $subscriptionId);
                
                    } catch (\Exception $e) {
                        saveLog("invoice.paid error:", "WebhookController", $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                break;

                case 'customer.subscription.updated':
                    try {
                        $subscriptionObj = $event->data->object;
                        saveLog("customer.subscription.updated received", "WebhookController", $subscriptionObj->id ?? '');
                
                        // Normalize metadata (handles array or object)
                        $metadata = (object) ($subscriptionObj->metadata ?? []);
                
                        $documentId = $metadata->document_id ?? null;
                        $planId = $metadata->plan_id ?? null;
                        $orderId = $metadata->order_id ?? null;
                
                        // Try to find order and user safely
                        $order = $orderId ? Order::find($orderId) : null;
                        $user = $order?->user ?? null;
                
                        // Try to find local Subscription by stripe id, fallback to finding by order
                        $subscription = Subscription::where('stripe_subscription_id', $subscriptionObj->id)->first();
                        if (!$subscription && $order) {
                            // If you keep subscription relation on order, try that
                            $subscription = $order->subscription ?? null;
                        }
                
                        if (!$subscription) {
                            saveLog("Subscription model not found for stripe id", "WebhookController", $subscriptionObj->id);
                            // nothing to update locally — exit gracefully
                            break;
                        }
                
                        // Ensure we have a user (try subscription->user if order->user not present)
                        $user = $user ?? $subscription->user ?? null;
                        if (!$user) {
                            saveLog("User not found for subscription", "WebhookController", $subscription->id);
                            break;
                        }
                
                        // Determine pause_collection / pause behavior
                        $isPausedRemote = isset($subscriptionObj->pause_collection);
                        $pauseData = $subscriptionObj->pause_collection ?? null;
                
                        // Common period timestamps
                        $currentPeriodStart = isset($subscriptionObj->current_period_start)
                            ? Carbon::createFromTimestamp(intval($subscriptionObj->current_period_start))
                            : null;
                        $currentPeriodEnd = isset($subscriptionObj->current_period_end)
                            ? Carbon::createFromTimestamp(intval($subscriptionObj->current_period_end))
                            : null;
                
                        if ($isPausedRemote && $pauseData) {
                            $pauseBehavior = $pauseData->behavior ?? null;
                            $pauseResumesAt = isset($pauseData->resumes_at) ? Carbon::createFromTimestamp(intval($pauseData->resumes_at)) : null;
                
                            // Update subscription record for paused state
                            $subscription->update([
                                'status' => $subscriptionObj->status,
                                'stripe_status' => $subscriptionObj->status,
                                'current_period_start_date' => $currentPeriodStart,
                                'current_period_end_date' => $currentPeriodEnd,
                                'pause_behavior' => $pauseBehavior,
                                'is_paused' => true,
                                'pause_start_at' => now(),
                                'pause_end_at' => $pauseResumesAt,
                            ]);
                
                            // Update user trial_ends_at only if resumes_at present
                            $user->update([
                                'trial_ends_at' => $pauseResumesAt,
                            ]);
                        } else {
                            // Not paused: clear pause fields
                            $subscription->update([
                                'status' => $subscriptionObj->status,
                                'stripe_status' => $subscriptionObj->status,
                                'current_period_start_date' => $currentPeriodStart,
                                'current_period_end_date' => $currentPeriodEnd,
                                'is_paused' => false,
                                'pause_behavior' => null,
                                'pause_start_at' => null,
                                'pause_end_at' => null,
                            ]);
                
                            $user->update([
                                'trial_ends_at' => null,
                            ]);
                        }
                
                        // Handle trial_end: if trial ended (timestamp present) and it's <= today, expire/cancel locally
                        $trialEndTs = $subscriptionObj->trial_end ?? null;
                        if ($trialEndTs) {
                            $trialEndDate = Carbon::createFromTimestamp(intval($trialEndTs));
                            saveLog("customer.subscription.updated trial_end", "WebhookController", $trialEndDate->toDateTimeString());
                
                            if ($trialEndDate->lte(Carbon::today())) {
                                // mark subscription ended/cancelled locally
                                $subscription->update([
                                    'end_date' => $trialEndDate,
                                    'status' => 'canceled',
                                    'stripe_status' => $subscriptionObj->status,
                                ]);
                
                                $user->update([
                                    'trial_ends_at' => $trialEndDate,
                                    'is_subscribed' => 0,
                                ]);
                
                                UserPlan::where([
                                    ['user_id', $subscription->user_id],
                                    ['document_id', $documentId],
                                    ['plan_id', $subscription->plan_id]
                                ])->update(['status' => 0]);
                
                                // Update order & transactions if present
                                $localOrder = Order::where('stripe_subscription_id', $subscription->stripe_subscription_id)->first();
                                if ($localOrder) {
                                    $localOrder->update(['status' => 4]); // expired/cancelled status per your app
                                    Transaction::where('order_id', $localOrder->id)->update(['status' => 'expired']);
                                }
                
                                // Remove credits for this subscription cycle
                                CreditTransaction::where('subscription_id', $subscription->id)
                                    ->where('type', 1)
                                    ->update([
                                        'amount' => 0,
                                        'description' => "Credits removed after trial ended"
                                    ]);
                
                                // UserCredit::where('user_id', $subscription->user_id)->update(['balance' => 0]);
                
                                FreeSubscription::where('user_id', $subscription->user_id)
                                    ->where('order_id', $subscription->order_id)
                                    ->where('subscription_id', $subscription->stripe_subscription_id)
                                    ->update(['status' => 'expired']);
                            }
                        }
                
                        // If metadata changed and we have a plan/document mapping, update any CreditTransaction document_id
                        if ($planId) {
                            $creditTransaction = CreditTransaction::where('plan_id', $planId)->first();
                            if ($creditTransaction) {
                                $creditTransaction->update([
                                    'document_id' => $documentId,
                                ]);
                            }
                        }
                
                        // Create an audit log
                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $planId ?? $subscription->plan_id,
                            'event_type' => 'customer.subscription.updated',
                            'description' => ($isPausedRemote ? 'Subscription paused/updated' : 'Subscription updated'),
                            'status' => $subscription->status,
                            'payload' => json_encode($event),
                        ]);
                
                        saveLog("customer.subscription.updated processed", "WebhookController", $subscription->stripe_subscription_id);
                
                    } catch (\Exception $e) {
                        saveLog("customer.subscription.updated error:", "WebhookController", $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                    break;
                
            
                case 'customer.subscription.deleted':

                    
                    $subscriptionObj = $event->data->object;

                    
                    saveLog("customer.subscription.updated received", "WebhookController", $subscriptionObj->id ?? '');
            
                    // Normalize metadata (handles array or object)
                    $metadata = (object) ($subscriptionObj->metadata ?? []);
            
                    $documentId = $metadata->document_id ?? null;

                    $subscription = Subscription::where('stripe_subscription_id', $subscriptionObj->id)->first();
                    if ($subscription) {
                        $subscription->update([
                            'status' => 'cancel', 
                            'stripe_status' => 'canceled',
                            'end_date' => Carbon::now()
                        ]);
                
                        UserPlan::where([
                            ['user_id', $subscription->user_id],
                            // ['document_i', $documentId],
                            ['plan_id', $subscription->plan_id]
                        ])->update(['status' => 0]);
                
                        User::where('id', $subscription->user_id)->update(['is_subscribed' => 0]);
                
                        $order = Order::where('stripe_subscription_id', $subscription->stripe_subscription_id)->first();
                        if ($order) {
                            $order->update(['status' => 3]); 
                            Transaction::where('order_id', $order->id)->update(['status' => 'cancelled']);
                        }
                
                        CreditTransaction::where('subscription_id', $subscription->id)
                            ->where('type', 1)
                            ->update([
                                'description' => "Cancelled due to subscription deletion"
                            ]);
                
                        UserCredit::where('user_id', $subscription->user_id)->update(['balance' => 0]);
                
                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => 'customer.subscription.deleted',
                            'description' => 'Subscription Deleted',
                            'status' => 'cancel',
                            'payload' => json_encode($event),
                        ]);
                    }

                    break;
                    
                case 'customer.subscription.trial_will_end':
                    $subscriptionObj = $event->data->object;
                    saveLog("Subscription object:", "WebhookController", $subscriptionObj);
                    
                    $subscription = Subscription::where('stripe_subscription_id', $subscriptionObj->id)->first();
                    if($subscription){
                        $user = $subscription->user;
                        $subscription->update([
                            'status'                    => 'active',  
                            'stripe_status'             => $subscriptionObj->status,
                            'current_period_start_date' => Carbon::createFromTimestamp($subscriptionObj->current_period_start),
                            'current_period_end_date'   => Carbon::createFromTimestamp($subscriptionObj->current_period_end),
                            'end_date'                  => Carbon::createFromTimestamp($subscriptionObj->trial_end), 
                            'pause_behavior'            => 'trial_will_end', 
                            'updated_at'                => now(),
                        ]);
                        $user->update([
                            'trial_ends_at' => Carbon::createFromTimestamp($subscriptionObj->trial_end),
                            'is_subscribed' => 1,
                        ]);
                        saveLog("Trial will end soon, subscription updated", "WebhookController", $subscription->id);

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => 'customer.subscription.trial_will_end',
                            'description' => 'Trial will end soon',
                            'status' => 'active',
                            'payload' => json_encode($event),
                        ]);
                    }

                    break;

                case 'invoice.payment_failed':
                    try {
                        $subscriptionId = $object->subscription ?? null;
                
                        if (!$subscriptionId) {
                            Log::warning('invoice.payment_failed: No subscription ID found in payload.');
                            saveLog("invoice.payment_failed:", "WebhookController", 'No subscription ID found in payload.');
                            break;
                        }
                
                        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();
                
                        if (!$subscription) {
                            Log::warning("invoice.payment_failed: Subscription not found for ID: {$subscriptionId}");
                            saveLog("invoice.payment_failed:", "WebhookController", "Subscription not found for ID: {$subscriptionId}");
                            break;
                        }
                
                        $subscription->update(['status' => 'incomplete']);
                
                        $order = Order::where('stripe_subscription_id', $subscription->stripe_subscription_id)->first();
                        if ($order) {
                            $order->update(['status' => 0]);
                
                            Transaction::where('order_id', $order->id)->update(['status' => 'incomplete']);
                        } else {
                            Log::warning("invoice.payment_failed: Order not found for subscription ID: {$subscriptionId}");
                            saveLog("invoice.payment_failed:", "WebhookController", "Order not found for subscription ID: {$subscriptionId}");
                        }
                
                        
                        User::where('id', $subscription->user_id)->update(['is_subscribed' => 0]);
                
                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => 'invoice.payment_failed',
                            'description' => 'Payment failed',
                            'status' => 'incomplete',
                            'payload' => json_encode($event),
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('invoice.payment_failed webhook error: ' . $e->getMessage(), [
                            'exception' => $e,
                            'stripe_payload' => $event,
                        ]);
                        saveLog("invoice.payment_failed webhook error:", "WebhookController", $e->getMessage());
                        
                        return response()->json(['error' => 'Webhook handler failed'], 200); 
                    }
                
                    break;
                
                    
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $intent_id = $paymentIntent->id;

                    $stripeCustomerId = $paymentIntent->customer ?? null;

                    \Log::info("Processing succeeded payment for intent ID: $intent_id");
                    saveLog("Processing succeeded payment for intent ID:", "WebhookController", $intent_id);

                    $transaction = Transaction::where('payment_intent', $intent_id)->first();
                    if($transaction){
                        $order = $transaction->order;
                        $userId = $order?->user?->id;
                        if ($order) {
                            $transaction->update([
                                'amount' => $order->amount,
                                'type' => 'stripe',
                                'status' => 'succeeded',
                            ]);
                            $order->update(['status' => 1]);
                            \Log::info("Order {$order->id} marked as succeeded");
                            saveLog("Order marked as succeeded:", "WebhookController", $order->id);

                            $user = User::find($userId);
                            $user->update([
                                'stripe_cus_id' => $stripeCustomerId,
                            ]);

                        } else {
                            \Log::error("Order not found for transaction with intent ID: $intent_id");
                            saveLog("Order not found for transaction with intent ID:", "WebhookController", $intent_id);
                        }
                    } else {
                        \Log::error("Transaction not found for payment intent ID: $intent_id");
                        saveLog("Transaction not found for payment intent ID:", "WebhookController", $intent_id);
                    }
                break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $intent_id = $paymentIntent->id;
                    \Log::info("Processing failed payment for intent ID: $intent_id");
                    saveLog("Processing failed payment for intent ID:", "WebhookController", $intent_id);

                    $transaction = Transaction::where('payment_intent', $intent_id)->first();
                    if ($transaction) {
                        $order = $transaction->order;
                        if ($order) {
                            $transaction->update([
                                'amount' => $order->amount,
                                'type' => 'stripe',
                                'status' => 'payment_failed',
                            ]);
                            saveLog("Processing failed payment for intent ID:", "WebhookController", $intent_id);
                            \Log::info("Order {$order->id} marked as payment_failed");
                        } else {
                            saveLog("Order not found for transaction with intent ID:", "WebhookController", $intent_id);
                            \Log::error("Order not found for transaction with intent ID: $intent_id");
                        }
                    } else {
                        saveLog("Transaction not found for payment intent ID:", "WebhookController", $intent_id);
                        \Log::error("Transaction not found for payment intent ID: $intent_id");
                    }
                    break;
                
                case 'payment_intent.processing':
                    $paymentIntent = $event->data->object;
                    saveLog("Payment processing:", "WebhookController", $paymentIntent->id);
                    \Log::info('Payment processing: ' . $paymentIntent->id);
                    break;

                case 'charge.refunded':
                    $charge = $event->data->object;
                    $intent_id = $charge->payment_intent;

                    saveLog("Processing refund for intent ID:", "WebhookController", $intent_id);
                    \Log::info("Processing refund for intent ID: $intent_id");

                    $transaction = Transaction::where('payment_intent', $intent_id)->first();
                    if ($transaction) {
                        $order = $transaction->order;
                        if($order){
                            $transaction->update(['status' => 'refunded']);
                            $order->update(['status' => 2]);
                            saveLog("Order marked as refunded:", "WebhookController", $order->id);
                            \Log::info("Order {$order->id} marked as refunded");
                        }else{
                            saveLog("Order not found for refunded transaction with intent ID:", "WebhookController", $intent_id);
                            \Log::error("Order not found for refunded transaction with intent ID: $intent_id");
                        }
                    }else{
                        saveLog("Transaction not found for refunded payment intent ID:", "WebhookController", $intent_id);
                        // \Log::error("Transaction not found for refunded payment intent ID: $intent_id");
                    }
                    break;

                default:
                    saveLog("Unhandled event type:", "WebhookController", $event->type);
                    \Log::warning("Unhandled event type: " . $event->type);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            saveLog("Webhook error:", "WebhookController", $e->getMessage());
            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }

    public function handlePaypalWebhook(Request $request)
    {
        try {
            // $payload = $request->all();

            if (empty($payload) || !isset($payload['event_type'], $payload['id'])) {
                Log::warning('PayPal Webhook: Invalid payload structure');
                return response()->json(['error' => 'Invalid PayPal webhook payload'], 400);
            }

            $eventType = $payload['event_type'];
            $eventId   = $payload['id'];
            $resource  = $payload['resource'] ?? [];

            // Verify request is from paypal
            if (!$this->verifyPaypalSignature($request)) {
                Log::warning('PayPal Webhook: Signature verification failed', ['event_id' => $eventId,]);
                return response()->json(['error' => 'Invalid PayPal signature'], 401);
            }

            if (PaypalWebhookLog::where('event_id', $eventId)->exists()) {
                Log::info('PayPal Webhook: Duplicate event detected', [
                    'event_id' => $eventId,
                    'event_type' => $eventType
                ]);
                return response()->json(['status' => 'duplicate'], 200);
            }

            PaypalWebhookLog::create([
                'event_id'   => $eventId,
                'event_type' => $eventType,
                'payload'    => json_encode($payload),
                'status'     => 'received',
            ]);

            Log::info('PayPal Webhook: Event received', [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'resource_id' => $resource['id'] ?? null
            ]);

            switch ($eventType) {
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    try {
                        DB::beginTransaction();
                        
                        $subscriptionId = $resource['id'] ?? null;
                        $planId = $resource['plan_id'] ?? null;
                        $status = strtolower($resource['status'] ?? 'active');
                        $customData = $resource['custom_id'] ?? null;
                        $subscriberInfo = $resource['subscriber'] ?? [];
                        $billingInfo = $resource['billing_info'] ?? [];

                        if (!$subscriptionId || !$planId) {
                            Log::warning('PayPal Webhook: Missing subscription or plan ID');
                            DB::rollBack();
                            break;
                        }

                        $orderId = null;
                        if ($customData) {
                            $parts = explode('|', $customData);
                            $orderId = $parts[0] ?? null;
                        }

                        if (Subscription::where('paypal_subscription_id', $subscriptionId)->exists()) {
                            Log::info('PayPal Webhook: Subscription already exists', ['sub_id' => $subscriptionId]);
                            DB::rollBack();
                            break;
                        }

                        $user = null;
                        $order = $orderId ? Order::find($orderId) : null;

                        if ($order) {
                            $user = $order->user;
                        }

                        if (!$user && isset($subscriberInfo['email_address'])) {
                            $user = User::where('email', $subscriberInfo['email_address'])->first();
                        }

                        if (!$user) {
                            Log::error('PayPal Webhook: User not found', ['sub_id' => $subscriptionId, 'order_id' => $orderId]);
                            DB::rollBack();
                            break;
                        }

                        $plan = Plans::where('paypal_plan_id', $planId)->first();
                        if (!$plan) {
                            Log::error('PayPal Webhook: Plan not found', ['plan_id' => $planId]);
                            DB::rollBack();
                            break;
                        }

                        $startTime = isset($resource['start_time']) ? Carbon::parse($resource['start_time']) : now();
                        $nextBillingTime = isset($billingInfo['next_billing_time'])
                            ? Carbon::parse($billingInfo['next_billing_time'])
                            : $startTime->copy()->addMonth();

                        $user->update([
                            'is_subscribed' => 1,
                            'paypal_payer_id' => $subscriberInfo['payer_id'] ?? $user->paypal_payer_id,
                        ]);

                        $newSubscription = Subscription::create([
                            'order_id' => $orderId,
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'paypal_subscription_id' => $subscriptionId,
                            'paypal_plan_id' => $planId,
                            'status' => 'active',
                            'paypal_status' => $status,
                            'start_date' => $startTime,
                            'end_date' => $nextBillingTime,
                            'current_period_start_date' => $startTime,
                            'current_period_end_date' => $nextBillingTime,
                        ]);

                        // ✅ Free Trial cancel karo
                        \App\Models\FreeTrail::where('user_id', $user->id)
                            ->where('status', 'active')
                            ->update([
                                'status'   => 'converted',
                                'end_date' => now(),
                            ]);

                        UserPlan::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'plan_id' => $plan->id,
                                'status' => 1,
                                'start_date' => $startTime,
                                'end_date' => $nextBillingTime,
                            ]
                        );

                        SubscriptionLog::create([
                            'subscription_id' => $newSubscription->id,
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'event_type' => 'BILLING.SUBSCRIPTION.ACTIVATED',
                            'description' => 'PayPal subscription activated',
                            'payload' => json_encode($payload),
                            'status' => 'active',
                        ]);
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('PayPal Activated Error: ' . $e->getMessage(), [
                            'trace' => $e->getTraceAsString()
                        ]);
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                    break;
                    
                case 'PAYMENT.SALE.COMPLETED':
                    try {
                        
                        DB::beginTransaction();
                        
                        $saleId = $resource['id'] ?? null;
                        $billingAgreementId = $resource['billing_agreement_id'] ?? null;
                        $amount = $resource['amount']['total'] ?? 0;
                        $state = strtolower($resource['state'] ?? 'completed');
                        $customData = $resource['custom_id'] ?? null;

                        Log::info('PayPal Payment Sale Completed', [
                            'sale_id' => $saleId,
                            'billing_agreement_id' => $billingAgreementId,
                            'custom_data' => $customData
                        ]);

                        $orderId = null;
                        $extractedDocId = null;
                        if ($customData) {
                            $parts = explode('|', $customData);
                            $orderId = $parts[0] ?? null;
                            $extractedDocId = $parts[2] ?? null;
                        }

                        $subscription = null;
                        if ($billingAgreementId) {
                            $subscription = Subscription::where('paypal_subscription_id', $billingAgreementId)->first();
                        }

                        $order = $orderId ? Order::find($orderId) : null;

                        if (!$subscription && $order) {
                            $subscription = $order->subscription;
                        }

                        if (!$subscription) {
                            Log::warning('PayPal Webhook: Subscription not found for payment', [
                                'billing_agreement_id' => $billingAgreementId,
                                'order_id' => $orderId
                            ]);
                            DB::rollBack();
                            return response()->json(['error' => 'Subscription not found yet'], 404);
                        }

                        $order = $subscription->order ?? $order;
                        $user = $subscription->user ?? $order?->user;
                        $plan = $subscription->plan ?? $order?->plan;
                        $documentId = $order?->document_id ?? $extractedDocId;

                        if (!$user || !$plan) {
                            Log::warning('PayPal Webhook: User or Plan not found');
                            DB::rollBack();
                            break;
                        }

                        $createTime = isset($resource['create_time']) ? Carbon::parse($resource['create_time']) : now();
                        $periodStart = $createTime;
                        $periodEnd = $createTime->copy()->addMonth();

                        $documentLimit = optional(web_setting('fair_use_document_limit'))->value ?? 50;

                        $userCredit = UserCredit::where('user_id', $user->id)
                            ->where('document_id', $documentId)
                            ->first();

                        $previousBalance = $userCredit->balance ?? 0;
                        $carryForward = $previousBalance;
                        $newCredits = $documentLimit;
                        $totalBalance = $carryForward + $newCredits;

                        UserCredit::updateOrCreate(
                            ['user_id' => $user->id, 'document_id' => $documentId],
                            ['balance' => $totalBalance]
                        );

                        CreditTransaction::create([
                            'user_id'         => $user->id,
                            'document_id'     => $documentId,
                            'plan_id'         => $plan->id,
                            'subscription_id' => $subscription->id,
                            'order_id'        => $order?->id,
                            'carry_forward'   => $carryForward,
                            'used_amount'     => 0,
                            'amount'          => ($carryForward > 0) ? $carryForward : $newCredits,
                            'type'            => ($carryForward > 0) ? 0 : 1,
                            'transaction_date' => now(),
                            'period_start'    => $periodStart,
                            'period_end'      => $periodEnd,
                            'description'     => ($carryForward > 0) ? 'Carried forward unused credits (PayPal)' : 'New billing cycle credits added (PayPal)'
                        ]);

                        $subscription->update([
                            'status' => 'active',
                            'paypal_status' => $state,
                            'current_period_start_date' => $periodStart,
                            'current_period_end_date' => $periodEnd,
                        ]);

                        if ($order) {
                            $order->update(['status' => 1]);

                            Transaction::where('order_id', $order->id)->update([
                                'paypal_sale_id' => $saleId,
                                'amount' => $amount,
                                'status' => 'succeeded',
                            ]);
                        }

                        UserPlan::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'plan_id' => $plan->id,
                                'status' => 1,
                                'start_date' => $periodStart,
                                'end_date' => $periodEnd,
                            ]
                        );

                        $user->update(['is_subscribed' => 1]);

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'event_type' => 'PAYMENT.SALE.COMPLETED',
                            'description' => "Credits refreshed: carry {$carryForward}, new {$newCredits}, total {$totalBalance}",
                            'payload' => json_encode($payload),
                            'status' => 'active',
                        ]);
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('PAYMENT.SALE.COMPLETED error: ' . $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                    break;
                    
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                    try {
                        DB::beginTransaction();
                        
                        $subscriptionId = $resource['id'] ?? null;
                        $statusKey = ($eventType === 'BILLING.SUBSCRIPTION.CANCELLED') ? 'cancel' : 'expired';
                        $orderStatus = ($eventType === 'BILLING.SUBSCRIPTION.CANCELLED') ? 3 : 4;
                        $isExpired = ($eventType === 'BILLING.SUBSCRIPTION.EXPIRED');

                        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();
                        if (!$subscription) {
                            DB::rollBack();
                            break;
                        }

                        $order = $subscription->order;
                        $documentId = $order?->document_id;

                        $expiryDate = $isExpired ? now() : $subscription->current_period_end_date;

                        $subscription->update([
                            'status' => $statusKey,
                            'paypal_status' => strtolower($resource['status'] ?? $statusKey),
                            'end_date' => $expiryDate
                        ]);

                        if ($isExpired) {
                            UserPlan::where('user_id', $subscription->user_id)
                                ->where('plan_id', $subscription->plan_id)
                                ->update(['status' => 0]);

                            User::where('id', $subscription->user_id)->update([
                                'is_subscribed' => 0,
                                'trial_ends_at' => null
                            ]);

                            UserCredit::where('user_id', $subscription->user_id)
                                ->where('document_id', $documentId)
                                ->update(['balance' => 0]);
                        }

                        if ($order) {
                            $order->update(['status' => $orderStatus]);
                            Transaction::where('order_id', $order->id)->update([
                                'status' => ($statusKey === 'cancel' ? 'cancelled' : 'expired')
                            ]);
                        }

                        CreditTransaction::where('subscription_id', $subscription->id)
                            ->where('type', 1)
                            ->update(['description' => "Credits removed due to PayPal $statusKey"]);

                        FreeSubscription::where('subscription_id', $subscriptionId)->update(['status' => $statusKey]);

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => $eventType,
                            'description' => "PayPal subscription $statusKey",
                            'status' => $statusKey,
                            'payload' => json_encode($payload),
                        ]);
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("$eventType error: " . $e->getMessage());
                    }
                    break;
                    
                case 'PAYMENT.SALE.DENIED':
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    try {
                        DB::beginTransaction();
                        
                        $subscriptionId = $resource['billing_agreement_id'] ?? $resource['id'] ?? null;
                        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();
                        if (!$subscription) {
                            DB::rollBack();
                            break;
                        }

                        $internalStatus = ($eventType === 'PAYMENT.SALE.DENIED') ? 'incomplete' : 'suspended';

                        $subscription->update([
                            'status' => $internalStatus,
                            'paypal_status' => 'suspended',
                            'is_paused' => true,
                            'pause_start_at' => now()
                        ]);

                        User::where('id', $subscription->user_id)->update(['is_subscribed' => 0]);

                        UserPlan::where('user_id', $subscription->user_id)
                            ->where('plan_id', $subscription->plan_id)
                            ->update(['status' => 0]);

                        if ($subscription->order) {
                            $subscription->order->update(['status' => 0]);
                            Transaction::where('order_id', $subscription->order->id)->update(['status' => 'failed']);
                        }

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => $eventType,
                            'description' => "PayPal subscription $internalStatus",
                            'status' => $internalStatus,
                            'payload' => json_encode($payload),
                        ]);
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error("$eventType error: " . $e->getMessage());
                    }
                    break;

                case 'BILLING.SUBSCRIPTION.UPDATED':
                    try {
                        DB::beginTransaction();
                        
                        $subscriptionId = $resource['id'] ?? null;

                        if (!$subscriptionId) {
                            Log::warning('PayPal Subscription Updated: Missing subscription ID', $payload);
                            DB::rollBack();
                            break;
                        }

                        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();

                        if (!$subscription) {
                            Log::warning('PayPal Subscription Updated: Subscription not found', [
                                'paypal_subscription_id' => $subscriptionId
                            ]);
                            DB::rollBack();
                            break;
                        }

                        $updates = [];

                        if (!empty($resource['status'])) {
                            $updates['paypal_status'] = strtolower($resource['status']);
                        }

                        if (!empty($resource['plan_id']) && $resource['plan_id'] !== $subscription->paypal_plan_id) {
                            $updates['paypal_plan_id'] = $resource['plan_id'];
                        }

                        if (!empty($resource['billing_info']['next_billing_time'])) {
                            $updates['current_period_end_date'] = Carbon::parse(
                                $resource['billing_info']['next_billing_time']
                            );
                        }

                        if (!empty($updates)) {
                            $subscription->update($updates);
                        }

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id'         => $subscription->user_id,
                            'plan_id'         => $subscription->plan_id,
                            'event_type'      => 'BILLING.SUBSCRIPTION.UPDATED',
                            'description'     => 'Subscription updated via PayPal webhook',
                            'status'          => $updates['paypal_status'] ?? $subscription->paypal_status,
                            'payload'         => json_encode($payload),
                        ]);

                        Log::info('PayPal Subscription Updated', [
                            'paypal_subscription_id' => $subscriptionId,
                            'updates' => $updates,
                        ]);

                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Log::error('BILLING.SUBSCRIPTION.UPDATED webhook failed', [
                            'error' => $e->getMessage(),
                            'payload' => $payload,
                        ]);
                    }
                    break;

                case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
                    try {
                        DB::beginTransaction();
                        
                        $subscriptionId = $resource['id'] ?? null;
                        $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();
                        
                        if ($subscription) {
                            $subscription->update([
                                'status' => 'active',
                                'paypal_status' => 'active',
                                'is_paused' => false,
                                'pause_start_at' => null,
                            ]);
                            
                            User::where('id', $subscription->user_id)->update(['is_subscribed' => 1]);
                            
                            UserPlan::where('user_id', $subscription->user_id)
                                ->where('plan_id', $subscription->plan_id)
                                ->update(['status' => 1]);
                            
                            if ($subscription->order) {
                                $subscription->order->update(['status' => 1]);
                            }
                            
                            SubscriptionLog::create([
                                'subscription_id' => $subscription->id,
                                'user_id' => $subscription->user_id,
                                'plan_id' => $subscription->plan_id,
                                'event_type' => 'BILLING.SUBSCRIPTION.RE-ACTIVATED',
                                'description' => 'Subscription reactivated',
                                'status' => 'active',
                                'payload' => json_encode($payload),
                            ]);
                        }
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('BILLING.SUBSCRIPTION.RE-ACTIVATED error: ' . $e->getMessage());
                    }
                    break;
                
                case 'PAYMENT.SALE.REFUNDED':
                    try {
                        DB::beginTransaction();
                        
                        $saleId = $resource['sale_id'] ?? $resource['id'] ?? null;
                        $billingAgreementId = $resource['billing_agreement_id'] ?? null;
                        $refundAmount = $resource['amount']['total'] ?? 0;
                        
                        $subscription = Subscription::where('paypal_subscription_id', $billingAgreementId)->first();
                        
                        if ($subscription) {
                            $order = $subscription->order;
                            
                            if ($order) {
                                Transaction::where('order_id', $order->id)->update([
                                    'status' => 'refunded',
                                    'refund_amount' => $refundAmount,
                                    'refunded_at' => now(),
                                ]);
                            }
                            
                            SubscriptionLog::create([
                                'subscription_id' => $subscription->id,
                                'user_id' => $subscription->user_id,
                                'plan_id' => $subscription->plan_id,
                                'event_type' => 'PAYMENT.SALE.REFUNDED',
                                'description' => "Payment refunded: $refundAmount",
                                'status' => 'refunded',
                                'payload' => json_encode($payload),
                            ]);
                        }
                        
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('PAYMENT.SALE.REFUNDED error: ' . $e->getMessage());
                    }
                    break;
                case 'PAYMENT.SALE.REVERSED':
                    try {
                        DB::beginTransaction();

                        $saleId = $resource['id'] ?? null;
                        $billingAgreementId = $resource['billing_agreement_id'] ?? null;
                        $parentPayment = $resource['parent_payment'] ?? null;
                        
                        Log::warning('PayPal Payment Reversal Detected', [
                            'sale_id' => $saleId,
                            'billing_agreement_id' => $billingAgreementId
                        ]);

                        $subscription = Subscription::where('paypal_subscription_id', $billingAgreementId)->first();

                        if (!$subscription) {
                            Log::error('PayPal Reversal: Subscription not found', ['sub_id' => $billingAgreementId]);
                            DB::rollBack();
                            break;
                        }

                        $user = $subscription->user;
                        $order = $subscription->order;
                        $documentId = $order?->document_id;

                        if ($user) {
                            $user->update(['is_subscribed' => 0]);
                        }

                        UserPlan::where('user_id', $subscription->user_id)
                            ->where('plan_id', $subscription->plan_id)
                            ->update(['status' => 0]);


                        UserCredit::where('user_id', $subscription->user_id)
                            ->where('document_id', $documentId)
                            ->update(['balance' => 0]);

                        $subscription->update([
                            'status' => 'reversed',
                            'paypal_status' => 'reversed'
                        ]);

                        if ($order) {
                            $order->update(['status' => 2]); 
                            
                            Transaction::where('order_id', $order->id)->update([
                                'status' => 'reversed',
                                'description' => 'Payment reversed by PayPal'
                            ]);
                        }

                        SubscriptionLog::create([
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id,
                            'plan_id' => $subscription->plan_id,
                            'event_type' => 'PAYMENT.SALE.REVERSED',
                            'description' => "CRITICAL: Payment reversed. Credits revoked and access suspended.",
                            'status' => 'reversed',
                            'payload' => json_encode($payload),
                        ]);

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('PAYMENT.SALE.REVERSED error: ' . $e->getMessage());
                    }
                    break;

                default:
                    Log::info('PayPal Webhook: Unhandled event type', [
                        'event_type' => $eventType
                    ]);
                    break;
            }

            // Update webhook log status
            PaypalWebhookLog::where('event_id', $eventId)->update(['status' => 'processed']);

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('PayPal Webhook: Exception occurred', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    private function verifyPaypalSignature(Request $request)
    {
        if (app()->environment('local')) {
            return true;
        }

        $webhookId     = web_setting('paypal_webhook_id', true);
        $mode          = web_setting('paypal_mode', true);
        $client_id     = web_setting('paypal_client_id', true);
        $client_secret = web_setting('paypal_secret_key', true);

        if (empty($webhookId)) {
            Log::warning('PayPal Webhook: webhook_id missing');
            return false;
        }

        $authAlgo         = $request->header('PAYPAL-AUTH-ALGO');
        $certUrl          = $request->header('PAYPAL-CERT-URL');
        $transmissionId   = $request->header('PAYPAL-TRANSMISSION-ID');
        $transmissionSig  = $request->header('PAYPAL-TRANSMISSION-SIG');
        $transmissionTime = $request->header('PAYPAL-TRANSMISSION-TIME');

        if (
            !$authAlgo ||
            !$certUrl ||
            !$transmissionId ||
            !$transmissionSig ||
            !$transmissionTime
        ) {
            Log::warning('PayPal Webhook: Missing signature headers');
            return false;
        }

        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        try {
            $accessToken = Cache::remember(
                "paypal_webhook_access_token_{$mode}",
                300,
                function () use ($baseUrl, $client_id, $client_secret) {
                    $response = Http::withBasicAuth($client_id, $client_secret)
                        ->asForm()
                        ->post($baseUrl . '/v1/oauth2/token', [
                            'grant_type' => 'client_credentials',
                        ]);

                    if (!$response->successful()) {
                        Log::error('PayPal Webhook: Failed to obtain access token', [
                            'response' => $response->body(),
                        ]);
                        return null;
                    }

                    return $response->json()['access_token'] ?? null;
                }
            );

            if (!$accessToken) {
                return false;
            }

            $verifyResponse = Http::withToken($accessToken)
                ->post($baseUrl . '/v1/notifications/verify-webhook-signature', [
                    'auth_algo'         => $authAlgo,
                    'cert_url'          => $certUrl,
                    'transmission_id'   => $transmissionId,
                    'transmission_sig'  => $transmissionSig,
                    'transmission_time' => $transmissionTime,
                    'webhook_id'        => $webhookId,
                    'webhook_event'     => $request->all(),
                ]);

            if (!$verifyResponse->successful()) {
                Log::error('PayPal Webhook: Signature verification API failed', [
                    'response' => $verifyResponse->body(),
                ]);
                return false;
            }

            return ($verifyResponse->json()['verification_status'] ?? null) === 'SUCCESS';

        } catch (\Throwable $e) {
            Log::error('PayPal Webhook: Signature verification exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }



}
