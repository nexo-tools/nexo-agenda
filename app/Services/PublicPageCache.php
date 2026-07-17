<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Caches the read-only data behind the public business page. Availability is
 * time-sensitive and stays live; only the stable landing data (active services,
 * rating aggregates, featured reviews) is cached and invalidated by model events
 * on Service, Review and Business (see AppServiceProvider).
 */
class PublicPageCache
{
    private const TTL = 3600;

    /**
     * @return array{
     *     services: Collection<int, Service>,
     *     ratingAverage: float,
     *     ratingCount: int,
     *     reviews: Collection<int, Review>
     * }
     */
    public function businessPage(Business $business): array
    {
        return Cache::remember($this->key($business->id), self::TTL, fn () => [
            'services' => $business->services()->where('is_active', true)->orderBy('name')->get(),
            'ratingAverage' => round((float) $business->visibleReviews()->avg('rating'), 1),
            'ratingCount' => $business->visibleReviews()->count(),
            'reviews' => $business->visibleReviews()->latest()->whereNotNull('comment')->take(3)->get(),
        ]);
    }

    public function forgetBusiness(int $businessId): void
    {
        Cache::forget($this->key($businessId));
    }

    private function key(int $businessId): string
    {
        return "public_page:business:{$businessId}";
    }
}
