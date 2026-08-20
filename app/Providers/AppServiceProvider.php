<?php

namespace App\Providers;

use App\Models\DocumentCategory;
use App\Models\HomeContent;
use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingService::class, fn () => new SettingService());
    }

    public function boot(): void
    {
        if (! app()->runningUnitTests()) {
            $settingService = $this->app->make(SettingService::class);
            $secret = $settingService->getValue('stripe_secret_key');

            if (is_string($secret) && $secret !== '') {
                Stripe::setApiKey($secret);
            }

            View::composer('*', function () {
                $keys = ['popular'];
                $results = HomeContent::query()->whereIn('key', $keys)->get()->keyBy('key');
                $popularIds = isset($results['popular']) ? json_decode($results['popular']->value, true) : [];

                $headerPopularCategories = empty($popularIds)
                    ? collect([])
                    : DocumentCategory::query()
                        ->where('is_deleted', 0)
                        ->whereIn('id', $popularIds)
                        ->with(['documents' => function ($query) {
                            $query->where('published', 1);
                        }])
                        ->get();

                View::share('header_popular_categories', $headerPopularCategories);
            });
        }
    }
}
