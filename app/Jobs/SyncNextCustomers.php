<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPCustomer;

class SyncNextCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $sap_customer;
    
    protected $next_url;

    protected $database;
    protected $username;
    protected $password;
    protected $log_id;
    protected $search;

    public function __construct($database, $username, $password, $next_url, $log_id = false, $search)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;
        $this->search = $search;

        $this->next_url = $next_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_customer = new SAPCustomer($this->database, $this->username, $this->password, $this->log_id, $this->search);
        
        // Save Data of customer in database
        $sap_customer->addCustomerDataInDatabase($this->next_url);
    }
}
