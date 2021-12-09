@extends('layouts.master')

@section('title','Place Order for Customer')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">

    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Create Order for Customer</h1>
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
                    <div class="col-md-12">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-body py-3">
                                <div class="top-items-wrapper mb-5">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="col-form-label text-right">Select Customers<span class="asterisk">*</span></label>
                                                <select class="form-select form-select-solid" id='selectCustomers' data-control="select2" data-hide-search="false" name="customer_id">
                                                    <option value="">Select Customer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <div class="form-group">
                                                <label class="col-form-label text-right">Select Address<span class="asterisk">*</span></label>
                                                <select class="form-select form-select-solid" id='selectAddress' data-control="select2" data-hide-search="false" name="address_id" disabled="disabled">
                                                    <option value="">Select Address</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <label class="col-form-label text-right">Due Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control" placeholder="Invoice Date" id="kt_datepicker_1" name="due_date" autocomplete="off">
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
                                                    <th class="min-w-80px">Price</th>
                                                    <th class="min-w-80px">Amount</th>
                                                    <th class="min-w-80px"></th>
                                                    </tr>
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody data-repeater-list="products">
                                                    <tr data-repeater-item>
                                                        <td>
                                                            <div class="form-group">
                                                                <select class="form-select form-select-solid selectProducts" data-control="select2" data-hide-search="false" name="product_id">
                                                                    <option value="">Select Product</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control quantity" name="quantity" placeholder="Enter quantity">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" placeholder="Price" value="0.00" disabled="disabled">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control" placeholder="Price" value="0.00" disabled="disabled">
                                                        </td>
                                                        <td>
                                                            <input type="button" class="btn btn-sm btn-danger" data-repeater-delete value="Delete">
                                                        </td>
                                                    </tr>
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
                                                <span style="text-align: right; width: 100%;" class="d-block">0.00</span>
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
                                                <span style="text-align: right; width: 100%;" class="d-block">0.00</span>
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
                            <input type="submit" class="btn btn-lg btn-primary" value="Place Order">
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
<script src="{{ asset('assets')}}/assets/js/custom/bootstrap-datepicker.js"/></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
<script>
    $(document).ready(function() {
        var form = $('#myForm');
        form.find('select').select2({
            ajax: {
                url: "{{route('sales-specialist-orders.getProducts')}}",
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
            placeholder: 'Select Products.',
            // minimumInputLength: 1,
            multiple: false,
            // data: $initialOptions
        });

        form.repeater({
            initEmpty: false,
            show: function () {
                $(this).slideDown();
                form.find('select').next('.select2-container').remove();
                form.find('select').select2({
                    ajax: {
                        url: "{{route('sales-specialist-orders.getProducts')}}",
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
                    placeholder: 'Select Products.',
                    // minimumInputLength: 1,
                    multiple: false,
                    // data: $initialOptions
                });
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
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
            $('#selectAddress').val(null).trigger('change');

            if($customer){
                $('#selectAddress').prop('disabled', false);
            } else {
                $('#selectAddress').prop('disabled', 'disabled');
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

        $('body').on("submit", "#myForm", function (e) {
            e.preventDefault();
            var validator = validate_form();

            if (validator.form() != false) {
                $('[type="submit"]').prop('disabled', true);
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
    });
</script>
@endpush
