<?php

namespace App\Models;

use App\Enums\ServiceMode;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $business_id
 * @property ServiceMode $mode
 * @property bool $is_active
 */
#[Fillable([
    'name', 'duration_minutes', 'price', 'mode', 'video_link', 'buffer_minutes',
    'min_notice_hours', 'cancellation_hours', 'max_advance_days', 'is_active',
])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'mode' => ServiceMode::class,
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
