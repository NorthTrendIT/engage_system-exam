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
                      <label>Assignment Name<span class="asterisk">*</span></label>
                      <input type="text" name="assignment_name" id="assignment_name" placeholder="Enter assignment name" class="form-control" value="{{(isset($edit)?@$edit->assignment_name:'')}}">
                    </div>
                  </div>

                </div>

                <div class="row mb-5">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Business Unit<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCompany' data-control="select2" data-hide-search="false" name="company_id" data-allow-clear="true" data-placeholder="Select business unit">
                        <option value=""></option>
                        @foreach($company as $c)
                          <option value="{{ $c->id }}" @if(isset($edit) && $c->id == $edit->assignment[0]->customer->real_sap_connection_id ) selected @endif>{{ $c->company_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>


                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Customer Territory<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCustomerTerritory' data-control="select2" data-hide-search="false" name="customer_territory_ids[]">
                      </select>
                    </div>
                  </div>
                </div>
                
                
                <div class="row mb-5 d-none">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Customer Group<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCustomerGroup' data-control="select2" data-hide-search="false" name="customer_group_ids[]">
                      </select>
                    </div>
                  </div>
                </div>

                @if(!isset($edit))
                <div class="col-md-6 mb-5 customer_selection_div d-none">
                  <div class="form-group">
                    <label>Customer Selection<span class="asterisk">*</span></label>
                    <select class="form-select form-select-solid" data-control="select2" id="selectCustomerSelection" data-hide-search="false" name="customer_selection" data-placeholder="Select Customer Option">
                      <option value=""></option>
                      <option value="all">All Customers</option>
                      <option value="specific">Specific Customers</option>
                    </select>
                  </div>
                </div>
                @endif

                <div class="row  mb-5 customer_div d-none" @if(!isset($edit)) style="display:none" @endif>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Customer<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectCustomer' data-control="select2" data-hide-search="false" name="customer_ids[]">
                      </select>
                      <span class="text-muted">Note: The already assigned sales specialist customer is not shown in the above list. <a href="javascript:" title="Click here" class="click_here_link">Click here</a></span>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Sales Personnel<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" id='selectSalseSpecialist' multiple="multiple" data-control="select2" data-hide-search="false" data-allow-clear="true" name="ss_ids[]">
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
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


<!--begin::Modal - View Users-->
<div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
  <!--begin::Modal dialog-->
  <div class="modal-dialog mw-650px">
    <!--begin::Modal content-->
    <div class="modal-content">
      <!--begin::Modal header-->
      <div class="modal-header pb-0 border-0 justify-content-end">
        <!--begin::Close-->
        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
          <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
              <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
            </svg>
          </span>
          <!--end::Svg Icon-->
        </div>
        <!--end::Close-->
      </div>
      <!--begin::Modal header-->
      <!--begin::Modal body-->
      <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
        <!--begin::Heading-->
        <div class="text-center mb-13">
          <!--begin::Title-->
          <h1 class="mb-3">Customer List</h1>
          <!--end::Title-->
        </div>
        <!--end::Heading-->
        <!--begin::Users-->
        <div class="mb-15">
          <!--begin::List-->
          <div class="mh-375px scroll-y me-n7 pe-7 customer_list_div" >
            <!--begin::User-->
            
            <!--end::User-->
          </div>
          <!--end::List-->
        </div>
        <!--end::Users-->
      </div>
      <!--end::Modal body-->
    </div>
    <!--end::Modal content-->
  </div>
  <!--end::Modal dialog-->
</div>
<!--end::Modal - View Users-->

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
            "customer_territory_ids[]":{
              required: true
            },
            "customer_group_ids[]":{
              required: false
            },
            customer_selection:{
              required: true
            },
            "customer_ids[]":{
              required: function () {
                      if( $('#selectCustomerSelection').find('option:selected').val() == "specific"){
                        return true;
                      }else{
                        return false;
                      }
                    },
            },
            "ss_ids[]":{
              required: true
            }
          },
          messages: {
            company_id:{
              required: "Please select business unit.",
            },
            "customer_territory_ids[]":{
              required: "Please select customer territories.",
            },
            "customer_group_ids[]":{
              required: "Please select customer groups.",
            },
            customer_selection:{
              required: "Please select customer option.",
            },
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
    $initialProductGroups = [];
    $initialProductTerritories = [];

    @if(isset($territories))      
      @foreach ($territories as $data)
        var initialOption = {
            id: {{ $data['id'] }},
            text: "{!! $data['description'] !!}",
            selected: true
        }
        $initialProductTerritories.push(initialOption);
      @endforeach      
    @endif

    @if(isset($groups))      
      @foreach ($groups as $data1)
        var initialOption = {
            id: {{ $data1['id'] }},
            text: "{!! $data1['name'] !!}",
            selected: true
        }
        $initialProductGroups.push(initialOption);
      @endforeach      
    @endif

    @if(isset($edit) && !empty($edit->assignment))
        @foreach ($edit->assignment as $data)
        var initialOption = {
            id: {{ $data->customer->id }},
            text: "{{ $data->customer->card_name}}"+`{!! ' (Code: '.$data->customer->card_code. (@$data->customer->user->email ? ', Email: '.@$data->customer->user->email : ""). ')' !!}`,
            sap_connection_id: '{!! $data->customer->sap_connection_id !!}',
            selected: true
        }
        $initialCustomer.push(initialOption);
        @endforeach
    @endif

    @if(isset($edit) && @$edit->assignment)
      
      @foreach ($ss_ids->assignment as $data1)
        @if(isset($data1->sales_person->sales_specialist_name))
          var initialOption = {
              id: {{ $data1->ss_id }},
              text: "{!! $data1->sales_person->sales_specialist_name !!} (Email: {!! $data1->sales_person->email !!})",
              selected: true
          }
          $initialSalesPerson.push(initialOption);
        @endif
      @endforeach
      
    @endif
    
    @if(isset($brand) && @$brand->brand)
      
      @foreach ($brand->brand as $data1)
        var initialOption = {
            id: {{ $data1->product_group_id }},
            text: '{!! $data1->product_group->group_name !!}',
            selected: true
        }
        $initialProductBrand.push(initialOption);
      @endforeach
      
    @endif

    @if(isset($item) && @$item->item)
      @foreach ($item->item as $data)
        var initialOption = {
            id: {{ $data->product_item_line_id }},
            text: '{!! @$data->product_item_line->u_item_line_sap_value->value ?? $data->product_item_line->u_item_line !!}',
            selected: true
        }
        $initialProductLine.push(initialOption);
      @endforeach
    @endif

    @if(isset($category) && @$category->category)
      @foreach ($category->category as $data)
        var initialOption = {
            id: {{ $data->product_tires_category_id }},
            text: '{!! $data->product_tires_category->u_tires !!}',
            selected: true
        }
        $initialProductCategory.push(initialOption);
      @endforeach
    @endif

    $("#selectCustomerTerritory").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.getCustomerTerritories')}}",
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
      placeholder: 'Select Customer Territory',
      multiple: true,
      data: $initialProductTerritories,
    });


    $("#selectCustomerGroup").select2({
      ajax: {
          url: "{{route('customers-sales-specialist.getCustomerGroups')}}",
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
      placeholder: 'Select Customer Group',
      multiple: true,
      data: $initialProductGroups,
    });

    $("#selectCustomer").select2({
        
        ajax: {
            url: "{{route('customers-sales-specialist.getCustomers')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="company_id"]').val(),
                    group_id: $('#selectCustomerGroup').find('option:selected').toArray().map(item => item.value),
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        
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
              sap_connection_id: $('[name="company_id"]').val(),
              territories: $('[name="customer_territory_ids[]"]').select2('val')
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
      $('#selectCustomerGroup').val('').trigger('change');
      $('#selectCustomerSelection').val('').trigger('change');
      $('#selectSalseSpecialist').val('').trigger('change');
      $('#selectProductBrand').val('').trigger('change');
      $('#selectProductLine').val('').trigger('change');
      $('#selectProductCategory').val('').trigger('change');
    });


    $(document).on('change', '[name="customer_selection"]', function(event) {
      event.preventDefault();
      if($(this).val() == "specific"){
        $('.customer_div').show();
      }else{
        $('.customer_div').hide();
      }
    });

    function showCustomerList(){
      $.ajax({
        url: "{{route('customers-sales-specialist.get-assigned-customer-list')}}",
        type: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          sap_connection_id: $('[name="company_id"]').val(),
          group_id: $('#selectCustomerGroup').find('option:selected').toArray().map(item => item.value),
        },
        success: function (data) {
          if (data.status) {
            $('.customer_list_div').html(data.html);
          } else {
            $('.customer_list_div').html(data.html);
          }
        },
        error: function () {
          toast_error("Something went to wrong !");
          $('.customer_list_div').html("");
        },
      });
    }

    $(document).on('click', '.click_here_link', function(event) {
      event.preventDefault();
      
      showCustomerList();
      $('#kt_modal_view_users').modal('show');

    });

});
</script>
@endpush
