<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            // sha256(app key | ip | user agent | date): anonymous, rotates daily, no cookies.
            $table->string('visitor_hash', 64);
            $table->timestamps();

            $table->unique(['business_id', 'date', 'visitor_hash']);
        });
    }

    public function down(): void
    {
        Schema::drop('page_visits');
    }
};
