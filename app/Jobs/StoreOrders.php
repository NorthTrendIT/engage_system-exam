<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;

class StoreOrders implements ShouldQueue
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

            foreach ($this->data as $order) {

                if($this->real_sap_connection_id == 1){ // GROUP Cagayan, Davao NEED TO STORE in Solid Trend 
                    $customer = Customer::where('card_code', $order['CardCode'])->where('sap_connection_id', 5)->first();
                    if(!empty($customer)){
                        $this->sap_connection_id = 5;
                    }else{
                        $this->sap_connection_id = 1;
                    }
                }

                $insert = array(
                            'doc_entry' => $order['DocEntry'],
                            'num_at_card' => $order['NumAtCard'],
                            'doc_num' => $order['DocNum'],
                            'doc_type' => $order['DocType'],
                            'doc_date' => $order['DocDate'],
                            'doc_due_date' => $order['DocDueDate'],
                            'card_code' => $order['CardCode'],
                            'card_name' => $order['CardName'],
                            'address' => $order['Address'],
                            'doc_total' => $order['DocTotal'],
                            'doc_currency' => $order['DocCurrency'],
                            'journal_memo' => $order['JournalMemo'],
                            'payment_group_code' => $order['PaymentGroupCode'],
                            'sales_person_code' => (int)$order['SalesPersonCode'],
                            'u_brand' => $order['U_BRAND'],
                            'u_branch' => $order['U_BRANCH'],
                            'u_commitment' => @$order['U_COMMITMENT'],
                            'u_time' => $order['U_TIME'],
                            'u_posono' => $order['U_POSONO'],
                            'u_posodate' => $order['U_POSODATE'],
                            'u_posotime' => $order['U_POSOTIME'],
                            'u_sostat' => $order['U_SOSTAT'],
                            'cancelled' => @$order['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                            'cancel_date' => $order['CancelDate'],
                            'created_at' => $order['CreationDate'],
                            'updated_at' => $order['UpdateDate'],
                            'document_status' => $order['DocumentStatus'],
                            //'response' => json_encode($order),

                            'updated_date' => $order['UpdateDate'],
                            'last_sync_at' => current_datetime(),
                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                        );

                if(!empty($order['DocumentLines'])){
                    array_push($insert, array('base_entry' => $order['DocumentLines'][0]['BaseEntry']));
                }

                $obj = Order::updateOrCreate([
                                            'doc_entry' => $order['DocEntry'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );

                if(!empty($order['DocumentLines'])){

                    $order_items = @$order['DocumentLines'];

                    foreach($order_items as $value){
                        $item = array(
                            'order_id' => $obj->id,
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

                            'sap_connection_id' => $this->sap_connection_id,
                            'real_sap_connection_id' => $this->real_sap_connection_id,
                        );

                        $item_obj = OrderItem::updateOrCreate([
                                        'order_id' => $obj->id,
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
