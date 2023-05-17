<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\VatGroup;
use Log;

class StoreVatGroups implements ShouldQueue
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
                                'code'              => @$value['Code'],
                                'name'              => @$value['Name'],
                                'category'          => @$value['Category'],
                                'tax_account'       => @$value['TaxAccount'], 
                                'eu'                => @$value['EU'],
                                'triangular_deal'   => @$value['TriangularDeal'],
                                'acquisition_reverse' => @$value['AcquisitionReverse'],
                                'non_deduct'        => @$value['NonDeduct'],
                                'acquisition_tax'   => @$value['AcquisitionTax'],
                                'goods_shipment'    => @$value['GoodsShipment'],
                                'non_deduct_acc'    => @$value['NonDeductAcc'],
                                'deferred_tax_acc'  => @$value['DeferredTaxAcc'],
                                'correction'        => @$value['Correction'],
                                'vat_correction'    => @$value['VatCorrection'],
                                'equalization_tax_account' => @$value['EqualizationTaxAccount'],
                                'service_supply'    => @$value['ServiceSupply'],
                                'inactive'          => @$value['Inactive'],
                                'tax_type_black_list' => @$value['TaxTypeBlackList'],
                                'report_349_code' => @$value['Report349Code'],
                                'vat_in_revenue_account' => @$value['VATInRevenueAccount'],
                                'down_payment_tax_offset_account' => @$value['DownPaymentTaxOffsetAccount'],
                                'cash_discount_account'  => @$value['CashDiscountAccount'],
                                'vat_deductible_account' => @$value['VATDeductibleAccount'],
                                'tax_region'             => @$value['TaxRegion'],
                                'vatgroups_lines'        => json_encode(@$value['VatGroups_Lines']),
                                'sap_connection_id'      => $this->sap_connection_id,
                            );

                VatGroup::updateOrCreate(
                            [
                                'code' => @$value['Code'],
                                'sap_connection_id' => $this->sap_connection_id,
                            ],
                            $insert
                        );

            }

        }
    }
}
