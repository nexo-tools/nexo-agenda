<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // The directory always filters by the opt-in flag first, then narrows
            // by category or city. Composite indexes keep those listings cheap as
            // the table grows.
            $table->index(['in_directory', 'category']);
            $table->index(['in_directory', 'city']);
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['in_directory', 'category']);
            $table->dropIndex(['in_directory', 'city']);
        });
    }
};
