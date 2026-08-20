<?php

use App\Models\AppLog;
use App\Models\Document;
use App\Models\MetaData;
use App\Services\Settings\SettingService;
use Illuminate\Support\Str;
use Stripe\Customer;
use Stripe\Stripe;

function saveLog($message = '', $filename = '', $payload = [])
{
    $appLog = new AppLog();
    $appLog->message = $message;
    $appLog->filename = $filename;
    $appLog->payload = json_encode($payload, true);
    $appLog->save();

    return true;
}

function web_setting($key = null, $value = false, $type = null, $modelRef = null)
{
    $service = app(SettingService::class);

    if ($type !== null) {
        return $service->allByType($type, $modelRef);
    }

    if ($key === null) {
        return $service->getMany();
    }

    if ($value === true) {
        return $service->getValue($key);
    }

    return $service->get($key, $value !== false ? $value : null);
}

function getStorageFilepath($filePath)
{
    return str_replace('public/', '', $filePath ?? null);
}

function generateFileName($file)
{
    return time() . rand(1, 50) . '.' . $file->extension();
}

function getFilePath($filePath)
{
    return storage_path('app/' . $filePath);
}

function getOrCreateCustomer()
{
    $user = auth()->user();

    if ($user && $user->stripe_cus_id) {
        return $user->stripe_cus_id;
    }

    if (! $user) {
        return null;
    }

    try {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
            ],
        ]);

        $user->stripe_cus_id = $customer->id;
        $user->save();

        return $customer->id;
    } catch (\Exception $e) {
        return null;
    }
}

function getDocument($id)
{
    return Document::find($id) ?? null;
}

function getMetadata($key)
{
    $metaData = MetaData::where('key', $key)->first();

    return $metaData ? html_entity_decode($metaData->value) : '';
}

function limitText($text, $len = 20)
{
    return Str::limit($text, $len, '...');
}

function dimage()
{
    return asset('profile_images/default_img.svg');
}
