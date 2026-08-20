<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PayPalClient::class, function ($app) {
            $paypal = new PayPalClient();
            $mode = web_setting('paypal_mode', true);
            $client_id  = web_setting('paypal_client_id', true);
            $client_secret = web_setting('paypal_secret_key', true);

            // dd([
            //     'mode' => $mode,
            //     'client_id' => $client_id,
            //     'secret' => $client_secret,
            // ]);

            // $mode = env('PAYPAL_MODE');
            // $client_id = env('PAYPAL_SANDBOX_CLIENT_ID');
            // $client_secret = env('PAYPAL_SANDBOX_CLIENT_SECRET');

            if (!$client_id || !$client_secret) {
                throw new \Exception("PayPal credentials are missing. Please check your settings.");
            }
            $credentials = [
                'mode'    => $mode,
                'sandbox' => [
                    'client_id'     => $mode === 'sandbox' ? $client_id : '',
                    'client_secret' => $mode === 'sandbox' ? $client_secret : '',
                    'app_id'        => 'APP-80W284485P519543T',
                ],
                'live' => [
                    'client_id'     => $mode === 'live' ? $client_id : '',
                    'client_secret' => $mode === 'live' ? $client_secret : '',
                    'app_id'        => '',

                ],
                'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
                'currency'       => env('PAYPAL_CURRENCY', 'USD'),
                'notify_url'     => env('PAYPAL_NOTIFY_URL', ''),
                'locale'         => env('PAYPAL_LOCALE', 'en_US'),
                'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true),
            ];
            $paypal->setApiCredentials($credentials);
            $token = $paypal->getAccessToken();
            $paypal->setAccessToken($token);
            return $paypal;
        });
    }
    public function boot()
    {

    }
}
