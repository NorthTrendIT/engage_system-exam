<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;

class StoreProducts implements ShouldQueue
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

                                'item_code' => @$value['ItemCode'],
                                'item_name' => @$value['ItemName'],
                                'foreign_name' => @$value['ForeignName'],
                                'items_group_code' => @$value['ItemsGroupCode'],
                                'customs_group_code' => @$value['CustomsGroupCode'],
                                'sales_vat_group' => @$value['SalesVATGroup'],
                                'purchase_vat_group' => @$value['PurchaseVATGroup'],
                                'created_date' => @$value['CreateDate']." ".@$value['CreateTime'],
                                'is_active' => @$value['Valid'] == "tYES" ? true : false,
                                'item_prices' => json_encode(@$value['ItemPrices']),
                                //'response' => json_encode($value),

                                'u_mobil_sc' => @$value['U_MOBIL_SC'],
                                'u_item_type' => @$value['U_ItemType'],
                                'u_item_application' => @$value['U_ItemApplication'],
                                'u_pattern_type' => @$value['U_PatternType'],
                                'u_tire_size' => @$value['U_TireSize'],
                                'u_tire_diameter' => @$value['U_TireDiameter'],
                                'u_speed_symbol' => @$value['U_SpeedSymbol'],
                                'u_ply_rating' => @$value['U_PlyRating'],
                                'u_tire_const' => @$value['U_TireConst'],
                                'u_fitment_conf' => @$value['U_FitmentConf'],
                                'u_business_group' => @$value['U_BusinessGroup'],
                                'u_section_width' => @$value['U_SectionWidth'],
                                'u_series' => @$value['U_Series'],


                                'sap_connection_id' => $this->sap_connection_id,
                            );

                $obj = Product::updateOrCreate(
                                        [
                                            'item_code' => @$value['ItemCode'],
                                            'sap_connection_id' => $this->sap_connection_id,
                                        ],
                                        $insert
                                    );
            }

        }
    }
}
