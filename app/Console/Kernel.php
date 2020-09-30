<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        
        // Billing-Related
        Commands\Bill::class,
        Commands\Charge::class,
        Commands\GenerateInvoices::class,
        Commands\NotifyCreditCardExpiration::class,
        Commands\RemoveChecks::class,
        Commands\SecuritecCheckOrder::class,
        Commands\SambaCheckOrder::class,
        Commands\SambaCheckTestOrder::class,
        Commands\TidyLogs::class,
        
		//Commands\decodeReport::class,
		//Commands\checkOffense::class,
		
		//Commands\Neeyamo::class,
		//Commands\CustomReport::class,
		//Commands\TestInvoices::class,
		Commands\BulkNationalTriEye::class,
		
		Commands\ChecksTest::class,
		
		Commands\Models\convert_checks_to_order::class,
		Commands\Models\create_checks_from_orders::class,
		Commands\Models\create_company_from_rep::class,
		Commands\Models\add_prices_to_company::class,
		Commands\Models\clear_converted_data::class,
   
    ];

    protected function schedule(Schedule $schedule)
    {
    	
        $schedule->command('logs:tidy')
                ->dailyAt('03:00');
				
		$schedule->command('removeChecks')
                ->dailyAt('12:00');
        
        $schedule->command('bill')
                ->hourly();
               
        //$schedule->command('invoice')
                //->monthlyOn(1, '12:00');
				 
		//$schedule->command('charge')
                 //->monthlyOn(3, '12:00');
		
        $schedule->command('notify_credit_card_expiration')
                 ->monthly();
		
		$schedule->command("securitec_checks")
				 ->hourly();
				 //->cron('*/4 * * * *');
				
		$schedule->command("samba_checks")
				->cron('*/2 * * * *');
				
		$schedule->command("samba_test_checks")
				->cron('*/5 * * * *');
				
		$schedule->command("BulkNationalTriEye")
				->cron('*/1 * * * *');
        
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
