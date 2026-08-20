<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FreeSubscription;
use Illuminate\Support\Facades\Notification;


class SubscriptionTrialEnds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscription-trial-ends';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check trial periods (4-5 days left, <24h left, or expired) and notify users or downgrade access accordingly';

    /**
     * Execute the console command.
    **/

    public function handle()
    {
        // 1 Users with 4–5 days left
        $fourFiveDaysUsers = User::where('is_subscribed', 0)
            ->whereBetween('trial_ends_at', [
                now()->addDays(4)->startOfDay(),
                now()->addDays(5)->endOfDay()
            ])
            ->get();

        foreach ($fourFiveDaysUsers as $user) {
            $this->info("User {$user->id} trial ends in 4–5 days.");
            // Send email/notification
            // Notification::send($user, new TrialReminderNotification($user, '4-5days'));
        }

        // 2 Users with less than 24h left
        $endingSoonUsers = User::where('is_subscribed', 0)
            ->whereBetween('trial_ends_at', [now(), now()->addDay()])
            ->get();

      

        foreach($endingSoonUsers as $user){
            $this->info("User {$user->id} has less than 24h left on trial.");
            // Send urgent reminder
            // Notification::send($user, new TrialReminderNotification($user, '24h'));
        }

        // 3 Users whose trial already ended
        $expiredUsers = User::where('is_subscribed', 0)
            ->where('trial_ends_at', '<', now())
            ->get();

        foreach($expiredUsers as $user){
            $this->info("User {$user->id} trial has expired.");
            $user->update([
                'is_subscribed' => 0, // optional if you track trial status
            ]);
            // Downgrade features, disable access, etc.
        }

        $this->info("Trial period check completed.");
    }
}

