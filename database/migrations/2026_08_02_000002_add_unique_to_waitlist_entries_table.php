<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The waitlist dedupe becomes a constraint.
 *
 * WaitlistController checks `exists()` before inserting, which is a rule that
 * holds right up until two requests run at the same time — exactly what happens
 * when somebody double-taps "notify me" on a phone. The index that was there
 * (business_id, service_id, date) was not unique, so the database had no opinion.
 *
 * DATA PLAN (this migration touches live rows, DATABASE-STANDARD.md §11):
 *
 *   1. Duplicates are collapsed first, keeping the OLDEST row of each group —
 *      the one whose `notified_at` may already be set, so nobody is told twice
 *      about a slot and nobody loses their place in the queue. The query is
 *      idempotent: running it again on clean data deletes nothing.
 *   2. Then the unique index is added over the exact columns the application
 *      rule uses.
 *
 * NULL CAVEAT: `professional_id` is nullable ("any professional"). In both MySQL
 * and SQLite a NULL never collides with another NULL, so the constraint covers
 * the "specific professional" case and the application check keeps covering the
 * "any professional" one. That is deliberate and the reason the code check stays
 * where it is instead of being replaced by this index.
 *
 * ROLLBACK: down() drops the index. The de-duplication is NOT reverted — deleted
 * rows are gone, and re-creating them would be inventing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Keep the oldest of each duplicate group. Written with a subquery of
        // minimum ids so it works the same on MySQL and SQLite.
        $keep = DB::table('waitlist_entries')
            ->selectRaw('MIN(id) as id')
            ->groupBy('business_id', 'service_id', 'professional_id', 'date', 'client_email')
            ->pluck('id');

        DB::table('waitlist_entries')->whereNotIn('id', $keep)->delete();

        Schema::table('waitlist_entries', function (Blueprint $table) {
            $table->unique(
                ['business_id', 'service_id', 'professional_id', 'date', 'client_email'],
                'waitlist_entries_dedupe_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('waitlist_entries', function (Blueprint $table) {
            $table->dropUnique('waitlist_entries_dedupe_unique');
        });
    }
};
