<?php

use App\Models\Professional;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('feed_token', 64)->nullable()->unique()->after('is_active');
        });

        Professional::query()->whereNull('feed_token')->each(function (Professional $professional) {
            $professional->forceFill(['feed_token' => Str::random(48)])->save();
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn('feed_token');
        });
    }
};
