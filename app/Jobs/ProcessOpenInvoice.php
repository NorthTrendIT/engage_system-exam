<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Support\SAPInvoices;
use App\Support\SAPOrders;
use App\Models\Quotation;
use App\Models\SapConnection;
use App\Support\SAPQuotations;
use Illuminate\Support\Facades\Log;

class ProcessOpenInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sap_invoices;

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
        // $sap_quotations = new SAPQuotations($this->database, $this->username, $this->password, $this->log_id);
        // $sap_orders     = new SAPOrders($this->database, $this->username, $this->password, $this->log_id);
        $sap_invoices   = new SAPInvoices($this->database, $this->username, $this->password, $this->log_id);

        // $where = array(
        //     'db_name' => $this->database,
        //     'user_name' => $this->username,
        // );
        // $sap_connection = SapConnection::where($where)->first();

        // $quotations = Quotation::where('real_sap_connection_id', $sap_connection->id)->whereNotIn('status', ['Cancelled'])->get();
        
        $str = '';
        // $count = 0;
        // foreach($quotations as $q){
        //     $or   = ($count > 0) ? ' or' : ' and';
        //     $str .= $or.' U_OMSNo eq '.$q->doc_entry.'';

        //     // $sap_invoices->addSpecificInvoicesDataInDatabase($q->doc_entry, true, $sap_connection->id);
        //     $count++;
        // }
        
        $date_from = '2022-01-01';
        $date_to   = '2022-12-31';

        $quot_url = '/b1s/v1/Quotations?$filter=Cancelled eq \'tNO\' and CancelStatus eq \'csNo\' and CreationDate ge \''.$date_from.'\' and CreationDate le \''.$date_to.'\''.$str;
        $ord_url = '/b1s/v1/Orders?$filter=Cancelled eq \'tNO\' and CancelStatus eq \'csNo\' and CreationDate ge \''.$date_from.'\' and CreationDate le \''.$date_to.'\''.$str;
        $inv_url = '/b1s/v1/Invoices?$filter=Cancelled eq \'tNO\' and CancelStatus eq \'csNo\' and CreationDate ge \''.$date_from.'\' and CreationDate le \''.$date_to.'\''.$str;
        
        // $sap_quotations->addQuotationsDataInDatabase($quot_url);
        // $sap_orders->addOrdersDataInDatabase($ord_url);
        $sap_invoices->addInvoicesDataInDatabase($inv_url);
    }
}
