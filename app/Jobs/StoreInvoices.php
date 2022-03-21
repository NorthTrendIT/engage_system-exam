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

    public function __construct($data, $sap_connection_id)
    {
        $this->data = $data;
        $this->sap_connection_id = $sap_connection_id;
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
                            'sap_connection_id' => $this->sap_connection_id,
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
                            //'response' => json_encode($value),
                        );

                        $item_obj = InvoiceItem::updateOrCreate([
                                        'invoice_id' => $obj->id,
                                        'item_code' => @$value['ItemCode'],
                                    ],
                                    $item
                                );

                        if(!is_null(@$value['BaseEntry'])){
                            $obj->base_entry = @$value['BaseEntry'];
                            $obj->save();
                        }
                    }

                }
            }

        }
    }
}
