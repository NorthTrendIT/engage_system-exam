@extends('layouts.master')

@section('title','Company')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Company</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('company.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Company Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter company name" name="company_name" @if(isset($edit)) value="{{ $edit->company_name }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>User Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter user name" name="user_name" @if(isset($edit)) value="{{ $edit->user_name }}" @endif>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Database Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter database name" name="db_name" @if(isset($edit)) value="{{ $edit->db_name }}" @endif>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Password<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter password" name="password" @if(isset($edit)) value="{{ $edit->password }}" @endif>
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
          url: "{{route('company.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('company.index') }}';
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
            company_name:{
              required: true,
              maxlength: 185,
            },
            user_name:{
              required: true,
              maxlength: 185,
            },
            db_name:{
              required:true,
              maxlength: 185,
            },
            password:{
              required:true,
            },
          },
          messages: {
            first_name:{
              required: "Please enter company name.",
              maxlength:'Please enter company name less than 185 character',
            },
            last_name:{
              required: "Please enter user name.",
              maxlength:'Please enter user name less than 185 character',
            },
            email:{
              required:"Please enter database name.",
              maxlength:'Please enter database name less than 185 character',
            },
            email:{
              required:"Please select password.",
              maxlength:'Please enter password less than 185 character',
            },
          },
      });

      return validator;
    }

  });
</script>
@endpush
