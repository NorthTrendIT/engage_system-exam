@extends('layouts.master')

@section('title','Change Password')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Change Password</h1>
      </div>
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-6 col-md-6 col-lg-6 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">Update Details</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Current Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter current password" name="current_password">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>New Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter new password" name="new_password" id="new_password">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Confirm Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter confirm password" name="confirm_password" >
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <span class="text-muted">Password has to meet the following criteria: Must be at least 8 characters long. Must contain at least: one lowercase letter, one uppercase letter, one numeric character, and one of the following special characters !@#$%^&-_+=.</span>
                    </div>
                  </div>
                </div>
                
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="submit" value="Update" class="btn btn-primary">
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
          url: "{{route('profile.change-password.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.reload();
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
            current_password:{
              required:true,
              //minlength:8,
            },
            new_password:{
              required:true,
              minlength:8,
              maxlength:20,
            },
            confirm_password:{
              required:true,
              minlength:8,
              maxlength:20,
              equalTo : "#new_password"
            }
          },
          messages: {
            current_password:{
              required:'Please enter current password.',
              minlength:'Please enter current password greater than 8 digits',
            },
            new_password:{
              required:'Please enter new password.',
              minlength:'Please enter new password greater than 8 digits',
              maxlength:'Please enter new password less than 20 digits',
            },
            confirm_password:{
              required:'Please enter confirm password.',
              minlength:'Please enter confirm password greater than 8 digits',
              maxlength:'Please enter confirm password less than 20 digits',
              equalTo : "Enter confirm password not same as password !"
            }
          },
      });

      return validator;
    }
  
  });
</script>
@endpush