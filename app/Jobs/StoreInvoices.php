<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;

class StoreInvoices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;

    protected $sap_connection_id;
    protected $real_sap_connection_id;

    public function __construct($data, $sap_connection_id)
    {
        $this->data = $data;
        $this->sap_connection_id = $sap_connection_id;
        $this->real_sap_connection_id = $sap_connection_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!empty($this->data)){

            foreach ($this->data as $invoice) {

                if($this->real_sap_connection_id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                    $customer = Customer::where('card_code', $invoice['CardCode'])->where('sap_connection_id', 5)->first();
                    if(!empty($customer)){
                        $this->sap_connection_id = 5;
                    }else{
                        $this->sap_connection_id = 1;
                    }
                }

                $insert = array(
                            'doc_entry' => $invoice['DocEntry'],
                            'num_at_card' => $invoice['NumAtCard'],
                            'doc_num' => $invoice['DocNum'],
                            'doc_type' => $invoice['DocType'],
                            'doc_date' => $invoice['DocDate'],
                            'doc_due_date' => $invoice['DocDueDate'],
                            'card_code' => $invoice['CardCode'],
                            'card_name' => $invoice['CardName'],
                            'address' => $invoice['Address'],
                            'doc_total' => $invoice['DocTotal'],
                            'doc_currency' => $invoice['DocCurrency'],
                            'journal_memo' => $invoice['JournalMemo'],
                            'payment_group_code' => $invoice['PaymentGroupCode'],
                            'sales_person_code' => (int)$invoice['SalesPersonCode'],
                            'u_brand' => $invoice['U_BRAND'],
                            'u_branch' => $invoice['U_BRANCH'],
                            'u_commitment' => @$invoice['U_COMMITMENT'],
                            'u_time' => $invoice['U_TIME'],
                            'u_posono' => $invoice['U_POSONO'],
                            'u_posodate' => $invoice['U_POSODATE'],
                            'u_posotime' => $invoice['U_POSOTIME'],
                            'u_sostat' => $invoice['U_SOSTAT'],
                            'created_at' => $invoice['CreationDate'],
                            'updated_at' => $invoice['UpdateDate'],
                            'document_status' => $invoice['DocumentStatus'],
                            'cancelled' => @$invoice['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                            //'response' => json_encode($invoice),

                            'updated_date' => $invoice['UpdateDate'],
                            'end_delivery_date' => $invoice['EndDeliveryDate'],
                            'u_delivery' => $invoice['U_DELIVERY'],
                            'last_sync_at' => current_datetime(),
                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                        );

                if(!empty($invoice['DocumentLines'])){
                    array_push($insert, array('base_entry' => $invoice['DocumentLines'][0]['BaseEntry']));
                }

                $obj = Invoice::updateOrCreate([
                                            'doc_entry' => @$invoice['DocEntry'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );

                if(!empty($invoice['DocumentLines'])){

                    $item_codes = [];
                    $invoice_items = @$invoice['DocumentLines'];

                    foreach($invoice_items as $value){
                        $item = array(
                            'invoice_id' => $obj->id,
                            'line_num' => @$value['LineNum'],
                            'item_code' => @$value['ItemCode'],
                            'item_description' => @$value['ItemDescription'],
                            'quantity' => @$value['Quantity'],
                            'ship_date' => @$value['ShipDate'],
                            'price' => @$value['Price'],
                            'price_after_vat' => @$value['PriceAfterVAT'],
                            'currency' => @$value['Currency'],
                            'rate' => @$value['Rate'],
                            'discount_percent' => @$value['DiscountPercent'] != null ? @$value['DiscountPercent'] : 0.0,
                            'werehouse_code' => @$value['WarehouseCode'],
                            'sales_person_code' => @$value['SalesPersonCode'],
                            'gross_price' => @$value['GrossPrice'],
                            'gross_total' => @$value['GrossTotal'],
                            'gross_total_fc' => @$value['GrossTotalFC'],
                            'gross_total_sc' => @$value['GRossTotalSC'] != null ? @$value['GRossTotalSC'] : 0.0,
                            'ncm_code' => @$value['NCMCode'],
                            'ship_to_code' => @$value['ShipToCode'],
                            'ship_to_description' => @$value['ShipToDescription'],
                            'open_amount' => @$value['OpenAmount'],
                            'remaining_open_quantity' => @$value['RemainingOpenQuantity'],
                            //'response' => json_encode($value),

                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                        );

                        $item_obj = InvoiceItem::updateOrCreate([
                                        'invoice_id' => $obj->id,
                                        'item_code' => @$value['ItemCode'],
                                    ],
                                    $item
                                );

                        array_push($item_codes, @$value['ItemCode']);

                        if(!is_null(@$value['BaseEntry'])){
                            $obj->base_entry = @$value['BaseEntry'];
                            $obj->save();
                        }
                    }

                    InvoiceItem::where('invoice_id', $obj->id)->whereNotIn('item_code', $item_codes)->delete();

                }
            }

        }
    }
}
