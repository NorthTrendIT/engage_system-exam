<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Support\SAPQuotations;

class SyncNextQuotations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sap_quotations;
    
    protected $next_url;

    protected $database;
    protected $username;
    protected $password;
    protected $log_id;

    public function __construct($database, $username, $password, $next_url, $log_id = false)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id = $log_id;

        $this->next_url = $next_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_quotations = new SAPQuotations($this->database, $this->username, $this->password, $this->log_id);
        
        // Save Data of Quotations in database
        $sap_quotations->addQuotationsDataInDatabase($this->next_url);
    }
}
