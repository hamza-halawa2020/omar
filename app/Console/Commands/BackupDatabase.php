<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup the database';

    public function handle()
    {
        $this->call('backup:run', ['--only-db' => true]);
        return 0;
    }
}
