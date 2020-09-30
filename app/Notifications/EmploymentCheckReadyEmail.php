<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Log;

class EmploymentCheckReadyEmail extends Notification
{
    use Queueable;
	
	protected $reportId;
	
	public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }
	
	public function via($notifiable){
        return ['mail'];
    }
	
	public function toMail($notifiable){
		
		return (new MailMessage)
                    ->subject(env('APP_NAME').' A new employment validation is ready')
                    ->greeting('Hi '. $notifiable->first_name)
                    ->line('A new report is ready for you to view.')
                    ->line('To view your report, click on the link below:')
                    ->action('View Report', secure_url('checks/' . $this->reportId))
					->line('')
                    ->line('Thank you for your business!');
		
		
	}
	
}