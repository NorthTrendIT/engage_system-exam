@extends('layouts.master')

@section('title','Promotion')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion</h1>
            </div>
            <div class="d-flex align-items-center py-1">
                <a href="{{ route('promotion.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-bottom pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">{{ isset($edit) ? "Update" : "Add" }} Details</h1>
                        </div>

                        <div class="card-body">
                            <form method="post" id="myForm">
                                @csrf

                                @if(isset($edit))
                                <input type="hidden" name="id" value="{{ $edit->id }}">
                                @endif

                                <div class="row mb-5">
                                    <!-- Title -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" placeholder="Enter Promotion Title" name="title" @if(isset($edit)) value="{{ $edit->title }}" @endif >
                                        </div>
                                    </div>

                                    <!-- Promotion Type -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Promotion Type<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="false" name="promotion_type_id" data-placeholder="Select Promotion Type">
                                                <option value=""></option>
                                                @if(!empty($promotion_type))
                                                    @foreach($promotion_type as $type)
                                                        <option value="{{ $type['id'] }}" @if(isset($edit) && $edit->promotion_type_id == $type['id']) selected="" @endif>{{ $type['title'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <!-- Description -->
                                    <div class="col-md-12">
                                        <label>Description</label>
                                        <textarea class="form-control form-control-solid" name="description">@if(isset($edit)) {{ $edit->description }} @endif</textarea>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <!-- Discount Percentage -->
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Discount Percentage<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" placeholder="Enter Promotion Discount Percentage" name="discount_percentage" @if(isset($edit)) value="{{ $edit->discount_percentage }}" @endif >
                                        </div>
                                    </div> --}}

                                    <!-- Promotion For -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Promotion For<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="promotion_for" id="promotion_for">
                                                <option value="">Select Promotion For</option>
                                                <option value="All" @if(isset($edit) && $edit->promotion_for == "All") selected="" @endif>All</option>
                                                <option value="Limited" @if(isset($edit) && $edit->promotion_for == "Limited") selected="" @endif>Limited</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <!-- Promotion Scope -->
                                    <div class="col-md-6" id="scope_block" style="display: {{ isset($edit) ? ($edit->promotion_for == 'All' ? 'none' : '') : 'none' }}">
                                        <div class="form-group">
                                            <label>Select Promotion Scope<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="promotion_scope" id="promotion_scope">
                                                <option value="">Select Promotion Scope</option>
                                                <option value="C" @if(isset($edit) && $edit->promotion_scope == "C") selected="" @endif>Customers</option>
                                                <option value="CL" @if(isset($edit) && $edit->promotion_scope == "CL") selected="" @endif>Class</option>
                                                <option value="T" @if(isset($edit) && $edit->promotion_scope == "T") selected="" @endif>Territories</option>
                                                <option value="SS" @if(isset($edit) && $edit->promotion_scope == "SS") selected="" @endif>Sales Specialists</option>
                                                {{-- <option value="P" @if(isset($edit) && $edit->promotion_scope == "P") selected="" @endif>Products</option> --}}
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Customers -->
                                    <div class="col-md-6" id="customer_block" style="display: {{ isset($edit) ? ($edit->promotion_scope == 'C' ? '' : 'none') : 'none'}}">
                                        <div class="form-group">
                                            <label>Select Customers<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" id='selectCustomers' multiple="multiple" data-control="select2" data-hide-search="false" name="customer_ids[]">
                                                <option value=''>Select Customers</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Products -->
                                    <div class="col-md-6" id="product_block" style="display: {{ isset($edit) ? ($edit->promotion_scope == 'P' ? '' : 'none') : 'none'}}">
                                        <div class="form-group">
                                            <label>Select Products<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" id='selectProducts' multiple="multiple" data-control="select2" data-hide-search="false" name="product_ids[]">
                                                <option value=''>Select Products</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Territories -->
                                    <div class="col-md-6" id="territories_block" style="display: {{ isset($edit) ? ($edit->promotion_scope == 'T' ? '' : 'none') : 'none'}}">
                                        <div class="form-group">
                                            <label>Select Territories<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" id='selectTerritories' multiple="multiple" data-control="select2" data-hide-search="false" name="territories_ids[]">
                                                <option value=''>Select Territories</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Classes -->
                                    <div class="col-md-6" id="class_block" style="display: {{ isset($edit) ? ($edit->promotion_scope == 'CL' ? '' : 'none') : 'none'}}">
                                        <div class="form-group">
                                            <label>Select Classes<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" id='selectClasses' multiple="multiple" data-control="select2" data-hide-search="false" name="class_ids[]">
                                                <option value=''>Select Classes</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- sales_specialists-->
                                    <div class="col-md-6" id="sales_specialist_block" style="display: {{ isset($edit) ? ($edit->promotion_scope == 'SS' ? '' : 'none') : 'none'}}">
                                        <div class="form-group">
                                            <label>Select Sales Specialists<span class="asterisk">*</span></label>
                                            <select class="form-select form-select-solid" id='selectSalesSpecialist' multiple="multiple" data-control="select2" data-hide-search="false" name="sales_specialist_ids[]">
                                                <option value=''>Select Sales Specialists</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="row mb-5">
                                    <!-- Promotion Start Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Promotion Start Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-select-solid" name="promotion_start_date" @if(isset($edit)) value="{{date('m/d/Y',strtotime($edit->promotion_start_date))}}" @endif id="kt_datepicker_1" readonly placeholder="Select Promotion Start Date"/>
                                        </div>
                                    </div>

                                    <!-- Promotion End Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Promotion end Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-select-solid" name="promotion_end_date" @if(isset($edit)) value="{{date('m/d/Y',strtotime($edit->promotion_end_date))}}" @endif id="kt_datepicker_1" readonly placeholder="Select Promotion End Date"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <!-- Promotion Image -->
                                    <div class="col-md-6 mt-5">
                                        <div class="custom-file">
                                            <input type="file" class="form-control form-control-solid" name="promo_image"/>
                                            @if(isset($edit))
                                                <input type="hidden" class="form-control form-control-solid" name="old_promo_image" value="{{ $edit->promo_image }}"/>
                                                @if($edit->promo_image)
                                                    <img src="{{ get_valid_file_url('sitebucket/promotion',$edit->promo_image) }}" height="100" width="100" class="mt-10">
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
  <link rel="stylesheet" href="{{ asset('assets') }}/assets/css/datepicker.css" class="href">
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>
{{-- <script src="{{ asset('assets')}}/assets/js/custom/bootstrap-datepicker.js"/></script> --}}
<script>

  $(document).ready(function() {

    $('[name="promotion_start_date"]').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        startDate:'today',
        autoclose: true,
    });

    $('[name="promotion_start_date"]').datepicker().on('changeDate', (selected) => {
        $('[name="promotion_end_date"]').val("").datepicker("update");
        var minDate = new Date(selected.date.valueOf());
        $('[name="promotion_end_date"]').datepicker('setStartDate', minDate);
    });

    $('[name="promotion_end_date"]').datepicker({
        todayHighlight: true,
        orientation: "bottom left",
        startDate:'today',
        autoclose: true,
    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('promotion.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('promotion.index') }}';
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
            title:{
              required: true,
              maxlength: 185,
            },
            promotion_type_id:{
              required: true,
            },
            // discount_percentage:{
            //   required: true,
            //   max: 100,
            //   min: 1,
            // },
            promotion_for:{
              required: true,
            },
            promotion_scope:{
              required: true,
            },
            promotion_start_date:{
              required: true,
            },
            promotion_end_date:{
              required: true,
            },
            'customer_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'C'){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            'class_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'CL'){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            'territories_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'T'){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            'sales_specialist_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'SS'){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
          },
          messages: {
            title:{
              required: "Please enter promotion title.",
              maxlength: "Promotion title is too long.",
            },
            promotion_type_id:{
              required: "Please select promotion type.",
            },
            discount_percentage:{
              required: "Please enter discount percentage.",
              max: "Please enter value less then 100.",
              min: "Please enter value greater than 0.",
            },
            promotion_for:{
              required: "Please select promotion for.",
            },
            promotion_scope:{
              required: "Please select promotion scope.",
            },
            promotion_start_date:{
              required: "Please enter promotion stating date.",
            },
            promotion_end_date:{
              required: "Please enter promotion end date.",
            },
          },
      });

      return validator;
    }

    $('body').on('change' ,'#promotion_for', function(){
        $promo_for = $('[name="promotion_for"]').val();
        console.log($promo_for);
        if($promo_for == "All"){
            $('#scope_block').hide();
            $('#customer_block').hide();
            $('#product_block').hide();
            $('#territories_block').hide();
            $('#class_block').hide();
            $('#sales_specialist_block').hide();
        } else {
            $('body #scope_block').show();
        }
    });

    $('body').on('change' ,'#promotion_scope', function(){
        $promo_scope = $('[name="promotion_scope"]').val();

        $('#customer_block').hide();
        $('#product_block').hide();
        $('#territories_block').hide();
        $('#class_block').hide();
        $('#sales_specialist_block').hide();


        if($promo_scope == "C"){
            $('#customer_block').show();
        } else if($promo_scope == "P"){
            $('#product_block').show();
        } else if($promo_scope == "T"){
            $('#territories_block').show();
        } else if($promo_scope == "CL"){
            $('#class_block').show();
        } else if($promo_scope == "SS"){
            $('#sales_specialist_block').show();
        }
    });

    $initialOptions = [];

    @if(isset($edit) && $edit->promotion_scope == 'C')
        @foreach ($edit->promotion_data as $data)
            var initialOption = {
                id: {{ $data->customer_id }},
                text: '{{ $data->customer->card_name }}',
                selected: true
            }
            $initialOptions.push(initialOption);
        @endforeach
    @endif

    @if(isset($edit) && $edit->promotion_scope == 'P')
        @foreach ($edit->promotion_data as $data)
            var initialOption = {
                id: {{ $data->product_id }},
                text: '{{ $data->product->item_name }}',
                selected: true
            }
            $initialOptions.push(initialOption);
        @endforeach
    @endif

    @if(isset($edit) && $edit->promotion_scope == 'T')
        @foreach ($edit->promotion_data as $data)
            var initialOption = {
                id: {{ $data->territory_id }},
                text: '{{ $data->territory->description }}',
                selected: true
            }
            $initialOptions.push(initialOption);
        @endforeach
    @endif

    @if(isset($edit) && $edit->promotion_scope == 'CL')
        @foreach ($edit->promotion_data as $data)
            var initialOption = {
                id: {{ $data->class_id }},
                text: '{{ $data->class->name }}',
                selected: true
            }
            $initialOptions.push(initialOption);
        @endforeach
    @endif

    @if(isset($edit) && $edit->promotion_scope == 'SS')
        @foreach ($edit->promotion_data as $data)
            var initialOption = {
                id: {{ $data->sales_specialist_id }},
                text: '{{ $data->sales_specialist->sales_specialist_name }}',
                selected: true
            }
            $initialOptions.push(initialOption);
        @endforeach
    @endif

    $("#selectCustomers").select2({
        ajax: {
            url: "{{route('promotion.getCustomers')}}",
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
        placeholder: 'Select Customers.',
        // minimumInputLength: 1,
        multiple: true,
        @if(isset($edit) && $edit->promotion_scope == 'C')
        data: $initialOptions
        @endif
    });

    $("#selectProducts").select2({
        ajax: {
            url: "{{route('promotion.getProducts')}}",
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
        multiple: true,
        @if(isset($edit) && $edit->promotion_scope == 'P')
        data: $initialOptions
        @endif
    });

    $("#selectTerritories").select2({
        ajax: {
            url: "{{route('promotion.getTerritories')}}",
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
        placeholder: 'Select Territories.',
        // minimumInputLength: 1,
        multiple: true,
        @if(isset($edit) && $edit->promotion_scope == 'T')
        data: $initialOptions
        @endif
    });

    $("#selectClasses").select2({
        ajax: {
            url: "{{route('promotion.getClasses')}}",
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
        placeholder: 'Select Classes.',
        // minimumInputLength: 1,
        multiple: true,
        @if(isset($edit) && $edit->promotion_scope == 'CL')
        data: $initialOptions
        @endif
    });

    $("#selectSalesSpecialist").select2({
        ajax: {
            url: "{{route('promotion.getSalesSpecialist')}}",
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
        placeholder: 'Select Sales Specialists.',
        // minimumInputLength: 1,
        multiple: true,
        @if(isset($edit) && $edit->promotion_scope == 'SS')
        data: $initialOptions
        @endif
    });

});
</script>
@endpush
