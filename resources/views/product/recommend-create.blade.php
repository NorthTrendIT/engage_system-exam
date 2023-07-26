@extends('layouts.master')

@section('title','Recommended Products')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Recommended Products - Create</h1>
      </div>
      <div class="d-flex align-items-center py-1">
        <a href="{{ route('product.recommended') }}" class="btn btn-sm btn-primary">Back</a>
      </div>
    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
           {{--  <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div> --}}
            <div class="card-body">
              <div class="row">

                <div class="col-md-3">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div> 
                <div class="col-md-9">
                  <div class="form-group">
                    <select class="form-select form-select-solid" id='selectProduct' data-control="select2" data-hide-search="false" name="product_ids[]" data-allow-clear="true" data-placeholder="Select Product">
                    </select>
                  </div>
                </div>            

              </div>
              <div class="row mt-5">
                <div class="col-md-3">
                  <div class="form-group">
                    <select class="form-select form-select-solid" data-control="select2" id="selectModule" data-hide-search="false" name="module">
                      <option value="">Select Customer</option>
                      <option value="all" @if(isset($edit->module) && $edit->module == 'all') selected @endif>All</option>
                      <option value="brand" @if(isset($edit->module) && $edit->module == 'role') selected @endif>By Brand</option>
                      <option value="customer_class" @if(isset($edit->module) && $edit->module == 'customer_class') selected @endif>By Class</option>
                      <option value="sales_specialist" @if(isset($edit->module) && $edit->module == 'sales_specialist') selected @endif>By Sales Specialist</option>
                      <option value="territory" @if(isset($edit->module) && $edit->module == 'territory') selected @endif>By Territory</option>
                      <option value="market_sector" @if(isset($edit->module) && $edit->module == 'territory') selected @endif>By Market Sector</option>
                      <option value="customer" @if(isset($edit->module) && $edit->module == 'customer') selected @endif>By Customer</option>
                    </select>
                </div>
                </div>
                {{-- <div class="col-md-9">
                  <div class="form-group">
                    <select class="form-select form-select-solid" id='selectCustomer' data-control="select2" data-hide-search="false" name="customer_ids[]" data-allow-clear="true" data-placeholder="Select Customer">
                    </select>
                  </div>
                </div>    --}}
                <!-- Brand -->
                <div class="col-md-6 mb-5 brand" style="display:none">
                    <div class="form-group">
                        {{-- <label>Select Brand<span class="asterisk">*</span></label> --}}
                        <select class="form-select form-select-solid" data-control="select2" id="selectBrand" data-hide-search="false" name="record_id[]" data-placeholder="Select Brand">
                        </select>
                    </div>
                </div>

                <!-- Customer -->
                <div class="col-md-6 mb-5 customer" style="display:none">
                    <div class="form-group">
                        {{-- <label>Select Customer<span class="asterisk">*</span></label> --}}
                        <select class="form-select form-select-solid" data-control="select2" id="selectCustomer" data-hide-search="false" name="record_id[]" data-placeholder="Select Customer">
                        </select>
                    </div>
                </div>

                <!-- Customer Class -->
                <div class="col-md-6 mb-5 customer_class" style="display:none">
                    <div class="form-group">
                        {{-- <label>Select Customer Class<span class="asterisk">*</span></label> --}}
                        <select class="form-select form-select-solid" data-control="select2" id="selectCustomerClass" data-hide-search="false" name="record_id[]" data-placeholder="Select Customer Class">
                        </select>
                    </div>
                </div>

                <!-- Sales Specilalist -->
                <div class="col-md-6 mb-5 sales_specialist" style="display:none">
                    <div class="form-group">
                        {{-- <label>Select Sales Specialist<span class="asterisk">*</span></label> --}}
                        <select class="form-select form-select-solid" data-control="select2" id="selectSalesSpecialist" data-hide-search="false" name="record_id[]" data-placeholder="Select Sales Specialist">
                        </select>
                    </div>
                </div>

                <!-- Territory -->
                <div class="col-md-6 mb-5 territory" style="display:none">
                    <div class="form-group">
                        <select class="form-select form-select-solid" data-control="select2" id="selectTerritory" data-hide-search="false" name="record_id[]" data-placeholder="Select Territory">
                        </select>
                    </div>
                </div>

                <!-- Market Sector -->
                <div class="col-md-6 mb-5 market_sector" style="display:none">
                    <div class="form-group">
                        <select class="form-select form-select-solid" data-control="select2" id="selectMarketSector" data-hide-search="false" name="record_id[]" data-placeholder="Select Market Sector">
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-5 select_class_customer_div" style="display:none">
                    <div class="form-group">
                        <label>Customer Selection<span class="asterisk">*</span></label>
                        <select class="form-select form-select-solid" data-control="select2" id="selectClassCustomer" data-hide-search="false" name="select_class_customer" data-placeholder="Select Class Customer">
                            <option value=""></option>
                            <option value="all">All Customers</option>
                            <option value="specific">Specific Customers</option>
                        </select>
                    </div>
                </div>


                <!-- Customer -->
                <div class="col-md-6 mb-5 class_customer_div" style="display:none">
                    <div class="form-group">
                        <select class="form-select form-select-solid" data-control="select2" id="classCustomer" data-hide-search="false" name="class_customer[]" data-placeholder="Select Customer">
                        </select>
                    </div>
                </div>


              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')

<script>
  $(document).ready(function() {

    $('body').on('change' ,'#selectModule', function(){
        $module = $('[name="module"]').val();
        // Hide all.
        $('.brand').hide();
        $('.customer').hide();
        $('.customer_class').hide();
        $('.sales_specialist').hide();
        $('.territory').hide();
        $('.market_sector').hide();
        $('.select_class_customer_div').hide();
        $('.class_customer_div').hide();


        // Dissable all.
        $('#selectrBrand').prop('disabled', true);
        $('#selectCustomer').prop('disabled', true);
        $('#selectCustomerClass').prop('disabled', true);
        $('#selectSalesSpecialist').prop('disabled', true);
        $('#selectTerritory').prop('disabled', true);
        $('#selectMarketSector').prop('disabled', true);
        $('#selectClassCustomer').prop('disabled', true);
        $('#classCustomer').prop('disabled', true);

        // Set null value to all.
        $('#selectBrand').val(null).trigger("change");
        $('#selectCustomer').val(null).trigger("change");
        $('#selectCustomerClass').val(null).trigger("change");
        $('#selectSalesSpecialist').val(null).trigger("change");
        $('#selectTerritory').val(null).trigger("change");
        $('#selectMarketSector').val(null).trigger("change");
        // $('#selectClassCustomer').val(null).trigger("change");
        $('#classCustomer').val(null).trigger("change");

        // Show and enable according to Module selection.
        if($module == "brand"){
            $('.brand').show();
            $('#selectBrand').prop('disabled', false);
        } else if ($module == "customer"){
            $('.customer').show();
            $('#selectCustomer').prop('disabled', false);
        } else if($module == "customer_class"){
            $('.customer_class').show();
            $('#selectCustomerClass').prop('disabled', false);

            $('.select_class_customer_div').show();
            $('#selectClassCustomer').prop('disabled', false);

        } else if($module == "sales_specialist"){
            $('.sales_specialist').show();
            $('#selectSalesSpecialist').prop('disabled', false);
        } else if($module == "territory"){
            $('.territory').show();
            $('#selectTerritory').prop('disabled', false);
        } else if($module == "market_sector"){
            $('.market_sector').show();
            $('#selectMarketSector').prop('disabled', false);
        }
    });

  })
</script>
@endpush
