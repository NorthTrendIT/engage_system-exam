<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\SapConnection;

use App\Jobs\SyncOrders;
use App\Jobs\SyncQuotations;
use App\Jobs\SyncInvoices;
use App\Jobs\SyncCreditNote;
use App\Support\SAPCustomer;
use App\Support\SAPProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        $logFilePath = storage_path('logs/dataSync-failed.log');
        if (file_exists($logFilePath)) {
            
            $lines = file($logFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); // Read the log file
            $latestDate = end($lines); // Get the last non-empty line
            if ($latestDate) { 
                $this->reSyncRecords($latestDate, $logFilePath);
            } else { 
                echo 'No log dates found or log file does not exist.'; 
            }

         }


        $sap_connections = SapConnection::where('id', '!=', 5)->get();

        foreach($sap_connections as $value){
            SyncQuotations::dispatch($value->db_name, $value->user_name, $value->password);
            SyncOrders::dispatch($value->db_name, $value->user_name, $value->password);
            SyncInvoices::dispatch($value->db_name, $value->user_name, $value->password);
            SyncCreditNote::dispatch($value->db_name, $value->user_name, $value->password);
        }

        echo "Sync all module data to take from SAP successfully";
        return 0;
    }

    private function reSyncRecords($previousDate, $logFilePath){
        $currentDate = Carbon::now(); 
        $todaysDate = $currentDate->toDateString();
        
        $sap_connections = SapConnection::where('id', '!=', 5)->get();
        foreach($sap_connections as $value){

            $sap_customer = new SAPCustomer ($value->db_name, $value->user_name, $value->password, false, '');
            $cust_url = '/b1s/v1/BusinessPartners?$count=true&$filter=(UpdateDate ge \''.$previousDate.'\' and UpdateDate le \''.$todaysDate.'\') or (CreateDate ge \''.$previousDate.'\' and CreateDate le \''.$todaysDate.'\')';
            $sap_customer->addCustomerDataInDatabase($cust_url, $logFilePath); // intend to delete log file if it was successful from fetching api

            
            $sap_product = new SAPProduct ($value->db_name, $value->user_name, $value->password, false, '');
            $prod_url = '/b1s/v1/Items?$count=true&$filter=(UpdateDate ge \''.$previousDate.'\' and UpdateDate le \''.$todaysDate.'\') or (CreateDate ge \''.$previousDate.'\' and CreateDate le \''.$todaysDate.'\')';
            $sap_product->addProductDataInDatabase($prod_url);
        }
    }
}
