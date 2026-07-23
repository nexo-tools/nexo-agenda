<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Additive only: standalone installs simply keep the column null. (ADR-004/005, nexo-id repo)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nexo_id_sub', 36)->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['nexo_id_sub']);
            $table->dropColumn('nexo_id_sub');
        });
    }
};
