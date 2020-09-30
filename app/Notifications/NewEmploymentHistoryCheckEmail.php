<?php

namespace App\Notifications;

use App\Models\Check;
use App\Models\Employment;
use App\Models\Profile;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewEmploymentHistoryCheckEmail extends Notification
{
    use Queueable;

    protected $check;
	protected $profile;
	protected $employment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Check $check, $profile)
    {
        $this->check = $check;
		$this->profile = $profile;
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
    	$profile = $this->profile;
		
    	$first_name = $this->check->first_name;
		$middle_name = !is_null($this->check->middle_name) ? $this->check->middle_name : "";
		$last_name = $this->check->last_name;
		$dob = $this->profile->birthday;
		$ssn = $this->profile->ssn;
		
		$person = $first_name . " " . $middle_name . " " . $last_name . ", " . $dob . ", " . $ssn;
		
		$msg = (new MailMessage)
                    ->subject('New Employment History Check')
                    ->line($this->check->user->full_name.' has submitted a new employment history check on:')
					->line($person)
					->line(" ")
					->line("EMPLOYMENT INFO: ")
					->line(" ");
					
					
		if(isset($profile->current_employer_name)){
			$msg->line("current EMPLOYER")
			    ->line("  current Employer: " . $profile->current_employer_name);
		}
		
		if(isset($profile->current_employer_address)){
			$msg->line("  Address: " . $profile->current_employer_address);
		}
		
		if(isset($profile->current_employer_city)){
			$msg->line("  City: " . $profile->current_employer_city);
		}
		
		if(isset($profile->current_employer_state)){
			$msg->line("  State: " . $profile->current_employer_state);
		}
		
		if(isset($profile->current_employer_zip)){
			$msg->line("  Zip: " . $profile->current_employer_zip);
		}
		
		if(isset($profile->current_employer_phone)){
			$msg->line("  Phone: " . $profile->current_employer_phone);
		}
		
		if(isset($profile->current_job_title)){
			$msg->line("  Title: " . $profile->current_job_title);
		}
		
		if(isset($profile->current_hire_date)){
			$msg->line("  Hire Date: " . $profile->current_hire_date);
		}
					
		if(isset($profile->past_employer_name)){
			$msg->line("PAST EMPLOYER")
			    ->line("  Past Employer: " . $profile->past_employer_name);
		}
		
		if(isset($profile->past_employer_address)){
			$msg->line("  Address: " . $profile->past_employer_address);
		}
		
		if(isset($profile->past_employer_city)){
			$msg->line("  City: " . $profile->past_employer_city);
		}
		
		if(isset($profile->past_employer_state)){
			$msg->line("  State: " . $profile->past_employer_state);
		}
		
		if(isset($profile->past_employer_zip)){
			$msg->line("  Zip: " . $profile->past_employer_zip);
		}
		
		if(isset($profile->past_employer_phone)){
			$msg->line("  Phone: " . $profile->past_employer_phone);
		}
		
		if(isset($profile->past_job_title)){
			$msg->line("  Title: " . $profile->past_job_title);
		}
		
		if(isset($profile->past_hire_date)){
			$msg->line("  Hire Date: " . $profile->past_hire_date);
		}
		
		if(isset($profile->past_hire_date)){
			$msg->line("  End Date: " . $profile->past_end_date);
		}
		
		return $msg;
		
		
        /*
        return (new MailMessage)
                    ->subject('New Employment History Check')
                    ->line($this->check->user->full_name.' has submitted a new employment history check')
					->line("PERSONAL:")
					->line($person)
					->line(" ")
					->line("EMPLOYMENT INFO: ")
					->line("  Past Employer: " . $past_employer_name)
					->line("  Address: " . $past_employer_address)
					->line("  Phone: " . $past_employer_phone)
					->line("  Title: " . $past_job_title)
					->line("  Hire Date: " . $past_hire_date)
					->line(" ")
                    ->action('Details', secure_url('admin/checks/'.$this->check->id));
		 * */
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
}
