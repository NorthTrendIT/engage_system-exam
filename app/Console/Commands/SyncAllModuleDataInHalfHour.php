<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\SapConnection;

use App\Jobs\SyncOrders;
use App\Jobs\SyncQuotations;
use App\Jobs\SyncInvoices;
use App\Jobs\SyncCreditNote;

class SyncAllModuleDataInHalfHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:all_module_data_in_half_hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all module data to take from SAP.';

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
        $sap_connections = SapConnection::all();

        foreach($sap_connections as $value){
            SyncOrders::dispatch($value->db_name, $value->user_name, $value->password);
            SyncQuotations::dispatch($value->db_name, $value->user_name, $value->password);
            SyncInvoices::dispatch($value->db_name, $value->user_name, $value->password);
            SyncCreditNote::dispatch($value->db_name, $value->user_name, $value->password);
        }

        echo "Sync all module data to take from SAP successfully";
        return 0;
    }
}
