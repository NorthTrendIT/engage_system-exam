<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendedProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'products'
    ];

    public function assignments(){
        return $this->hasMany(RecommendedProductAssignment::class, 'assignment_id', 'id');
    }

    public function items(){
        return $this->hasMany(RecommendedProductItem::class, 'assignment_id', 'id');
    }

    public function sap_connection()
    {
        return $this->belongsTo(SapConnection::class,'b_unit');
    }
    
    public function getCustomerClassAttribute(){
        $ids = explode(',', $this->ids);
        $data = Classes::whereIn('id', $ids)->where('module', 'C')->get();

        return $data;
    }

    public function getSalesSpecialistAttribute(){
        $ids = explode(',', $this->ids);
        $data = User::orderby('sales_specialist_name','asc')->select('id','sales_specialist_name')->where(['role_id' => 14, 'is_active' => true])->whereIn('id', $ids)->get();

        return $data;
    }
    
    public function getBrandsAttribute(){
        $ids = explode(',', $this->ids);
        $data = ProductGroup::whereNotIn('group_name', ['Items', 'MKTG. MATERIALS', 'OFFICIAL DOCUMENT'])
                                ->whereIn('id', $ids)
                                ->where('is_active', true)
                                ->select('id','group_name')
                                ->orderby('group_name','asc')
                                ->get();
        
        return $data;
    }

    public function getTerritoriesAttribute(){
        $ids = explode(',', $this->ids);
        $data = Territory::whereIn('id', $ids)->where('territory_id','!=','-2')->where('is_active',true)->orderBy('description','asc')->get();

        return $data;
    }

    public function getMarketSectorAttribute(){
        $ids = explode(',', $this->ids);
        $data = Customer::whereIn('u_sector', $ids)->orderby('u_sector','asc')
                                ->select('u_sector')->groupBy('u_sector')->get();
        
        return $data;
    }

}
