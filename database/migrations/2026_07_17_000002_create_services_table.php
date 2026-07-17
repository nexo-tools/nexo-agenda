<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('duration_minutes');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('mode')->default('in_person');
            $table->string('video_link')->nullable();
            $table->unsignedSmallInteger('buffer_minutes')->default(0);
            $table->unsignedSmallInteger('min_notice_hours')->default(2);
            $table->unsignedSmallInteger('cancellation_hours')->default(12);
            $table->unsignedSmallInteger('max_advance_days')->default(60);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['business_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::drop('services');
    }
};
