<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('comment', 500)->nullable();
            $table->string('client_name');
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();

            $table->index(['business_id', 'is_hidden']);
        });
    }

    public function down(): void
    {
        Schema::drop('reviews');
    }
};
