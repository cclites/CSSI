<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Log;

class EmployeeScreenEmail extends Notification
{
    use Queueable;
	
	protected $checkRequestId;
	protected $companyName;
	protected $companyEmail;

    public function __construct($checkRequestId, $companyName, $companyEmail)
    {
       $this->checkRequestId = $checkRequestId;
	   $this->companyName = $companyName;
	   $this->companyEmail = $companyEmail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
	
	public function toMail($notifiable)
    {
    	$companyName = $this->companyName;
		$checkRequestId = $this->checkRequestId;
		$companyEmail = $this->companyEmail;
		
		$params = "?id=$checkRequestId";
		
		//$params = "?token=$token&key=$key&id=$checkRequestId";
		
        $message =  (new MailMessage)
	            ->from($companyEmail)
                ->subject('Background Check for ' . $companyName)
				->line('A background check has been requested by ' . $companyName . '.')
				->line('Click on the link below to start the background check process.')
                //->action('Begin Check', secure_url('company/screen/view' . $params))
                ->action('Begin Check', secure_url('company/screen/start' . $params))
                ->line('If you need any help or if you have any other questions, contact ' . $companyEmail)
				/*->markdown('mail::whitelabel', []);*/
				->markdown('vendor.notifications.whitelabel', []);
				
		//Log::info(json_encode($message));
		return $message;
					
		//return view('vendor/mail/html/whitelabel', ['slot'=>$message]);
    }
	
}

?>