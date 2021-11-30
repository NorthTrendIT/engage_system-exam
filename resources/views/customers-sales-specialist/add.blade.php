@extends('layouts.master')

@section('title','Assign Sales Specialist')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Assign Salse Specialist</h1>
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
                  <input type="hidden" name="id" value="{{ $customer->id }}">
                @endif

                <div class="row mb-5">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Customer<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" id='selectCustomer' data-control="select2" data-hide-search="false" name="customer_id">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Select Sales Specialist<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" id='selectSalseSpecialist' multiple="multiple" data-control="select2" data-hide-search="false" name="ss_ids[]">
                                <option value=""></option>
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

    $('[name="parent_id"]').select2({
      placeholder: "Select a province",
      allowClear: true
    });

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
                window.location.href = '{{ route('customers-sales-specialist.index') }}';
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
              required: true
            },
            ss_ids:{
              required: true
            }
          },
          messages: {
            name:{
              required: "Please enter name.",
              maxlength:'Please enter name less than 185 character',
            }
          },
      });

      return validator;
    }

    $initialCustomer = [];
    $initialSalesPerson= [];

    @if(isset($customer) && !empty($customer))
        var initialOption = {
            id: {{ $customer->id }},
            text: '{{ $customer->card_name }}',
            selected: true
        }
        $initialCustomer.push(initialOption);
    @endif

    @if(isset($edit) && !empty($edit))
        @foreach ($edit as $data)
            var initialOption = {
                id: {{ $data->ss_id }},
                text: '{{ $data->sales_person->sales_specialist_name }}',
                selected: true
            }
            $initialSalesPerson.push(initialOption);
        @endforeach
    @endif

    $("#selectCustomer").select2({
        ajax: {
            url: "{{route('customers-sales-specialist.getCustomers')}}",
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
        placeholder: 'Select Customer',
        multiple: false,
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
        placeholder: 'Select Sales Specialist',
        multiple: true,
        @if(isset($edit))
        data: $initialSalesPerson,
        @endif
    });

});
</script>
@endpush
