<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Carbon\CarbonImmutable;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property CarbonImmutable $starts_at
 * @property CarbonImmutable $ends_at
 * @property CarbonImmutable|null $reminded_at
 * @property CarbonImmutable|null $cancelled_at
 * @property BookingStatus $status
 */
#[Fillable([
    'professional_id', 'service_id', 'client_name', 'client_email', 'client_phone',
    'note', 'starts_at', 'ends_at', 'status', 'management_token',
])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'starts_at' => 'immutable_datetime',
            'ends_at' => 'immutable_datetime',
            'reminded_at' => 'immutable_datetime',
            'cancelled_at' => 'immutable_datetime',
            'status' => BookingStatus::class,
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return BelongsTo<Professional, $this> */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @return HasOne<Review, $this> */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canBeReviewed(): bool
    {
        return $this->status === BookingStatus::Attended && $this->review === null;
    }

    /** @param  Builder<self>  $query */
    public function scopeOccupying(Builder $query): void
    {
        $query->where('status', '!=', BookingStatus::Cancelled->value);
    }

    /** @return array{token: string, hash: string} */
    public static function newManagementToken(): array
    {
        $token = Str::random(48);

        return ['token' => $token, 'hash' => hash('sha256', $token)];
    }

    public static function findByManagementToken(string $token): ?self
    {
        return static::where('management_token', hash('sha256', $token))->first();
    }

    public function managementDeadline(): CarbonImmutable
    {
        return $this->starts_at->subHours($this->service->cancellation_hours);
    }

    public function clientCanManage(): bool
    {
        return $this->status === BookingStatus::Confirmed
            && CarbonImmutable::now()->lt($this->managementDeadline());
    }

    public function cancel(): void
    {
        $this->forceFill([
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => CarbonImmutable::now(),
        ])->save();
    }
}
