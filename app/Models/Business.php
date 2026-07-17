<?php

namespace App\Models;

use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'city', 'timezone', 'whatsapp_phone', 'address', 'description', 'in_directory'])]
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
