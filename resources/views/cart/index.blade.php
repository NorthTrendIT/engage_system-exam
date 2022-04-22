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
            @if(isset($data) && count($data) > 0)
            <form method="post" id="myForm">
                @csrf
                <div class="row gy-5 g-xl-8 mt-5">
                    <!--begin::Col-->
                    <div class="col-xl-8">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark productCount">Products ({{ count($data) }})</span>
                                </h3>
                            </div>
                            @foreach($data as $value)
                            <div class="card-body pt-5 productSection">
                                <div class="d-flex align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800 fs-6 fw-bolder">{{ $value->product->item_name}}</span>
                                            <span class="text-muted fw-bold d-block fs-7">CODE: {{ $value->product->item_code }}</span>
                                        </div>
                                        <span class="fw-bolder my-2 price">₱ {{ number_format_value(get_product_customer_price(@$value->product->item_prices,@Auth::user()->customer->price_list_num) * $value->qty ) }}</span>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <div class="button-wrap">
                                    <div class="counter">
                                        <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyMinus" data-url="{{ route('cart.qty-minus', $value->id)}}">
                                            <i class="fas fa-minus"></i>
                                        </a>

                                        <input class="form-control qty" data-url="{{ route('cart.update-qty', $value->id)}}" type="number" min="1" value="{{ $value->qty }}" >

                                        <a href="javascript:;" class="btn btn-xs btn-icon mr-2 qtyPlus" data-url="{{ route('cart.qty-plus', $value->id)}}">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                    <div class="remove">
                                        <a href="javascript:;" class="remove-from-cart" data-url="{{ route('cart.remove',$value->id) }}">Remove</a>
                                    </div>
                                </div>
                            </div>
                            <div class="separator separator-solid"></div>
                            @endforeach
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col-->
                    <div class="col-xl-4">
                        <!--begin::List Widget 4-->
                        <div class="card mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark">Price Details</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-5">
                                <!--begin::Item-->
                                <div class="d-flex align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800 fs-6 fw-bolder">Price</span>
                                        </div>

                                        <span class="fw-bolder my-2 totalPrice">₱ {{ number_format_value($total) }}</span>
                                    </div>
                                    <!--end::Section-->
                                </div>

                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800 fs-6 fw-bolder">Discount</span>
                                        </div>
                                        <span class="fw-bolder my-2">0</span>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="d-flex align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800 fs-6 fw-bolder">Delivery Charges</span>
                                        </div>
                                        <span class="fw-bolder my-2">FREE</span>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Item-->

                                <!--begin::Item-->
                                <div class="d-flex align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <h3 class="text-gray-800 fs-6 fw-bolder">Total Amount</h3>
                                        </div>
                                        <span class="fw-boldest fs-5 my-2 totalAmount">₱ {{ number_format_value($total) }}</span>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Item-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <div class="card shipping-card mb-5 mb-xl-8">
                            <!--begin::Header-->
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bolder text-dark">SHIPPING INFORMATION</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-5">
                                <!--begin::Item-->
                                <div class="align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="">
                                        <div class="flex-grow-1 me-2">
                                            <span class="fs-4 fw-bolder">Delivery Date :</span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Delivery Date" id="kt_datepicker_1" name="due_date" autocomplete="off">
                                    </div>
                                    <!--end::Section-->
                                </div>

                                <!--end::Item-->
                                <!--begin::Item-->
                                <div class="align-items-sm-center mb-7">
                                    <!--begin::Section-->
                                    <div class="">
                                        <div class="flex-grow-1 me-2">
                                            <span class="fs-4 fw-bolder">Delivery Location :
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-select form-select-solid" id='selectAddress' data-control="select2" data-hide-search="false" name="address_id" data-placeholder="Select address" data-allow-clear="true">
                                                @if(isset($data) && !empty($address))
                                                    <option value=""></option>
                                                    @foreach($address as $item)
                                                    <option value="{{ $item->id }}" 
                                                            
                                                            data-address="{{ $item->address }}"
                                                            data-street="{{ $item->street }}"
                                                            data-zip_code="{{ $item->zip_code }}"
                                                            data-city="{{ $item->city }}"
                                                            data-state="{{ $item->state }}"
                                                            data-country="{{ $item->country }}"

                                                        >{{$item->address}}
                                                    </option>
                                                    @endforeach
                                                @else
                                                    <option value="">No record found</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Item-->

                                <!--begin::Item-->
                                <div class="align-items-sm-center mb-7 address_details_div" style="display:none;">
                                    <!--begin::Section-->
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
                                    <!--end::Section-->
                                </div>
                                <!--end::Item-->

                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::List Widget 4-->
                    </div>
                    <!--end::Col-->
                    <div class="place-button">
                        <button type="submit" href="javascript:;" class="placeOrder btn btn-dark" >PLACE ORDER</button>
                    </div>
                </div>
            </form>
            @else
                <div class="row gy-5 g-xl-8 mt-5">
                    <div class="col-xl-12 text-center">
                        <h2>Whoops! your cart is empty. <a href="{{ route('product-list.index') }}">Add products</a></h2>
                    </div>
                </div>
            @endif
            <div class="row gy-5 g-xl-8 mt-5 emptyCart" style="display:none">
                <div class="col-xl-12 text-center">
                    <h2>Whoops! your cart is empty. <a href="{{ route('product-list.index') }}">Add products</a></h2>
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
<script>
$(document).ready(function() {
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
            }else{
                toast_success(result.message);
                setTimeout(function(){
                    window.location.reload();
                },1500)
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $(document).on('click', '.remove-from-cart', function(event) {
        event.preventDefault();
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
                        $self.closest('.productSection').remove();
                        if(result.count > 0){
                            $('.totalAmount').html('₱ '+result.total);
                            $('.totalPrice').html('₱ '+result.total);
                            $('.productCount').html('Products ('+result.count+')');
                        } else {
                            $('.emptyCart').show();
                            $('#myForm').remove();
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
        $(this).parent().find('.qty').val($qty + 1);
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
                toast_success(result.message);
                $('.totalAmount').html('₱ '+result.total);
                $('.totalPrice').html('₱ '+result.total);
                // setTimeout(function(){
                //     window.location.reload();
                // },1500)
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
                toast_success(result.message);
                if(parseInt($self.parent().find('.qty').val()) <= 0){
                    $self.closest('.productSection').remove();
                }
                if(result.count > 0){
                    $('.totalAmount').html('₱ '+result.total);
                    $('.totalPrice').html('₱ '+result.total);
                    $('.productCount').html('Products ('+result.count+')');
                    if(result.cart_count > 0){
                        $('.cartCount').show();
                        $('.cartCount').html(result.cart_count);
                    }
                } else {
                    $('.emptyCart').show();
                    $('#myForm').remove();
                    $('.cartCount').hide();
                }
                // setTimeout(function(){
                //     window.location.reload();
                // },1500)
            }
        })
        .fail(function() {
            toast_error("error");
        });
    });

    $('body').on("click", ".placeOrder", function (e) {
        e.preventDefault();
        var validator = validate_form();
        if (validator.form() != false) {
            $('[type="submit"]').prop('disabled', true);
            // $('[name="address_id"]').removeAttr('disabled');
            $.ajax({
                url: "{{route('cart.placeOrder')}}",
                type: "POST",
                data: new FormData($("#myForm")[0]),
                async: false,
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
});
</script>
@endpush
