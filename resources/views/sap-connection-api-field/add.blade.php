@extends('layouts.master')

@section('title','SAP Connection API Field')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">SAP Connection API Field</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('sap-connection-api-field.index') }}" class="btn btn-sm btn-primary">Back</a>
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
            <div class="card-header border-bottom pt-5 pb-5">
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
                      <label>Business Unit<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="sap_connection_id" data-control="select2" data-hide-search="false" data-placeholder="Select business unit" data-allow-clear="true">
                        <option value=""></option>
                        @foreach($company as $c)
                        <option value="{{ $c->id }}" @if(isset($edit) && $edit->sap_connection_id == $c->id) selected @endif>{{ $c->company_name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Field<span class="asterisk">*</span></label>
                      <select class="form-control form-control-lg form-control-solid" name="field" data-control="select2" data-hide-search="false" data-placeholder="Select field" data-allow-clear="true">
                        <option value=""></option>
                        @foreach(\App\Models\SapConnectionApiField::$fields as $key => $value)
                        <option value="{{ $key }}" @if(isset($edit) && $edit->field == $key) selected @endif>{{ $value }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>


                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Field Id<span class="asterisk">*</span></label>
                      <input type="number" class="form-control form-control-solid" placeholder="Enter field id" name="sap_field_id" @if(isset($edit)) value="{{ $edit->sap_field_id }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Table Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter table name" name="sap_table_name" @if(isset($edit)) value="{{ $edit->sap_table_name }}" @endif>
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

<script>
  $(document).ready(function() {

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('sap-connection-api-field.store')}}",
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
                  window.location.href = '{{ route('sap-connection-api-field.index') }}';
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
            sap_connection_id:{
              required: true,
            },
            field:{
              required: true,
              maxlength: 185,
            },
            sap_table_name:{
              required:true,
              maxlength: 185,
            },
            sap_field_id:{
              required:true,
              maxlength: 185,
              digits:true,
            },
          },
          messages: {
            /*company_name:{
              required: "Please enter company name.",
              maxlength:'Please enter company name less than 185 character',
            },
            user_name:{
              required: "Please enter user name.",
              maxlength:'Please enter user name less than 185 character',
            },
            db_name:{
              required:"Please enter database name.",
              maxlength:'Please enter database name less than 185 character',
            },
            password:{
              required:"Please enter password.",
              maxlength:'Please enter password less than 185 character',
            },*/
          },
      });

      return validator;
    }

  });
</script>
@endpush
