<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPApiField;

class SyncSapApiField implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sap_obj;

    protected $database;
    protected $username;
    protected $password;
    protected $input;
    protected $log_id;

    public function __construct($database, $username, $password, $input, $log_id = false)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->input = $input;
        $this->log_id = $log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_obj = new SAPApiField($this->database, $this->username, $this->password, $this->log_id);
        
        // Save Data in database
        $sap_obj->addApiFieldInDatabase($this->input);
    }
}
