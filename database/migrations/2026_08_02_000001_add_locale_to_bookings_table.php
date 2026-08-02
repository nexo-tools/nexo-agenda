<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The language the client booked in, kept with the booking.
 *
 * Everything this app sends about an appointment is triggered by somebody else
 * or by nobody at all: the owner cancels from their own session, the reminder
 * runs from a scheduled command with no request at all. Until now the mail took
 * whatever locale happened to be active, so a client who booked in Portuguese
 * got their cancellation in the owner's Spanish, and every reminder went out in
 * APP_LOCALE regardless.
 *
 * Nullable and additive: bookings made before this fall back to the instance
 * default, which is exactly what they did before.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('locale', 5)->nullable()->after('client_email');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
