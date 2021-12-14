<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPQuotations;

class SyncQuotations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $sap_customer;

    protected $database;
    protected $username;
    protected $password;

    public function __construct($database, $username, $password)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_order = new SAPQuotations($this->database, $this->username, $this->password);

        // Save Data of order in database
        $sap_order->addQuotationsDataInDatabase();
    }
}
