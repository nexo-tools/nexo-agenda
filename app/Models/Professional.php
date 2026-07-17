<?php

namespace App\Models;

use Database\Factories\ProfessionalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'is_active'])]
class Professional extends Model
{
    /** @use HasFactory<ProfessionalFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Professional $professional) {
            $professional->feed_token ??= Str::random(48);
        });
    }

    public function regenerateFeedToken(): void
    {
        $this->forceFill(['feed_token' => Str::random(48)])->save();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Business, $this> */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** @return HasMany<ScheduleBlock, $this> */
    public function scheduleBlocks(): HasMany
    {
        return $this->hasMany(ScheduleBlock::class);
    }

    /** @return HasMany<Absence, $this> */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
