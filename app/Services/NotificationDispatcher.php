<?php
namespace App\Services;

use App\Models\NotificationTemplate;
use App\Notifications\DynamicNotification;
use App\Models\User;

class NotificationDispatcher
{
    public static function dispatch(string $eventType, $user = null, array $data = [])
    {
        $template = NotificationTemplate::where('type', $eventType)->first();

        if (!$template) return;

        $users = $user ? collect([$user]) : User::all();

        foreach ($users as $u) {
            $u->notify(new DynamicNotification($template, $data + ['name' => $u->name]));
        }
    }
}
