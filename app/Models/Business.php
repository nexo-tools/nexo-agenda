<?php

namespace App\Models;

use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'city', 'timezone', 'whatsapp_phone', 'address', 'description', 'in_directory', 'brand_color', 'logo_path'])]
class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'in_directory' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Service, $this> */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /** @return HasMany<Professional, $this> */
    public function professionals(): HasMany
    {
        return $this->hasMany(Professional::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** @return HasMany<WaitlistEntry, $this> */
    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    /** @return HasMany<Review, $this> */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** @return HasMany<Review, $this> */
    public function visibleReviews(): HasMany
    {
        return $this->reviews()->where('is_hidden', false);
    }

    /**
     * Black or white, whichever contrasts best with the accent color (WCAG-oriented).
     */
    public function accentTextColor(): string
    {
        $hex = ltrim($this->brand_color ?? '#0f766e', '#');

        [$r, $g, $b] = [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];

        // Relative luminance, sRGB.
        $luminance = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;

        return $luminance > 0.55 ? '#0f172a' : '#ffffff';
    }

    public static function uniqueSlugFor(string $name): string
    {
        $base = Str::slug($name) ?: 'negocio';
        $slug = $base;
        $suffix = 1;

        while (in_array($slug, config('nexo.reserved_slugs'), true) || static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$suffix);
        }

        return $slug;
    }
}
