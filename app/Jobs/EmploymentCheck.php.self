<?php

namespace App\Jobs;

use App\Models\Check;
use App\Models\Employment;

use \Carbon\Carbon;

// Notifications
use Notification;
use \App\Notifications\NewEmploymentHistoryCheckEmail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use DB;
use Crypt;

class EmploymentCheck implements ShouldQueue
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
		$profile = json_decode(Crypt::decrypt($check->profile->profile));
		$employment = Employment::where("check_id", $check->id)->first();
        
        Notification::route('mail', env('MAIL_TO_ADDRESS'))->notify(new NewEmploymentHistoryCheckEmail($check, $profile, $employment));

        $check->is_completed();
    }
}
