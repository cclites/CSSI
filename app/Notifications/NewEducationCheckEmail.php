<?php

namespace App\Notifications;

use App\Models\Check;
use App\Models\Profile;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewEducationCheckEmail extends Notification
{
    use Queueable;

    protected $check;

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
                    ->subject('New Education Check')
                    ->line($this->check->user->full_name.' has submitted a new education check on:')
					->line($person)
					->line(" ")
					->line("EDUCATION INFO: ")
					->line(" ");
					
					
		if(isset($profile->college_name)){
			$msg->line("---------------------------------------------------------");
			$msg->line("College: " . $profile->college_name);
		}
		
		if(isset($profile->college_city_and_state)){
			$msg->line("Address: " . $profile->college_city_and_state);
		}
		
		if(isset($profile->college_city)){
			$msg->line("City: " . $profile->college_city);
		}
		
		if(isset($profile->college_state)){
			$msg->line("State: " . $profile->college_state);
		}
		
		if(isset($profile->college_zip)){
			$msg->line("Zip: " . $profile->college_zip);
		}
		
		if(isset($profile->college_phone)){
			$msg->line("Phone: " . $profile->college_phone);
		}
		
		if(isset($profile->college_years_attended)){
			$msg->line("Years Attended: " . $profile->college_years_attended);
		}
		
		if(isset($profile->college_degree_type)){
			$msg->line("Degree: " . $profile->college_degree_type);
		}
		
		if(isset($profile->college_graduation_year)){
			$msg->line("Year Graduated: " . $profile->college_graduation_year);
		}
		
		if(isset($profile->high_school_name)){
			$msg->line("---------------------------------------------------------");
			$msg->line("high_school: " . $profile->high_school_name);
		}
		
		if(isset($profile->high_school_city_and_state)){
			$msg->line("Address: " . $profile->high_school_city_and_state);
		}
		
		if(isset($profile->high_school_city)){
			$msg->line("City: " . $profile->high_school_city);
		}
		
		if(isset($profile->high_school_state)){
			$msg->line("State: " . $profile->high_school_state);
		}
		
		if(isset($profile->high_school_zip)){
			$msg->line("Zip: " . $profile->high_school_zip);
		}
		
		if(isset($profile->high_school_phone)){
			$msg->line("Phone: " . $profile->high_school_phone);
		}
		
		if(isset($profile->high_school_years_attended)){
			$msg->line("Years Attended: " . $profile->high_school_years_attended);
		}
		
		if(isset($profile->high_school_degree_type)){
			$msg->line("Degree: " . $profile->high_school_degree_type);
		}
		
		if(isset($profile->high_school_graduation_year)){
			$msg->line("Year Graduated: " . $profile->high_school_graduation_year);
		}
		
		return $msg;
		
		
        /*
        return (new MailMessage)
                    ->subject('New Education Check')
                    ->line($this->check->user->full_name.' has submitted a new education check on:')
                    ->action('Details', secure_url('admin/checks/'.$this->check->id.'#education'));
		 */
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
