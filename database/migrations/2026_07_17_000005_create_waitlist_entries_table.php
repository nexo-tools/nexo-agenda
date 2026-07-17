<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professional_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date'); // Local business date the client is waiting for.
            $table->string('client_name');
            $table->string('client_email');
            $table->dateTime('notified_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'service_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::drop('waitlist_entries');
    }
};
