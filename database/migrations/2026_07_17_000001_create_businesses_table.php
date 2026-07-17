<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->string('city')->index();
            $table->string('timezone')->default('America/Argentina/Buenos_Aires');
            $table->string('whatsapp_phone')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->boolean('in_directory')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('businesses');
    }
};
