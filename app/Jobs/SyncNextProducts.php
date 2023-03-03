<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPProduct;


class SyncNextProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $sap_product;

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
        $this->next_url = $next_url;
        $this->log_id = $log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_product = new SAPProduct($this->database, $this->username, $this->password, $this->log_id);
        
        // Save Data of product in database
        $sap_product->addProductDataInDatabase($this->next_url);
    }
}
