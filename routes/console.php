<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('nexo:send-reminders')->hourly();
