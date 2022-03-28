<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CreditNote;
use App\Models\CreditNoteItem;

class StoreCreditNote implements ShouldQueue
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

            foreach ($this->data as $value) {

                $insert = array(
                            'doc_entry' => $value['DocEntry'],
                            'num_at_card' => $value['NumAtCard'],
                            'doc_num' => $value['DocNum'],
                            'doc_type' => $value['DocType'],
                            'doc_date' => $value['DocDate'],
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
                            'u_sostat' => $value['U_SOSTAT'],
                            'cancelled' => @$value['Cancelled'] == 'tYES' ? 'Yes' : 'No',
                            'cancel_date' => $value['CancelDate'],
                            'created_at' => $value['CreationDate'],
                            'updated_at' => $value['UpdateDate'],
                            'document_status' => $value['DocumentStatus'],
                            'comments' => $value['Comments'],

                            'updated_date' => $value['UpdateDate'],
                            'last_sync_at' => current_datetime(),
                            'sap_connection_id' => $this->sap_connection_id,
                        );

                if(!empty($value['DocumentLines'])){
                    array_push($insert, array('base_entry' => $value['DocumentLines'][0]['BaseEntry']));
                }

                $obj = CreditNote::updateOrCreate([
                                            'doc_entry' => $value['DocEntry'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );

                if(!empty($value['DocumentLines'])){

                    $items = @$value['DocumentLines'];

                    foreach($items as $item){
                        $item = array(
                            'credit_note_id' => $obj->id,
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

                            'sap_connection_id' => $this->sap_connection_id,
                        );

                        $item_obj = CreditNoteItem::updateOrCreate([
                                        'credit_note_id' => $obj->id,
                                        'item_code' => @$item['ItemCode'],
                                    ],
                                    $item
                                );

                        if(!is_null(@$item['BaseEntry'])){
                            $obj->base_entry = @$item['BaseEntry'];
                            $obj->save();
                        }
                    }

                }
            }

        }
    }
}
