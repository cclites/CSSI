<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\Log;

class TidyLogs extends Command
{
   
    protected $signature = 'logs:tidy';
    protected $description = 'Clear out old logs entries so the database doesn\'t get bloated';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info($this->description);

        $this->line('');

        $this->info('Clearing old logs that do not have errors');
        $count = Log::where('created_at', '<', date('Y-m-d', strtotime('-1 Week')))
        ->where('code', 200)
        ->delete();
        $this->info('   Removed '.number_format($count).' old messages without errors');

        $this->line('');

        $this->info('Clearing old logs that DO have errors');
        $count = Log::where('created_at', '<', date('Y-m-d', strtotime('-1 Month')))
        ->where('code', '!=', 200)
        ->delete();
        $this->info('   Removed '.number_format($count).' old messages with errors');

        $this->line('');
    }
}
