@extends('layouts.master')

@section('title','Place Order for Customer')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">@if(isset($edit)) Update @else Create @endif Order for Customer</h1>
            </div>
            <div class="d-flex align-items-center py-1">
                <a href="{{ route('sales-specialist-orders.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">

        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <form method="post" id="myForm">
                    @csrf

                    @if(isset($edit))
                        <input type="hidden" value="{{$edit->id}}" name="id">
                    @endif
                    <div class="col-md-12">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-body py-3">
                                <div class="top-items-wrapper mb-5">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="col-form-label text-right">Select Customers<span class="asterisk">*</span></label>
                                                <select class="form-select form-select-solid" id='selectCustomers' data-control="select2" data-hide-search="false" name="customer_id" @if(isset($edit)) disabled="disabled" @endif>
                                                    @if(isset($edit))
                                                    <option value="{{ $edit->customer_id }}" selected>{{ $edit->customer->card_name }}</option>
                                                    @else
                                                    <option value="">Select Customer</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="col-form-label text-right">Select Address<span class="asterisk">*</span></label>
                                                <select class="form-select form-select-solid selectAddress" id='selectAddress' data-control="select2" data-hide-search="false" name="address_id" @if(!isset($edit)) disabled="disabled" @endif>
                                                    @if(isset($edit))
                                                        <option value="{{ $edit->address->id }}" selected>{{$edit->address->address}}</option>
                                                    @else
                                                        <option value="">Select Address</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <label class="col-form-label text-right">Delivery Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control" placeholder="Delivery Date" id="kt_datepicker_1" name="due_date" autocomplete="off" @if(isset($edit))  value="{{date('d/m/Y',strtotime($edit->due_date))}}" @endif>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row g-xl-8">
                            <div class="col-xl-12">
                                <div class="card card-xl-stretch mb-5 mb-xl-8">

                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bolder fs-3 mb-1">Products</span>
                                        </h3>
                                    </div>

                                    <div class="card-body py-3">

                                        <div class="table-responsive">
                                            <!--begin::Table-->
                                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                                <!--begin::Table head-->
                                                <thead>
                                                    <tr class="fw-bolder text-muted">

                                                    <th class="min-w-150px">Product</th>
                                                    <th class="min-w-80px">Quantity</th>
                                                    <th class="min-w-80px" style="text-align:right">Price</th>
                                                    <th class="min-w-80px" style="text-align:right">Amount</th>
                                                    <th class="min-w-80px"></th>
                                                    </tr>
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody data-repeater-list="products">
                                                    @if(isset($edit))
                                                        @foreach($edit->items as $value)
                                                        <tr data-repeater-item name="items">
                                                            <td>
                                                                <div class="form-group">
                                                                    <select class="form-select form-select-solid selectProducts" data-control="select2" data-hide-search="false" name="product_id">
                                                                        <option value="{{ $value->product->id }}" selected>{{ $value->product->item_name }}</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="number" class="form-control quantity" name="quantity" data-price="{{ @$value->price }}" placeholder="Enter quantity" value="{{ $value->quantity }}" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                            </td>
                                                            <td style="text-align:right">
                                                                <span class="price text-primary">₱ {{ number_format_value(@$value->price) }}</span>
                                                            </td>
                                                            <td style="text-align:right">
                                                                <span class="amount text-primary" style="font-weight: bold">₱ {{ number_format_value(@$value->total) }}</span>
                                                            </td>
                                                            <td>
                                                                <input type="button" class="btn btn-sm btn-danger" data-repeater-delete value="Delete">
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    @else
                                                    <tr data-repeater-item name="items">
                                                        <td>
                                                            <div class="form-group">
                                                                <select class="form-select form-select-solid selectProducts" data-control="select2" data-hide-search="false" name="product_id">
                                                                    <option value="">Select Product</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control quantity" name="quantity" data-price="0" placeholder="Enter quantity" value="" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                        </td>
                                                        <td style="text-align:right">
                                                            <span class="price text-primary">₱ 0</span>
                                                        </td>
                                                        <td style="text-align:right">
                                                            <span class="amount text-primary" style="font-weight: bold">₱ 0</span>
                                                        </td>
                                                        <td>
                                                            <input type="button" class="btn btn-sm btn-danger" data-repeater-delete value="Delete">
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>

                                        <div class="add-btn-wrap mb-5">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <input type="button" class="btn btn-sm btn-primary" data-repeater-create value="Add Product">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="total-box-section">

                        <div class="row gy-5 g-xl-8">
                            <div class="col-md-4 col-12"></div>
                            <div class="col-md-4 col-12"></div>
                            <div class="col-md-4 col-12">
                                <div class="card p-8">
                                    <div class="sub-total-box">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <span class="text-muted me-2 fs-7 fw-bold text-uppercase">sub total</span>
                                            </div>
                                            <div class="col-md-6 mb-3 ">
                                                <span style="text-align: right; width: 100%;" class="d-block text-primary price subTotal">@if(isset($edit)) ₱ {{ $edit->total }} @else ₱ 0.00 @endif</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span class="text-muted me-2 fs-7 fw-bold text-uppercase">discount</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span style="text-align: right; width: 100%;" class="d-block">0%</span>
                                            </div>
                                        </div>
                                        <div class="row pt-8" style="border-top: 1px solid #e4e6ef;">
                                            <div class="col-md-6 mb-3">
                                                <span class="text-muted me-2 fs-7 fw-bold text-uppercase">total</span>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <span style="text-align: right; width: 100%;" class="d-block text-primary price grandTotal">@if(isset($edit)) ₱ {{ $edit->total }} @else ₱ 0.00 @endif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row gy-5 g-xl-8">
                        <div class="col-xl-12">
                            <div class="d-flex flex-wrap pt-2 text-center justify-content-center">
                                <input type="button" class="btn btn-lg btn-primary submitForm mx-5" value="Save As Draft">
                                <input type="button" class="btn btn-lg btn-primary placeOrder" value="Save & Place Order">
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->
</div>
@endsection

@push('css')
  <link rel="stylesheet" href="{{ asset('assets') }}/assets/css/datepicker.css" class="href">
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
<script>
    @if(isset($edit))
    window.onload = function() {
        $('#selectCustomers').trigger('change');
    };
    @endif
    $(document).ready(function() {
        var form = $('#myForm');

        form.find('.selectProducts').select2({
            ajax: {
                url: "{{route('sales-specialist-orders.getProducts')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var product_ids = [];
                    $('.selectProducts').each(function(){
                        if(this.value){
                            product_ids.push(this.value);
                        }
                    });
                    return {
                        _token: "{{ csrf_token() }}",
                        filter_search: params.term,
                        customer_id: $('[name="customer_id"]').val(),
                        product_ids: product_ids,
                    };
                },
                processResults: function (response) {
                    return {
                        results:  $.map(response, function (item) {
                                    return {
                                        text: item.item_name,
                                        id: item.id
                                    }
                                })
                        };
                },
                cache: true
            },
            placeholder: 'Select Product',
            // minimumInputLength: 1,
            multiple: false,
            // data: $initialOptions
        });

        form.repeater({
            initEmpty: false,
            show: function () {
                $(this).slideDown();
                $(this).find('.price').html('₱ 0.00')
                $(this).find('.amount').html('₱ 0.00')
                var product_ids = [];
                $('.selectProducts').each(function(){
                    if(this.value){
                        product_ids.push(this.value);
                    }
                });
                form.find('.selectProducts').next('.select2-container').remove();
                form.find('.selectProducts').select2({
                    ajax: {
                        url: "{{route('sales-specialist-orders.getProducts')}}",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                _token: "{{ csrf_token() }}",
                                filter_search: params.term,
                                customer_id: $('[name="customer_id"]').val(),
                                product_ids: product_ids,
                            };
                        },
                        processResults: function (response) {
                            return {
                                results:  $.map(response, function (item) {
                                            return {
                                                text: item.item_name,
                                                id: item.id
                                            }
                                        })
                                };
                        },
                        cache: true
                    },
                    placeholder: 'Select Product',
                    // minimumInputLength: 1,
                    multiple: false,
                    // data: $initialOptions
                });
            },
            hide: function (deleteElement) {
                Swal.fire({
                    title: 'Are you sure you want to delete this product?',
                    // text: "Syncing process will run in background and it may take some time to sync all products Data.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, do it!'
                }).then((result) => {
                    if(result.isConfirmed) {
                        $(this).slideUp(deleteElement);
                    }
                })
            },
            isFirstItemUndeletable: true
        });

        $("#selectCustomers").select2({
            ajax: {
                url: "{{route('sales-specialist-orders.getCustomers')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: "{{ csrf_token() }}",
                        search: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            },
            placeholder: 'Select Customers',
            // minimumInputLength: 1,
            multiple: false,
            // data: $initialOptions
        });

        $('body').on('change' ,'#selectCustomers', function(){
            $customer = $('[name="customer_id"]').val();

            @if(empty($edit))
                $('#selectAddress').val(null).trigger('change');
            @endif

            if($customer){
                $('#selectAddress').prop('disabled', false);
            } else {
                $('#selectAddress').prop('disabled', 'disabled');
            }

            if($customer){
                $.ajax({
                    url: "{{route('sales-specialist-orders.get-customer-schedule')}}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_id: $('[name="customer_id"]').val(),
                    },
                    success: function (data) {
                        if (data.status) {
                            $dates = JSON.parse(data.dates);

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

                            if($dates.length > 0){
                                $('[name="due_date"]').datepicker({
                                    format: 'dd/mm/yyyy',
                                    todayHighlight: true,
                                    orientation: "bottom left",
                                    startDate: "+3d",
                                    autoclose: true,
                                    beforeShowDay: function(date){
                                        if ($dates.indexOf(formatDate(date)) < 0)
                                        return {
                                            enabled: false
                                        }
                                        else
                                        return {
                                            enabled: true
                                        }
                                    },
                                });
                            } else {
                                $('[name="due_date"]').datepicker({
                                    format: 'dd/mm/yyyy',
                                    todayHighlight: true,
                                    orientation: "bottom left",
                                    startDate: "+3d",
                                    autoclose: true,
                                });
                            }

                        }else{
                            $('[name="due_date"]').datepicker({
                                format: 'dd/mm/yyyy',
                                todayHighlight: true,
                                orientation: "bottom left",
                                startDate: "+3d",
                                autoclose: true,
                            });
                        }
                    },
                    error: function () {
                        toast_error("Something went to wrong !");
                    },
                });
            }
        });

        $("#selectAddress").select2({
            ajax: {
                url: "{{route('sales-specialist-orders.getAddress')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: "{{ csrf_token() }}",
                        search: params.term,
                        customer_id: $('[name="customer_id"]').val(),
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            },
            placeholder: 'Select Address',
            // minimumInputLength: 1,
            multiple: false,
            // data: $initialOptions
        });

        $('body').on("click", ".submitForm", function (e) {
            e.preventDefault();
            var validator = validate_form();
            @if(isset($edit))
                $('[name="address_id"]').removeAttr('disabled');
            @endif
            $('[name="address_id"]').removeAttr('disabled');
            if (validator.form() != false) {
                $('[type="submit"]').prop('disabled', true);
                $('[name="address_id"]').removeAttr('disabled');
                $('[name="customer_id"]').removeAttr('disabled');
                $.ajax({
                    url: "{{route('sales-specialist-orders.store')}}",
                    type: "POST",
                    data: new FormData($("#myForm")[0]),
                    async: false,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status) {
                            toast_success(data.message)
                            setTimeout(function(){
                                window.location.href = '{{ route('sales-specialist-orders.index') }}';
                            },1500)
                        } else {
                            toast_error(data.message);
                            $('[type="submit"]').prop('disabled', false);
                            $('[name="customer_id"]').prop('disabled', false);
                        }
                    },
                    error: function () {
                        toast_error("Something went to wrong !");
                        $('[type="submit"]').prop('disabled', false);
                        $('[name="customer_id"]').prop('disabled', false);
                    },
                });
            }
            @if(isset($edit))
                $('[name="address_id"]').prop('disabled', false);
            @endif
        });

        $('body').on("click", ".placeOrder", function (e) {
            e.preventDefault();
            var validator = validate_form();

            if (validator.form() != false) {
                $('[type="submit"]').prop('disabled', true);
                $('[name="address_id"]').removeAttr('disabled');
                $('[name="customer_id"]').removeAttr('disabled');
                $.ajax({
                    url: "{{route('sales-specialist-orders.placeOrder')}}",
                    type: "POST",
                    data: new FormData($("#myForm")[0]),
                    async: false,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status) {
                            toast_success(data.message)
                            setTimeout(function(){
                                window.location.href = '{{ route('sales-specialist-orders.index') }}';
                            },1500)
                        } else {
                            toast_error(data.message);
                            $('[type="submit"]').prop('disabled', false);
                            $('[name="address_id"]').removeAttr('disabled', false);
                            $('[name="customer_id"]').removeAttr('disabled', false);
                        }
                    },
                    error: function () {
                        toast_error("Something went to wrong !");
                        $('[type="submit"]').prop('disabled', false);
                        $('[name="address_id"]').removeAttr('disabled', false);
                        $('[name="customer_id"]').removeAttr('disabled', false);
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
                        required: "Please select due date.",
                    },
                    product_id:{
                        required: "Please select product.",
                    },
                    quantity:{
                        required: "Please enter quantity.",
                        min: "Quentity must be grater than Zero."
                    }
                },
                errorPlacement: function (error, element) {
                    if (element.hasClass('.select2').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
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

        $(document).on('change', '.selectProducts',function(event){
            $self = $(this);
            $customer_id = $('[name="customer_id"]').val();
            if($customer_id){
                $.ajax({
                    url: "{{route('sales-specialist-orders.get-price')}}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        product_id: $(this).val(),
                        customer_id: $('[name="customer_id"]').val(),
                    },
                    success: function (data) {
                        if (data.status) {
                            $self.parent().parent().parent().find('.price').html('₱'+ number_format(data.price));
                            $self.parent().parent().parent().find('.quantity').attr('data-price', data.price);
                            $qty = parseFloat($self.parent().parent().parent().find(".quantity").val());
                            if(isNaN($qty)){
                                $self.parent().parent().parent().find(".quantity").val(1).trigger('change');
                            } else{
                                $self.parent().parent().parent().find(".quantity").trigger('change');
                            }
                        } else {
                            toast_error(data.message);
                        }
                    },
                    error: function () {
                        toast_error("Something went to wrong !");
                    },
                });
            }
            // $(this).trigger('keyup');
        });

        $(document).on('change', "input[type=number]",function(event){
            $price = parseFloat($(this).attr('data-price'));
            $qty = parseFloat($(this).val());
            $amount = $price * $qty;
            if(isNaN($qty) || $qty <= 0){
                $(this).val(1);
                $amount = $price;
            }
            $(this).parent().parent().find('td .amount').html('₱ '+ number_format($amount.toFixed(2)));

            $grandTotal = 0;
            $("tr[name='items']").each(function(){
                $subPrice = parseFloat($(this).find('.quantity').data('price'));
                $subQty = parseFloat($(this).find('.quantity').val());
                if(!isNaN($subQty) && $subQty != "" && $subQty > 0){
                    $grandTotal += $subPrice * $subQty;
                }
            });

            $('.subTotal').html('₱ '+number_format($grandTotal.toFixed(2)));
            $('.grandTotal').html('₱ '+number_format($grandTotal.toFixed(2)));
        });

        @if(isset($edit))
            $("input[type=number]").first().trigger('change');
        @endif
    });

</script>
@endpush
