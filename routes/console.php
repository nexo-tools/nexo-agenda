<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('nexo:send-reminders')->hourly();

/*
 * Shared hosting cannot run a long-lived queue worker (no daemons), so the
 * scheduler drains the database queue in short bursts instead. It rides the
 * cron that already runs the reminders:
 *
 *     * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
 *
 * Every mail in this app became queued on 2026-08-02 (family rule C2: a dead
 * SMTP must not break a booking), so without this line no mail leaves at all.
 *
 * --stop-when-empty exits once the queue drains so runs never pile up;
 * --max-time keeps a run inside its minute; withoutOverlapping is the belt to
 * that braces.
 */
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=55')
    ->everyMinute()
    ->withoutOverlapping();
