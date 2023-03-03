<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Support\SAPCreditNote;

class SyncCreditNote implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $database;
    protected $username;
    protected $password;
    protected $log_id;
    

    public function __construct($database, $username, $password, $log_id = false)
    {
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->log_id  = $log_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sap_credit_note = new SAPCreditNote($this->database, $this->username, $this->password, $this->log_id);

        // Save Data of credit note in database
        $sap_credit_note->addCreditNoteDataInDatabase();
    }
}