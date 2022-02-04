@extends('layouts.master')

@section('title','Warranty')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Warranty</h1>
            </div>
            <div class="d-flex align-items-center py-1">
                <a href="{{ route('warranty.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                                    <!-- Date -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" readonly disabled value="{{ date('F d, Y') }}">
                                        </div>
                                    </div>

                                    <!-- Customer Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Type of Warranty Claim<span class="asterisk">*</span></label>
                                            <select class="form-control form-control-lg form-control-solid" name="warranty_claim_type" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Select type of warranty claim">
                                                <option value=""></option>
                                                @foreach($warranty_claim_types as $key => $value)
                                                <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <!-- Customer Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Customer Name<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" readonly disabled value="{{ @Auth::user()->sales_specialist_name }}">
                                        </div>
                                    </div>

                                    <!-- Dealer's Name -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Dealer's Name<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="dealer_name" placeholder="Enter dealer's name">
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <!-- Address -->
                                    <div class="col-md-12">
                                        <label>Customer Address<span class="asterisk">*</span></label>
                                        <textarea class="form-control form-control-solid" name="address" placeholder="Enter address">@if(isset($edit)) {{ $edit->address }} @endif</textarea>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Location<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="location_1" placeholder="Enter location">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telephone<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="telephone_1" placeholder="Enter telephone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Location<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="location_2" placeholder="Enter location">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Telephone<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="telephone_2" placeholder="Enter telephone">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fax<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="fax" placeholder="Enter fax">
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h4 class="text-info">Tire & Vehicle Info</h4>
                                            <hr>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Maker<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_maker" placeholder="Enter vehicle maker">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Year<span class="asterisk">*</span></label>
                                            <input type="number" class="form-control form-control-solid" name="year" placeholder="Enter year">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Model<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_model" placeholder="Enter vehicle model">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>License Plate<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="license_plate" placeholder="Enter license plate">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Vehicle Mileage<span class="asterisk">*</span></label>
                                            <input type="text" class="form-control form-control-solid" name="vehicle_mileage" placeholder="Enter vehicle mileage">
                                        </div>
                                    </div>
                                </div>


                                {{-- <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <h4 class="text-info">Claim Points</h4>
                                            <hr>
                                        </div>
                                    </div>
                                </div> --}}


                                <div class="row mb-5 mt-10">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th><h4 class="text-info">Claim Points</h4></th>
                                                        <th>Yes</th>
                                                        <th>No</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach($warranty_claim_points as $key => $point)
                                                    <tr>
                                                        <td><b>{{ $key + 1}}. {{ $point->title }}</b></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                        @foreach($point->sub_titles as $s_key => $s_point)
                                                        <tr>
                                                            <td><span style="margin-left: 15px;">- {{ $s_point->title }}</span></td>
                                                            <td><input type="checkbox" class="form-check-input" name="claim_point[{{ $s_point->id }}]" value="yes" title="Yes"></td>
                                                            <td><input type="checkbox" class="form-check-input" name="claim_point[{{ $s_point->id }}]" value="no" title="No"></td>
                                                        </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>

                                            </table>
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
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
{{-- <script src="{{ asset('assets')}}/assets/js/custom/bootstrap-datepicker.js"/></script> --}}
<script>

$(document).ready(function() {

    // Select yes or no 
    $('input[type="checkbox"]').on('change', function() {
        $('input[name="' + this.name + '"]').not(this).prop('checked', false);
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

                @if(isset($edit->id))
                    window.location.reload(); 
                @else
                    window.location.href = '{{ route('promotion.index') }}';
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
            title:{
              required: true,
              maxlength: 185,
            },
            sap_connection_id:{
              required: true,
            },
            promotion_type_id:{
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
            @if(!isset($edit))
            promo_image:{
              required: true,
            },
            @endif
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
            'brand_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'B'){
                            return true;
                        }else{
                            return false;
                        }
                    },
            },
            'market_sector_ids[]':{
                required: function () {
                        if($('[name="promotion_scope"]').find('option:selected').val() == 'MS'){
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
            sap_connection_id:{
              required: "Please select business unit.",
            },
            promotion_scope:{
              required: "Please select customers.",
            },
            promotion_start_date:{
              required: "Please enter promotion stating date.",
            },
            promotion_end_date:{
              required: "Please enter promotion end date.",
            },
            promo_image:{
              required: "Please upload promotion image.",
            },
          },
      });

      return validator;
    }

});
</script>
@endpush
