<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Review;
use App\Models\Service;
use App\Services\PublicPageCache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->invalidatePublicPageCacheOnChanges();
    }

    /**
     * Drop the cached public-page data whenever the models it renders change.
     */
    private function invalidatePublicPageCacheOnChanges(): void
    {
        $cache = $this->app->make(PublicPageCache::class);

        $forget = fn (int $businessId) => $cache->forgetBusiness($businessId);

        Service::saved(fn (Service $service) => $forget($service->business_id));
        Service::deleted(fn (Service $service) => $forget($service->business_id));
        Review::saved(fn (Review $review) => $forget($review->business_id));
        Review::deleted(fn (Review $review) => $forget($review->business_id));
        Business::saved(fn (Business $business) => $forget($business->id));
    }
}
