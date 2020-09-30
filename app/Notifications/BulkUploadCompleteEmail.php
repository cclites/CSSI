<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Models\Type;



class BulkUploadCompleteEmail extends Notification
{
    use Queueable;

	protected $type;
	protected $name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $name)
    {
		$this->type = $type;
		$this->name = $name;
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable){
    	
		$type = $this->type;
		$name = $this->name;
		
		$checkType = Type::find($type);
		
        return (new MailMessage)
                    ->subject(env('APP_NAME').' Bulk Upload completed.')
                    ->greeting('Hi '. $name)
                    ->line('Your ' . $checkType->title . ' bulk upload is complete. The records can be viewed in your account history.')
                    ->line('Thank you for your business!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
	
	/*
	public function routeNotificationForMail($notification)
    {
    	if($this->invoice){
    		return $this->invoice;
    	}else{
    		return $this->email;
    	}
        //return $this->email_address;
    }
	 * 
	 */
}
