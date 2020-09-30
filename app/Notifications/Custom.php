<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;



class Custom extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {

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
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('From: ' . env('APP_NAME').' OOPS!')
                    ->greeting('Hi ')
                    ->line('Please disregard the invoice that showed up in your email. The system was undergoing automated testing, and the emails were sent inadvertently.')
					->line('The invoice has id #999999.')
					->line('I apologize for any confusion. Your regular invoice will arrive on the first of the month.')
                    ->line('Chad');
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
