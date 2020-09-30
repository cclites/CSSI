<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;



class BilledEmail extends Notification
{
    use Queueable;

    protected $amount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($amount)
    {
        $this->amount = $amount;
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
                    ->subject(env('APP_NAME').' Automatic Billing')
                    ->greeting('Hi '.$notifiable->first_name)
                    ->line('Your account has been automatically billed **$ ' . ($this->amount) . '**')
                    ->line('To view your billing history, click on the link below:')
                    ->action('Billing History', secure_url('transactions'))
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
