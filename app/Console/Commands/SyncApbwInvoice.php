<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessOpenInvoice;
use App\Models\SapConnection;

class SyncApbwInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:apbw_inv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $value = SapConnection::where('company_name', 'APBW')->first();
        ProcessOpenInvoice::dispatch($value->db_name, $value->user_name, $value->password);

        echo "Sync all APBW invoices from SAP successfully";
        return 0;
    }
}
