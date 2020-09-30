<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Log;

class InsufficiencyEmail extends Notification{
	
	use Queueable;
	
	protected $recipient;
	protected $message;
	protected $applicant;
	
	public function __construct($recipient, $message, $applicant)
    {
        $this->recipient = $recipient;
		$this->message = $message;
		$this->applicant = $applicant;
		
    }
	
	public function via($notifiable)
    {
        return ['mail'];
    }
	
	public function toMail($notifiable)
    {
    	$recipient = $this->recipient;
		$message = $this->message;
		$applicant = $this->applicant;
		

        return (new MailMessage)
                    	->subject(env('APP_NAME').' Insufficiency Notification')
                    	->line('An insufficiency notice has been received for ' . $applicant)
                    	->line('MESSAGE: ' . $message);
						
    }
	
	
}