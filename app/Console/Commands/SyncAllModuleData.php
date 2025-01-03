<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SapConnection;

use App\Jobs\SyncCustomers;
use App\Jobs\SyncCustomerGroups;
use App\Jobs\SyncProductGroups;
use App\Jobs\SyncProducts;
use App\Jobs\SyncOrders;
use App\Jobs\SyncQuotations;
use App\Jobs\SyncInvoices;
use App\Jobs\SyncSalesPersons;
use App\Jobs\SyncTerritories;
use App\Support\SAPTestAPI;
use Illuminate\Support\Facades\Log;


class SyncAllModuleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:allmoduledata';

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
        $data = SapConnection::where('id', '!=', 5)->firstOrFail();

        $testAPI = new SAPTestAPI($data->db_name, $data->user_name, $data->password);
        $testAPI->checkLogin(true);

        $sap_connections = SapConnection::where('id', '!=', 5)->get();

        foreach($sap_connections as $value){
            SyncCustomers::dispatch($value->db_name, $value->user_name, $value->password);
            SyncCustomerGroups::dispatch($value->db_name, $value->user_name, $value->password);

            SyncProductGroups::dispatch($value->db_name, $value->user_name, $value->password);
            SyncProducts::dispatch($value->db_name, $value->user_name, $value->password);

            // SyncOrders::dispatch($value->db_name, $value->user_name, $value->password);
            // SyncQuotations::dispatch($value->db_name, $value->user_name, $value->password);
            // SyncInvoices::dispatch($value->db_name, $value->user_name, $value->password);

            // SyncSalesPersons::dispatch($value->db_name, $value->user_name, $value->password);
            SyncTerritories::dispatch($value->db_name, $value->user_name, $value->password);
        }
        
        Log::channel('midnight-sync')->info('midnight sync has been made!');
        echo "Sync all module data to take from SAP successfully";
        return 0;
    }
}
