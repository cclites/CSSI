<?php

namespace App\Mail;

use App\Models\Check;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmploymentCheck extends Mailable
{
    use Queueable, SerializesModels;

    public $check;

    public function __construct(Check $check)
    {
        $this->check = $check;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.checks.employment');
    }
}
