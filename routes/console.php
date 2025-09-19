<?php

use Illuminate\Support\Facades\Artisan;


use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:database')->dailyAt('18:49');

// Schedule::command('backup:database')->everyMinute();

