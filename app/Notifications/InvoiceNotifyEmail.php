<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use Log;

class InvoiceNotifyEmail extends Notification
{
    use Queueable;

    protected $invoiceId;
	protected $amount;
	protected $attachment;
	protected $companyName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoiceId, $amount, $attachment, $companyName)
    {
    	Log::info("Loaded InvoiceNotifyEmail");
		
        $this->invoiceId = $invoiceId;
		$this->amount = $amount;
		$this->attachment = $attachment;
		$this->companyName = $companyName;
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
                    ->line('Your account has been automatically invoiced for **$ ' . ($this->amount) . '**')
					->line("If you have a payment method on file, it will be charged in 24-48 hours.")
                    ->line('Thank you for your business!')
					->attach($this->attachment);
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
