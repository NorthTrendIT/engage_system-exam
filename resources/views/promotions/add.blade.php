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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Title<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter Promotion Title" name="title" @if(isset($edit)) value="{{ $edit->title }}" @endif >
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Promotion Type<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="promotion_type_id">
                        <option value="">Select Promotion Type</option>
                        @if(!empty($promotion_type))
                            @foreach($promotion_type as $type)
                                <option value="1" @if(isset($edit) && $edit->promotion_type_id == $type['id']) selected="" @endif>{{ $type['name'] }}</option>
                            @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <label>Description</label>
                    <textarea class="form-control form-control-solid" name="description">@if(isset($edit)) {{ $edit->description }} @endif</textarea>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Discount Percentage<span class="asterisk">*</span></label>
                      <input type="number" class="form-control form-control-solid" placeholder="Enter Promotion Discount Percentage" name="discount_percentage" @if(isset($edit)) value="{{ $edit->discount_percentage }}" @endif >
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Promotion For<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="promotion_for">
                        <option value="">Select Promotion For</option>
                        <option value="All" @if(isset($edit) && $edit->promotion_for == "All") selected="" @endif>All</option>
                        <option value="Limited" @if(isset($edit) && $edit->promotion_for == "Limited") selected="" @endif>Limited</option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Select Promotion Scope<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="promotion_scope">
                        <option value="">Select Promotion Scope</option>
                        <option value="C" @if(isset($edit) && $edit->promotion_scope == "C") selected="" @endif>Customers</option>
                        <option value="CL" @if(isset($edit) && $edit->promotion_scope == "CL") selected="" @endif>Class</option>
                        <option value="L" @if(isset($edit) && $edit->promotion_scope == "L") selected="" @endif>Location</option>
                        <option value="P" @if(isset($edit) && $edit->promotion_scope == "P") selected="" @endif>Products</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Promotion Start Date<span class="asterisk">*</span></label>
                      <input type="text" class="form-control" name="promotion_start_date" @if(isset($edit)) value="{{date('m/d/Y',strtotime($edit->promotion_start_date))}}" @endif id="kt_datepicker_1" readonly placeholder="Select Promotion Start Date"/>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Promotion end Date<span class="asterisk">*</span></label>
                      <input type="text" class="form-control" name="promotion_end_date" @if(isset($edit)) value="{{date('m/d/Y',strtotime($edit->promotion_end_date))}}" @endif id="kt_datepicker_1" readonly placeholder="Select Promotion End Date"/>
                    </div>
                  </div>

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
                      <input type="submit" value="{{ isset($edit) ? "Update" : "Add" }}" class="btn btn-primary">
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
  <style>
    /* Datepicker CSS */
    .datepicker {
      width: 265px;
      padding: 10px;
      border-radius: 0.42rem;
    }
    .datepicker.datepicker-orient-top {
      margin-top: 8px;
    }
    .datepicker table {
      width: 100%;
    }
    .datepicker td,
    .datepicker th {
      font-size: 1rem;
      font-weight: regular;
      width: 33px;
      height: 33px;
      border-radius: 0.42rem;
    }
    .datepicker thead th {
      color: #3F4254;
    }
    .datepicker thead th.prev, .datepicker thead th.datepicker-switch, .datepicker thead th.next {
      font-weight: 500;
      color: #3F4254;
    }
    .datepicker thead th.prev i, .datepicker thead th.datepicker-switch i, .datepicker thead th.next i {
      font-size: 1.2rem;
      color: #7E8299;
    }
    .datepicker thead th.prev i:before, .datepicker thead th.datepicker-switch i:before, .datepicker thead th.next i:before {
      line-height: 0;
      vertical-align: middle;
    }
    .datepicker thead th.prev:hover, .datepicker thead th.datepicker-switch:hover, .datepicker thead th.next:hover {
      background: #F3F6F9 !important;
    }
    .datepicker thead th.dow {
      color: #3F4254;
      font-weight: 600;
    }
    .datepicker tbody tr > td {
      width: 35px;
      height: 35px;
    }
    .datepicker tbody tr > td.day {
      color: #7E8299;
      font-weight: 400;
      text-align: center;
    }
    .datepicker tbody tr > td.day:hover {
      background: #F3F6F9;
      color: #3F4254;
    }
    .datepicker tbody tr > td.day.old {
      color: #7E8299;
    }
    .datepicker tbody tr > td.day.new {
      color: #3F4254;
    }
    .datepicker tbody tr > td.day.selected, .datepicker tbody tr > td.day.selected:hover, .datepicker tbody tr > td.day.active, .datepicker tbody tr > td.day.active:hover {
      background: #3699FF;
      color: #ffffff;
    }
    .datepicker tbody tr > td.day.today {
      position: relative;
      background: #E1F0FF !important;
      color: #3699FF !important;
    }
    .datepicker tbody tr > td.day.today:before {
      content: "";
      display: inline-block;
      border: solid transparent;
      border-width: 0 0 7px 7px;
      border-bottom-color: #3699FF;
      border-top-color: #3699FF;
      position: absolute;
      bottom: 4px;
      right: 4px;
    }
    .datepicker tbody tr > td.day.range {
      background: #F3F6F9;
    }
    .datepicker tbody tr > td span.year,
    .datepicker tbody tr > td span.hour,
    .datepicker tbody tr > td span.minute,
    .datepicker tbody tr > td span.month {
      color: #7E8299;
    }
    .datepicker tbody tr > td span.year:hover,
    .datepicker tbody tr > td span.hour:hover,
    .datepicker tbody tr > td span.minute:hover,
    .datepicker tbody tr > td span.month:hover {
      background: #F3F6F9;
    }
    .datepicker tbody tr > td span.year.focused, .datepicker tbody tr > td span.year.focused:hover, .datepicker tbody tr > td span.year.active:hover, .datepicker tbody tr > td span.year.active.focused:hover, .datepicker tbody tr > td span.year.active,
    .datepicker tbody tr > td span.hour.focused,
    .datepicker tbody tr > td span.hour.focused:hover,
    .datepicker tbody tr > td span.hour.active:hover,
    .datepicker tbody tr > td span.hour.active.focused:hover,
    .datepicker tbody tr > td span.hour.active,
    .datepicker tbody tr > td span.minute.focused,
    .datepicker tbody tr > td span.minute.focused:hover,
    .datepicker tbody tr > td span.minute.active:hover,
    .datepicker tbody tr > td span.minute.active.focused:hover,
    .datepicker tbody tr > td span.minute.active,
    .datepicker tbody tr > td span.month.focused,
    .datepicker tbody tr > td span.month.focused:hover,
    .datepicker tbody tr > td span.month.active:hover,
    .datepicker tbody tr > td span.month.active.focused:hover,
    .datepicker tbody tr > td span.month.active {
      background: #3699FF;
      color: #ffffff;
    }
    .datepicker tfoot tr > th {
      width: 35px;
      height: 35px;
    }
    .datepicker tfoot tr > th.today, .datepicker tfoot tr > th.clear {
      border-radius: 0.42rem;
      font-weight: 500;
    }
    .datepicker tfoot tr > th.today:hover, .datepicker tfoot tr > th.clear:hover {
      background: #EBEDF3;
    }
    .datepicker.datepicker-inline {
      border: 1px solid #EBEDF3;
    }
  </style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" ></script>
<script src="{{ asset('assets')}}/assets/js/custom/bootstrap-datepicker.js"/></script>
<script>

  $(document).ready(function() {

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
            discount_percentage:{
              required: true,
              max: 100,
              min: 1,
            },
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

  });
</script>
@endpush
