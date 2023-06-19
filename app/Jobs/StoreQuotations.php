<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;

class StoreQuotations implements ShouldQueue
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

            foreach ($this->data as $value) {

                if($this->real_sap_connection_id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                    $customer = Customer::where('card_code', $value['CardCode'])->where('sap_connection_id', 5)->first();
                    if(!empty($customer)){
                        $this->sap_connection_id = 5;
                    }else{
                        $this->sap_connection_id = 1;
                    }
                }

                $insert = array(
                            'doc_entry' => $value['DocEntry'],
                            'doc_num' => $value['DocNum'],
                            'doc_type' => $value['DocType'],
                            'num_at_card' => $value['NumAtCard'],
                            'document_status' => $value['DocumentStatus'],
                            'doc_date' => $value['DocDate'],
                            'doc_time' => $value['DocTime'],
                            'doc_due_date' => $value['DocDueDate'],
                            'card_code' => $value['CardCode'],
                            'card_name' => $value['CardName'],
                            'address' => $value['Address'],
                            'doc_total' => $value['DocTotal'],
                            'doc_currency' => $value['DocCurrency'],
                            'journal_memo' => $value['JournalMemo'],
                            'payment_group_code' => $value['PaymentGroupCode'],
                            'sales_person_code' => (int)$value['SalesPersonCode'],
                            'u_brand' => $value['U_BRAND'],
                            'u_branch' => $value['U_BRANCH'],
                            'u_commitment' => @$value['U_COMMITMENT'],
                            'u_time' => $value['U_TIME'],
                            'u_posono' => $value['U_POSONO'],
                            'u_posodate' => $value['U_POSODATE'],
                            'u_posotime' => $value['U_POSOTIME'],
                            'u_remarks' => $value['U_REMARKS'],
                            'created_at' => $value['CreationDate'],
                            'updated_at' => $value['UpdateDate'],
                            'cancelled' => @$value['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                            'cancel_date' => $value['CancelDate'],
                            //'response' => json_encode($order),

                            'updated_date' => $value['UpdateDate'],
                            'last_sync_at' => current_datetime(),
                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                            'u_omsno' => @$value['U_OMSNo'],
                            'comments'=> @$value['Comments'],
                        );

                if(!empty($value['DocumentLines'])){
                    array_push($insert, array('base_entry' => $value['DocumentLines'][0]['BaseEntry']));
                }

                $obj = Quotation::updateOrCreate([
                                            'doc_entry' => $value['DocEntry'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );
                if($obj->cancelled === "Yes"){
                    Quotation::where('id', $obj->id)->update(['status' =>'Cancelled']);
                }

                if(!empty($value['DocumentLines'])){

                    $item_codes = [];
                    $quo_items = @$value['DocumentLines'];

                    foreach($quo_items as $item){
                        $fields = array(
                            'quotation_id' => $obj->id,
                            'line_num' => @$item['LineNum'],
                            'item_code' => @$item['ItemCode'],
                            'item_description' => @$item['ItemDescription'],
                            'quantity' => @$item['Quantity'],
                            'ship_date' => @$item['ShipDate'],
                            'price' => @$item['Price'],
                            'price_after_vat' => @$item['PriceAfterVAT'],
                            'currency' => @$item['Currency'],
                            'rate' => @$item['Rate'],
                            'discount_percent' => @$item['DiscountPercent'] != null ? @$item['DiscountPercent'] : 0.0,
                            'werehouse_code' => @$item['WarehouseCode'],
                            'sales_person_code' => @$item['SalesPersonCode'],
                            'gross_price' => @$item['GrossPrice'],
                            'gross_total' => @$item['GrossTotal'],
                            'gross_total_fc' => @$item['GrossTotalFC'],
                            'gross_total_sc' => @$item['GRossTotalSC'] != null ? @$item['GRossTotalSC'] : 0.0,
                            'ncm_code' => @$item['NCMCode'],
                            'ship_to_code' => @$item['ShipToCode'],
                            'ship_to_description' => @$item['ShipToDescription'],
                            'open_amount' => @$item['OpenAmount'],
                            'remaining_open_quantity' => @$item['RemainingOpenQuantity'],
                            //'response' => json_encode($item),

                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                            'line_status' => @$item['LineStatus'],
                            'u_itemstat' => @$item['U_ITEMSTAT'],

                        );

                        $item_obj = QuotationItem::updateOrCreate([
                                        'quotation_id' => $obj->id,
                                        'item_code' => @$item['ItemCode'],
                                    ],
                                    $fields
                                );

                        array_push($item_codes, @$item['ItemCode']);

                        if(!is_null(@$value['BaseEntry'])){
                            $obj->base_entry = @$value['BaseEntry'];
                            $obj->save();
                        }
                    }

                    QuotationItem::where('quotation_id', $obj->id)->whereNotIn('item_code', $item_codes)->delete();

                }
            }
        }
    }
}
