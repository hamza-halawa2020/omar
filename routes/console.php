<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::command('payment-ways:reset-limits')->monthlyOn(1, '00:00');

Schedule::command('backup:database')->everyMinute();

