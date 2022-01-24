@extends('layouts.master')

@section('title','Assign Sales Specialist')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Assign Sales Specialist</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customers-sales-specialist.index') }}" class="btn btn-sm btn-primary">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
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

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Company<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCompany' data-control="select2" data-hide-search="false" name="company_id" data-allow-clear="true" data-placeholder="Select company">
                        <option value=""></option>

                        @foreach($company as $c)
                          <option value="{{ $c->id }}" @if(isset($edit) && $c->id == $edit->sap_connection_id ) selected @endif>{{ $c->company_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Customer<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCustomer' data-control="select2" data-hide-search="false" name="customer_ids[]">
                      </select>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Sales Specialist<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectSalseSpecialist' multiple="multiple" data-control="select2" data-hide-search="false" data-allow-clear="true" name="ss_ids[]">
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Product Brand</label>
                      <select class="form-select form-select-solid" id='selectProductBrand' data-control="select2" data-hide-search="false" data-allow-clear="true" name="product_group_id[]">
                      </select>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Product Line</label>
                      <select class="form-select form-select-solid" id='selectProductLine' data-control="select2" data-hide-search="false" data-allow-clear="true" name="product_item_line_id[]">
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Product Category</label>
                      <select class="form-select form-select-solid" id='selectProductCategory' multiple="multiple" data-control="select2" data-hide-search="false" data-allow-clear="true" name="product_tires_category_id[]">
                      </select>
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


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
$(document).ready(function() {

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('customers-sales-specialist.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){

                @if(isset($edit->id))
                  window.location.reload(); 
                @else
                  window.location.href = '{{ route('customers-sales-specialist.index') }}';
                @endif

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
            company_id:{
              required: true
            },
            "customer_ids[]":{
              required: true
            },
            "ss_ids[]":{
              required: true
            }
          },
          messages: {
            "customer_ids[]":{
              required: "Please select customers.",
            },
            "ss_ids[]":{
              required: "Please select sales specialist.",
            },
          },
      });

      return validator;
    }

    $initialCustomer = [];
    $initialSalesPerson = [];
    $initialProductBrand = [];
    $initialProductLine = [];
    $initialProductCategory = [];

    @if(isset($edit) && !empty($edit))
        var initialOption = {
            id: {{ $edit->id }},
            text: '{!! $edit->card_name.' (Code: '.$edit->card_code. (@$edit->user->email ? ', Email: '.@$edit->user->email : ""). ')' !!}',
            sap_connection_id: '{!! $edit->sap_connection_id !!}',
            selected: true
        }
        $initialCustomer.push(initialOption);
    @endif

    @if(isset($edit) && @$edit->sales_specialist)
      @foreach ($edit->sales_specialist as $data)
        var initialOption = {
            id: {{ $data->ss_id }},
            text: '{!! $data->sales_person->sales_specialist_name !!}',
            selected: true
        }
        $initialSalesPerson.push(initialOption);
      @endforeach
    @endif
    
    @if(isset($edit) && @$edit->product_groups)
      @foreach ($edit->product_groups as $data)
        var initialOption = {
            id: {{ $data->product_group_id }},
            text: '{!! $data->product_group->group_name !!}',
            selected: true
        }
        $initialProductBrand.push(initialOption);
      @endforeach
    @endif

    @if(isset($edit) && @$edit->product_item_lines)
      @foreach ($edit->product_item_lines as $data)
        var initialOption = {
            id: {{ $data->product_item_line_id }},
            text: '{!! $data->product_item_line->u_item_line !!}',
            selected: true
        }
        $initialProductLine.push(initialOption);
      @endforeach
    @endif

    @if(isset($edit) && @$edit->product_tires_categories)
      @foreach ($edit->product_tires_categories as $data)
        var initialOption = {
            id: {{ $data->product_tires_category_id }},
            text: '{!! $data->product_tires_category->u_tires !!}',
            selected: true
        }
        $initialProductCategory.push(initialOption);
      @endforeach
    @endif


    $("#selectCustomer").select2({
        @if(!isset($edit))
        ajax: {
            url: "{{route('customers-sales-specialist.getCustomers')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="company_id"]').val()
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        @endif
        placeholder: 'Select Customer',
        multiple: true,
        @if(isset($edit))
        data: $initialCustomer,
        @endif
    });

    $("#selectSalseSpecialist").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.getSalseSpecialist')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            /*var customer_data = $("#selectCustomer").select2('data')[0];
            sap_connection_id = null;
            if(customer_data != undefined){
              sap_connection_id = customer_data.sap_connection_id;
            }*/

            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="company_id"]').val()
            };
          },
          processResults: function (response) {
            return {
              results: response
            };
          },
          cache: true
      },
      placeholder: 'Select Sales Specialist',
      multiple: true,
      @if(isset($edit))
      data: $initialSalesPerson,
      @endif
    });

    $("#selectProductBrand").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.get-product-brand')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="company_id"]').val()
            };
          },
          processResults: function (response) {
              return {
                  results: response
              };
          },
          cache: true
      },
      placeholder: 'Select Product Brand',
      multiple: true,
      @if(isset($edit))
      data: $initialProductBrand,
      @endif
    });

    $("#selectProductLine").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.get-product-line')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="company_id"]').val()
            };
          },
          processResults: function (response) {
              return {
                  results: response
              };
          },
          cache: true
      },
      placeholder: 'Select Product Line',
      multiple: true,
      @if(isset($edit))
      data: $initialProductLine,
      @endif
    });

    $("#selectProductCategory").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.get-product-category')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
            
            return {
              _token: "{{ csrf_token() }}",
              search: params.term,
              sap_connection_id: $('[name="company_id"]').val()
            };
          },
          processResults: function (response) {
              return {
                  results: response
              };
          },
          cache: true
      },
      placeholder: 'Select Product Category',
      multiple: true,
      @if(isset($edit))
      data: $initialProductCategory,
      @endif
    });


    $(document).on('change', '[name="company_id"]', function(event) {
      event.preventDefault();
      $('#selectCustomer').val('').trigger('change');
      $('#selectSalseSpecialist').val('').trigger('change');
      $('#selectProductBrand').val('').trigger('change');
      $('#selectProductLine').val('').trigger('change');
      $('#selectProductCategory').val('').trigger('change');
    });

});
</script>
@endpush
