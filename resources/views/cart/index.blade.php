@extends('layouts.master')

@section('title','My Cart')

@section('content')
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">My Cart</h1>
            </div>

            <div class="d-flex align-items-center py-1">
                <a href="{{ route('product-list.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <form method="post" id="myForm">
                @csrf
                <div class="row gy-5 g-xl-8 mt-5">
                    <!--begin::Col-->
                    <div class="col-xl-12">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-body pt-5 productSection">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="d-flex align-items-sm-center mb-7">
                                            <div class="flex-grow-1 me-2">
                                                <span class="text-muted fw-bold d-block fs-7">Customer name:</span>
                                                <span class="text-gray-800 fs-7 fw-bolder">{{@$customer->card_name}}  ({{@$customer->card_code}})</span>
                                                
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-sm-center mb-7">
                                            <div class="flex-grow-1 me-2">
                                                <span class="text-muted fw-bold d-block fs-7">Sales Specialist:</span>
                                                <span class="text-gray-800 fs-7 fw-bolder">{{@$sales_agent->sales_person->first_name}}  {{@$sales_agent->sales_person->last_name}}</span>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4">
                                        <div class="d-flex align-items-sm-center mb-7">
                                            <div class="flex-grow-1 me-2">
                                                <span class="fs-7 fw-bolder">Delivery Location :
                                                </span>
                                                <div class="form-group">
                                                    <select class="form-select form-select-solid" id='selectAddress' data-control="select2" data-hide-search="false" name="address_id" data-placeholder="Select address" data-allow-clear="true">
                                                        @if(!empty($address))
                                                            <option value=""></option>
                                                            @foreach($address as $item)
                                                            <option value="{{ $item->id }}" 
                                                                    
                                                                    data-address="{{ $item->address }}"
                                                                    data-street="{{ $item->street }}"
                                                                    data-zip_code="{{ $item->zip_code }}"
                                                                    data-city="{{ $item->city }}"
                                                                    data-state="{{ $item->state }}"
                                                                    data-country="{{ $item->country }}"

                                                                {{@$selected_address->id == $item->id ? 'selected' : ''}}>{{$item->address}}
                                                            </option>
                                                            @endforeach
                                                        @else
                                                            <option value="">No record found</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="align-items-sm-center mb-7 address_details_div" style="display:none;">
                                                    <div class="">
                                                        <div class="form-group">
                                                            <label class="">Address:</label><span class="text-muted address_span"></span><br>
                                                            <label class="mt-1">Street:</label><span class="text-muted street_span"></span><br>
                                                            <label class="mt-1">Zipcode:</label><span class="text-muted zip_code_span"></span><br>
                                                            <label class="mt-1">City:</label><span class="text-muted city_span"></span><br>
                                                            <label class="mt-1">State:</label><span class="text-muted state_span"></span><br>
                                                            <label class="mt-1">Country:</label><span class="text-muted country_span"></span><br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 me-2">
                                                    <div class="align-items-sm-center mb-7">
                                                        <div class="">
                                                            <div class="flex-grow-1 me-2">
                                                                <span class="fs-7 fw-bolder">Expected Delivery Date :</span>
                                                            </div>
                                                            <input type="text" class="form-control" placeholder="Delivery Date" id="kt_datepicker_1" name="due_date" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                  
                                    </div>
                                    <div>                                        
                                        
                                        <input type="button" name="deleteProduct" id="removeAll" value="Delete" class="btn btn-danger btn-sm" data-url="{{route('cart.removeall')}}">
                                    </div>
                                   
                                    <div class="col-xl-12">
                                        <?php $m= 0; ?>
                                        <table class="table cart-table" id="product_id_table">
                                            <thead>
                                                <tr>
                                                    <th style="display: none;"></th>
                                                    <th style="display: none;"></th>
                                                    <th><input type="checkbox" name="checkall" id="checkall"></th>
                                                    <th>#</th>
                                                    <th>Products (<span class="productCount">{{ count($data) }}</span>)</th>
                                                    <th>Quantity</th>
                                                    <th>Ordered Ltr/Kgs</th>
                                                    <th>Unit</th>
                                                    <th>Price</th>
                                                    <th>Amount</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                             @if(isset($data) && count($data) > 0)
                                            <tbody class="products_body">
                                                
                                                @foreach($data as $key=>$value)
                                                <?php $m++; ?>
                                                <tr id="tr_{{$m}}">
                                                    <td style="display: none"><span class="pr_weight">{{@$value->product->sales_unit_weight}}</span></td>
                                                    <td style="display: none"><span class="pr_volume">{{@$value->product->sales_unit_volume}}</span></td>
                                                    <td><input type="checkbox" name="remove_product" id="{{$value->id}}" data-url="{{ route('cart.removeall') }}" value="{{$value->id}}"></td>
                                                    <td>{{$key+1}}</td>
                                                    <td><span class="text-gray-800 fs-6 fw-bolder">{{ $value->product->item_name}}</span>
                                                        <span class="text-muted fw-bold d-block fs-7">CODE: {{ $value->product->item_code }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="button-wrap">
                                                            <div class="counter">
                                                                <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus" data-url="{{ route('cart.qty-minus', $value->id)}}">
                                                                    <i class="fas fa-minus"></i>
                                                                </a>

                                                                <input class="form-control qty text-end" data-url="{{ route('cart.update-qty', $value->id)}}" type="number" min="1" value="{{ $value->qty }}" >

                                                                <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus" data-url="{{ route('cart.qty-plus', $value->id)}}">
                                                                    <i class="fas fa-plus"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="individual_weight text-end">{{number_format_value(@$value->qty * @$value->product->sales_unit_weight)}}</span></td>
                                                    <td>{{@$value->product->sales_unit}}</td>
                                                    @php
                                                        $customer_id = explode(',', Auth::user()->multi_customer_id);
                                                        $customer_price_list_no = @get_customer_price_list_no_arr($customer_id)[@$value->product->sap_connection_id];
                                                    @endphp                    
                                                    <td class="text-end">
                                                        <span class="fw-bolder my-2 price">₱ {{ number_format_value(get_product_customer_price(@$value->product->item_prices,$customer_price_list_no) ) }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="fw-bolder my-2 price total_price">₱ {{ number_format_value(get_product_customer_price(@$value->product->item_prices,$customer_price_list_no) * $value->qty ) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="button-wrap">
                                                        <div>
                                                            <a href="javascript:;" class="remove-from-cart btn btn-danger btn-sm" data-url="{{ route('cart.remove',$value->id) }}">Delete</a>
                                                        </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><input type="button" name="addProduct" value="Add Product" class="btn btn-primary btn-sm addProduct"></td>
                                                    <td colspan="6"></td>
                                                </tr>
                                            </tbody>
                                            @else
                                            <tbody class="products_body">
                                                <tr>
                                                    <td colspan="10" style="font-size:15px;text-align:center;"> Cart is Empty</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><input type="button" name="addProduct" value="Add Product" class="btn btn-primary btn-sm addProduct"></td>
                                                    <td colspan="6"></td>
                                                </tr>
                                            </tbody>
                                            @endif
                                        </table>
                                    </div> 
                                     @if(isset($data) && count($data) > 0)
                                    <div class="separator separator-solid"></div>
                                    <br>
                                    <div class="d-flex flex-wrap justify-content-between remark_div">
                                        <div class="col-xl-4">
                                            <div class="align-items-sm-center mb-7">
                                                <div class="">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="fs-7 fw-bolder">Remarks :</span>
                                                    </div>
                                                    <textarea type="text" class="form-control" placeholder="Remark" id="remark" name="remark" autocomplete="off" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3">
                                            <div class="flex-grow-1 me-2 row">
                                                <span class="fs-8 col-xl-6">Total Weight:</span>
                                                <span class="fw-bolder fs-6 col-xl-6 weight_span">{{number_format_value(@$weight) ?? 0}} Kg</span>
                                            </div>  
                                            <div class="flex-grow-1 me-2 row">
                                                <span class="fs-8 col-xl-6">Total Volume:</span>
                                                <span class="fw-bolder fs-6 col-xl-6 volume_span">{{number_format_value(@$volume) ?? 0}}</span>
                                            </div>                                            
                                        </div>
                                        <div class="col-xl-4">                                            
                                            <div class="flex-grow-1 me-2 row mb-4">
                                                <span class="fs-8 col-xl-6">Total:</span>
                                                <span class="fw-bolder fs-6 col-xl-6 total_span">₱ {{ number_format_value($total) }}</span>
                                            </div>
                                            <div class="flex-grow-1 me-2">
                                               <p class="custom_note">Note: Prices may be subjected with discount. Final <br>amount of order will reflect on the actual invoice.</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>                            
                        </div>
                    </div>
                    <div class="place-button">
                        <button type="submit" href="javascript:;" class="placeOrder btn btn-dark" >PLACE ORDER</button>
                    </div>
                </div>
                
            </form>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title">Add Products</h4>
                      <button type="button" class="close add_product_close" data-bs-dismiss="modal">&times;</button>
                      
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5 mt-5">
                                <div class="input-icon">
                                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search product" name="filter_search1" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-3 mt-5">
                                <select class="form-control form-control-lg form-control-solid" name="filter_brand" id="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach($c_product_groups as $key)
                                    <option value="{{ $key->product_group->group_name }}">{{ $key->product_group->group_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-5">
                                <select class="form-control form-control-lg form-control-solid" name="filter_product_category" id="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach($c_product_category as $key => $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-5">
                                <select class="form-control form-control-lg form-control-solid" name="filter_product_line" id="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                                    <option value=""></option>
                                    @foreach($c_product_line as $key => $l)
                                    <option value="{{ $l->u_item_line }}">{{ @$l->u_item_line_sap_value->value ?? $l->u_item_line }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-5">
                              <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                              <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                            </div>
                        </div>
                        <div class="row mb-5 mt-5">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="table-responsive  modal-table-scroll">
                                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myProductTable">
                                          <thead>
                                            <tr>
                                              <th style="width:24px !important">No.</th>
                                              <th>Name</th>                                      
                                              <th>Brand</th>
                                              <th>Product Line</th>
                                              <th>Product Category</th>
                                              @if(userrole() != 2)
                                              <th>Price</th>
                                              @endif
                                              <th>Action</th>
                                            </tr>
                                          </thead>
                                          <tbody>

                                          </tbody>
                                       </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>      
            </div>
        </div>
    </div>
    <!--begin::Profile Personal Information-->

    <!--end::Profile Personal Information-->
@endsection

@push('css')
<!-- Place you css here -->
<link rel="stylesheet" href="{{ asset('assets') }}/assets/css/datepicker.css" class="href">
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<style>
    .button-wrap {
        display: flex;
        align-items: center;
    }
    .button-wrap .counter {
        margin-right: 22px;
        display: flex;
        align-items: center;
    }
    .button-wrap .counter i {
        color: black;
    }
    .button-wrap .counter a {
        color: black;
        background: #FAFAFB;
        border: 0.5px solid #E1E1FB !important;
        Width: 32px !important;
        Height: 32px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0;
        font-weight: bold;
    }
    .button-wrap input {
        color: black;
        background: #FAFAFB;
        border: 0.5px solid #E1E1FB !important;
        Width: 100px !important;
        Height: 32px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0;
        font-weight: bold;
    }
    .button-wrap .remove a {
        background-color: black;
        padding: 4px 25px 6px;
        color: white;
        text-transform: uppercase;
    }
    .shipping-card {
        color: black;
    }
    .shipping-card .detail {
        color: #92929D;
        font-size: 15px;
    }
    .shipping-card .card-header {
        border-bottom: 1px solid rgba(146, 146, 157, 0.31) !important;
    }
    .place-button {
        text-align: center;
        line-height: 5;
        margin-bottom: 30px;
    }
    .place-button a {
        background-color: black;
        padding: 15px 35px 17px;
        color: white;
    }
</style>
@endpush



@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script>
$(document).ready(function() {
   
    var cart_due_date = "{{@$cart_address->due_date}}";
    if(cart_due_date != "" && cart_due_date != "0000-00-00"){
        var split = cart_due_date.split("-");
        var final_date = split[2]+"/"+split[1]+"/"+split[0];
        $('[name="due_date"]').val(final_date);
    }
    

    var selected_address = '{!! json_encode($selected_address) !!}';
    var address = JSON.parse(selected_address);
    if(address == null){
        $('.address_span').text("");
        $('.street_span').text("");
        $('.zip_code_span').text("");
        $('.city_span').text("");
        $('.state_span').text("");
        $('.country_span').text("");

        $('.address_details_div').hide();
    }else{
        $("#selectAddress").val(address.id);
        $('.address_span').text(" " + address.address);
        $('.street_span').text(" " + address.street);
        $('.zip_code_span').text(" " + address.zip_code);
        $('.city_span').text(" " + address.city);
        $('.state_span').text(" " + address.state);
        $('.country_span').text(" " + address.country);

        $('.address_details_div').show();
    }
        
    @php
        $dates = @Auth::user()->customer_delivery_schedules->where('date','>',date("Y-m-d"));
        if(count($dates)){
            $dates = array_map( function ( $t ) {
                    return date('d/m/Y',strtotime($t));
                }, array_column( $dates->toArray(), 'date' ) );
        }
    @endphp
    @if(count($dates))
        var enableDays = {!! json_encode($dates) !!};
    @endif

    function formatDate(d) {
        var day = String(d.getDate())
        //add leading zero if day is is single digit
        if (day.length == 1)
            day = '0' + day
        var month = String((d.getMonth()+1))
        //add leading zero if month is is single digit
        if (month.length == 1)
            month = '0' + month
        return day + "/" + month + "/" + d.getFullYear()
    }


    $('[name="due_date"]').datepicker({
        format: 'dd/mm/yyyy',
        todayHighlight: true,
        orientation: "bottom left",
        startDate: "+3d",
        autoclose: true,

        @if(count($dates))
        beforeShowDay: function(date){
            if (enableDays.indexOf(formatDate(date)) < 0)
            return {
                enabled: false
            }
            else
            return {
                enabled: true
            }
        },
        @endif
    });

    $('.qty').change(function(event) {
        $value = $(this).val();
        $url = $(this).attr('data-url');
        $self = $(this);
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                _token:'{{ csrf_token() }}',
                qty: $value,
            }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
                $self.val(1);
            }else{
                ($self.parent().parent().closest('tr').find('.total_price')).text('₱ '+result.price);
                $(".total_span").html(result.total);
                $(".weight_span").html(result.weight);
                $(".volume_span").html(result.volume);
                $(this).parent().eq( 8 ).find('.total_price').text(result.price);
                ($self.parent().parent().closest('tr').find('.individual_weight')).text(result.weight_individual);
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $(document).on('click', '.remove-from-cart', function(event) {
        event.preventDefault();
        var $self = $(this); 
        $url = $(this).attr('data-url');
        $self = $(this);

        Swal.fire({
            title: 'Are you sure want to Remove this product?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Remove!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $url,
                    method: "POST",
                    data: {
                            _token:'{{ csrf_token() }}'
                        }
                })
                .done(function(result) {
                    if(result.status == false){
                        toast_error(result.message);
                        hide_loader();
                    }else{
                        toast_success(result.message);
                        $(this).closest("tr").remove();
                        if(result.count > 0){
                            $(".total_span").html(result.total);
                            $(".weight_span").html(result.weight);
                            $(".volume_span").html(result.volume);
                            $self.closest("tr").remove();
                            $('.productCount').html('Products ('+result.count+')');
                            $('.cartCount').html(result.count);
                        } else {
                            $self.closest("tr").remove();
                            $('.remark_div').attr("style", "display: none !important");
                            $('.products_body').html('<tr><td colspan="10" style="font-size:15px;text-align:center;"> Cart is Empty</td></tr>');
                            window.location.reload();
                        }
                        hide_loader();
                        // setTimeout(function(){
                        //     window.location.reload();
                        // },1500)
                    }
                })
                .fail(function() {
                    toast_error("error");
                    hide_loader();
                });
            }
        })
    });

    $(document).on('click', '.qtyPlus', function(event) {
        $url = $(this).attr('data-url');
        $qty = parseInt($(this).parent().find('.qty').val());
        $self = $(this);
        $(this).parent().find('.qty').val($qty + 1);
        $this = $(this);
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                _token:'{{ csrf_token() }}',
            }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
                $this.parent().find('.qty').val($qty);
            }else{
                ($self.parent().parent().closest('tr').find('.total_price')).text('₱ '+result.price);
                $(".total_span").html(result.total);
                $(".weight_span").html(result.weight);
                $(".volume_span").html(result.volume);
                $(this).parent().eq( 8 ).find('.total_price').text(result.price);
                ($self.parent().parent().closest('tr').find('.individual_weight')).text(result.weight_individual);
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $(document).on('click', '.qtyMinus', function(event) {
        $url = $(this).attr('data-url');
        $qty = parseInt($(this).parent().find('.qty').val());
        $self = $(this);
        $(this).parent().find('.qty').val($qty - 1);
        $.ajax({
            url: $url,
            method: "POST",
            data: {
                _token:'{{ csrf_token() }}',
            }
        })
        .done(function(result) { 
            if(result.status == false){
                toast_error(result.message);
            }else{
                //toast_success(result.message);
                if(parseInt($self.parent().find('.qty').val()) <= 0){
                    $self.closest('.productSection').remove();
                }
                if(result.cart_count > 0){
                    ($self.parent().parent().closest('tr').find('.total_price')).text('₱ '+result.price);
                    $(".total_span").html(result.total);
                    $(".weight_span").html(result.weight);
                    $(".volume_span").html(result.volume);
                    $('.productCount').html('Products ('+result.cart_count+')');
                    $self.parent().parent().closest('tr').find('.individual_weight').text(result.weight_individual);
                    if(result.cart_count > 0){
                        $('.cartCount').show();
                        $('.cartCount').html(result.cart_count);
                    }
                } else {
                    $('.emptyCart').show();
                    $('#myForm').remove();
                    $('.cartCount').hide();
                }
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $('body').on("click", ".placeOrder", function (e) {
        e.preventDefault();
        var validator = validate_form();
        if (validator.form() != true) {
            $('[type="submit"]').prop('disabled', false);
        }else{
            Swal.fire({
            title: 'Do want to proceed with the order?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
          }).then((result) => {
            if (result.isConfirmed) {
                show_loader();
                $('[type="submit"]').prop('disabled', true);
                $.ajax({
                    url: "{{route('cart.placeOrder')}}",
                    type: "POST",
                    data: new FormData($("#myForm")[0]),
                    //async: true,
                    processData: false,
                    contentType: false,                
                    success: function (data) {
                        if (data.status) {
                            toast_success(data.message)
                            setTimeout(function(){
                                window.location.href = "{{ route('orders.index') }}";
                            },1500);
                        } else {
                            toast_error(data.message);
                            $('[type="submit"]').prop('disabled', false);
                        }
                    },
                    error: function () {
                        toast_error("Something went to wrong !");
                        $('[type="submit"]').prop('disabled', false);
                    },
                });
            }
          });
            
        }
    });

    function validate_form(){
        var validator = $("#myForm").validate({
            errorClass: "is-invalid",
            validClass: "is-valid",
            rules: {
                customer_id:{
                    required: true,
                },
                address_id:{
                    required: true,
                },
                due_date:{
                    required: true,
                },
                product_id:{
                    required: true,
                },
                quantity:{
                    required: true,
                    min: 1
                }
            },
            messages: {
                customer_id:{
                    required: "Please select customer.",
                },
                address_id:{
                    required: "Please select address.",
                },
                due_date:{
                    required: "Please select delivery date.",
                },
                product_id:{
                    required: "Please select product.",
                },
                quantity:{
                    required: "Please enter quantity.",
                    min: "Quentity must be grater than Zero."
                }
            }
        });

        $(".selectProducts").each(function() {
            $(this).rules('add', {
                required: true,
            });
        });

        $(".quantity").each(function() {
            $(this).rules('add', {
                required: true,
                min: 1,
            });
        });
        return validator;
    }

    $("body").on("change", '[name="address_id"]', function (e) {
        e.preventDefault();
        var option = $(this).find('option:selected');

        if($(this).find('option:selected').val() != ""){
            $('.address_span').text(" " + option.data('address'));
            $('.street_span').text(" " + option.data('street'));
            $('.zip_code_span').text(" " + option.data('zip_code'));
            $('.city_span').text(" " + option.data('city'));
            $('.state_span').text(" " + option.data('state'));
            $('.country_span').text(" " + option.data('country'));

            $('.address_details_div').show();
        }else{
            $('.address_span').text("");
            $('.street_span').text("");
            $('.zip_code_span').text("");
            $('.city_span').text("");
            $('.state_span').text("");
            $('.country_span').text("");

            $('.address_details_div').hide();
        }
    });
   
    
    $(".addProduct").on("click",function(){
        $("#myModal").modal('toggle');                
    });

    render_table();

    function render_table(){
      var table = $("#myProductTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_product"]').val();
      $filter_search1 = $('[name="filter_search1"]').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          order: [],
          ajax: {
              'url': "{{ route('cart.get.product.list') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_search1 : $filter_search1,
                filter_brand : $filter_brand,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_name', name: 'item_name'},              
              {data: 'brand', name: 'brand'},
              {data: 'u_item_line', name: 'u_item_line'},
              {data: 'u_tires', name: 'u_tires'},
              @if(userrole() != 2)
              {data: 'price', name: 'price', orderable:false,searchable:false},
              @endif
              {data: 'action', name: 'action', orderable:false,searchable:false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');
              })
          },
          rowCallback: function( row, data, index ) {
              var split_price = (data['price']).split(' ');
              var price = split_price[1].split('.');
              if (price[0] <= 0) {
                  $(row).hide();
              }
          },  
          initComplete: function () {
          }
        });
    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })
    
    //cartTotal();

    function cartTotal(){
        var sum = 0;
        var weight = 0;
        var volume = 0;
        $( "#product_id_table tbody tr ").each( function( index ) {
            var str = $(this).children().eq( 9 ).text(); 
            if(str==""){
                
            }else{
                str = str.replace('₱ ', '');
                str = str.replace(',', '');
                str = str.trim();      
                sum += parseFloat(str);

                var str1 = $(this).children().eq( 0 ).text(); 
                var str2 = $(this).children().eq( 5 ).find('.qty').val();
                var str3 = $(this).children().eq( 1 ).text();

                weight = weight + parseFloat(str1) * parseFloat(str2);
                volume = volume + parseFloat(str2) * parseFloat(str3);
            }                       
            
        });

        if(sum.includes(".")){
            sum = sum.toString().split(".");
            sum[0] = sum[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            sum[1] = sum[1].substring(0, 2);
            sum = sum.join(".");
        }else{
            sum = sum + ".00";
        }        
        $(".total_span").html(sum);

        if(weight.indexOf(".") > -1){
            weight = weight.toString().split(".");
            weight[0] = weight[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            weight[1] = weight[1].substring(0, 2);
            weight = weight.join(".");
        }else{
            weight = weight + ".00";
        }
        $(".weight_span").html(weight);

        volume = volume.toString().split(".");
        volume[0] = volume[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        volume[1] = volume[1].substring(0, 2);
        volume = volume.join(".");
        $(".volume_span").html(volume);
    }

    $("#checkall").click(function() {
      $("input[type=checkbox]").prop("checked", $(this).prop("checked"));
    });

    $("input[type=checkbox]").click(function() {
      if (!$(this).prop("checked")) {
        $("#checkall").prop("checked", false);
      }
    });

    $(document).on('click', '#removeAll', function(event) {
        event.preventDefault();
        var chk =[];
        $("input:checkbox[name=remove_product]:checked").each(function(){
            chk.push($(this).val());
        });
        var $self = $(this); 
        $url = $(this).attr('data-url');
        $self = $(this);

        Swal.fire({
            title: 'Are you sure want to Remove this product?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Remove!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $url,
                    method: "POST",
                    data: {
                            _token:'{{ csrf_token() }}',
                            chk : chk,
                        }
                })
                .done(function(result) {
                    if(result.status == false){
                        toast_error(result.message);
                        hide_loader();
                    }else{
                        toast_success(result.message);
                        $(this).closest("tr").remove();
                        if(result.count > 0){
                            $(".total_span").html(result.total);
                            $(".weight_span").html(result.weight);
                            $(".volume_span").html(result.volume);
                            $self.closest("tr").remove();
                            $('.productCount').html('Products ('+result.count+')');
                            $('.cartCount').html(result.count);
                        } else {
                            $self.closest("tr").remove();
                            $('.remark_div').attr("style", "display: none !important");
                            $('.products_body').html('<tr><td colspan="10" style="font-size:15px;text-align:center;"> Cart is Empty</td></tr>');
                            window.location.reload();
                        }
                        hide_loader();
                        // setTimeout(function(){
                        //     window.location.reload();
                        // },1500)
                    }
                })
                .fail(function() {
                    toast_error("error");
                    hide_loader();
                });
            }
        })
    });

    $(".filter_product1").select2();

    $(document).on("change",".filter_product1",function(){
        addToCart();
    });

    $(document).on("keyup",".addquantity",function(){
        addToCart();
    });

    function addToCart(){
        var product = $(".filter_product1").val();
        var quantity = $(".addquantity").val();
        var url = "{{ route('cart.add', ['id' => ":product"]) }}";
        url = url.replace(":product", product);
        if(product != "" && quantity != ""){
            $.ajax({
                url: url,
                method: "POST",
                data: {
                    _token:'{{ csrf_token() }}',
                    id: product, 
                    quantity: quantity,
                    flag: 'Addproduct',
                }
            })
            .done(function(result) {
                window.location.reload();
                
            })
            .fail(function() {
                toast_error("error");
            });

        }
    }

    @if(userdepartment() != 1)
        $(document).on('click', '.addToCart', function(event) {
          event.preventDefault();
          $url = $(this).attr('data-url');
          var address = $("#selectAddress").val();
          var due_date = $("#kt_datepicker_1").val();
          $addToCartBtn = $(this);
          $goToCartBtn = $(this).parent().find('.goToCart');
            $.ajax({
                url: $url,
                method: "POST",
                data: {
                        _token:'{{ csrf_token() }}',
                        address: address,
                        due_date:due_date,
                        qty:1,
                    }
                })
                .done(function(result) {
                    if(result.status == false){
                        toast_error(result.message);
                    }else{
                        $addToCartBtn.hide();
                        $goToCartBtn.show();
                        if(result.count > 0){
                            $('.cartCount').show();
                            $('.cartCount').html(result.count);
                        }
                        toast_success(result.message);
                        window.location.reload();
                    }
                })
                .fail(function() {
                    toast_error("error");
                });
        });
    @endif
});
</script>
@endpush
