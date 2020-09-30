<?php

namespace App\Jobs;

use App\Models\Check;

use \Carbon\Carbon;

// Notifications
use Notification;
use \App\Notifications\NewEducationCheckEmail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EducationCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $check;

    public function __construct(Check $check)
    {
        $this->check = $check;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $check = $this->check;
        
        Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new NewEducationCheckEmail($check));

        $check->is_completed();
    }
}
