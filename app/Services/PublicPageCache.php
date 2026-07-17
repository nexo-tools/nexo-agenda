<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

/**
 * Caches the read-only data behind the public business page. Availability is
 * time-sensitive and stays live; only the stable landing data (active services,
 * rating aggregates, featured reviews) is cached and invalidated by model events
 * on Service, Review and Business (see AppServiceProvider).
 *
 * The payload is plain arrays of primitives — never Eloquent models — so it
 * survives serialization across any cache store without incomplete-object risk.
 */
class PublicPageCache
{
    private const TTL = 3600;

    /**
     * @return array{
     *     services: list<array{id: int, name: string, mode: string, duration_minutes: int, price: string|null}>,
     *     ratingAverage: float,
     *     ratingCount: int,
     *     reviews: list<array{rating: int, client_name: string, comment: string|null}>
     * }
     */
    public function businessPage(Business $business): array
    {
        return Cache::remember($this->key($business->id), self::TTL, fn () => [
            'services' => $business->services()
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn (Service $service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'mode' => $service->mode->value,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                ])
                ->all(),
            'ratingAverage' => round((float) $business->visibleReviews()->avg('rating'), 1),
            'ratingCount' => $business->visibleReviews()->count(),
            'reviews' => $business->visibleReviews()
                ->latest()
                ->whereNotNull('comment')
                ->take(3)
                ->get()
                ->map(fn (Review $review) => [
                    'rating' => $review->rating,
                    'client_name' => $review->client_name,
                    'comment' => $review->comment,
                ])
                ->all(),
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
