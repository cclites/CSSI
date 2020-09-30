<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Models
use App\Models\User;

// Notifications
use \App\Notifications\CreditCardExpirationEmail;

use Carbon\Carbon;

use Stripe\Charge;
use Stripe\Error\Card;
use Stripe\Error\InvalidRequest;
use Stripe\Error\ApiConnection;
use Stripe\Error\Base;
use Exception;



class NotifyCreditCardExpiration extends Command
{

    protected $signature = 'notify_credit_card_expiration';

    
    protected $description = 'Notify users of credit cards that may be expiring soon';

    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function handle()
    {
        $this->info($this->description);
        
        // Need to loop through all uses because the card expiration is not stored as a standard date
        $users = User::all();

        foreach ($users as $user) {            
            if (!$user->card_expiration) {
                $this->line('User ID: '.$user->id.' - No expiration date specified');
            }
            elseif (Carbon::createFromFormat('m/Y', $user->card_expiration) < Carbon::now()) {
                $this->info('User ID: '.$user->id.' - Already Expired '.$user->card_expiration);
            }
            elseif (Carbon::createFromFormat('m/Y', $user->card_expiration) < Carbon::now()->addMonths(3) ) {
                $this->info('User ID: '.$user->id.' - Set to expire '.$user->card_expiration.'. Notification sent to '.$user->email);
                $user->notify(new CreditCardExpirationEmail());
            }
            else {
                $this->line('User ID: '.$user->id.' - Set to expire '.$user->card_expiration.' - Looks good');
            } 
        }

    }
}
