@extends('layouts.master')

@section('title','Change Password')

@section('content')
<style type="text/css">
  label[for=new_password],label[for=confirm_password]
{
    position: absolute;
    bottom: -18px;
    left: 0;
}
.password_generate{
  margin: 20px 0 10px 0;
}
</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Change Password</h1>
      </div>
    </div>
  </div>

  @if(Session::has('profile_error_message') || Auth::user()->first_login == 1)
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="alert alert-custom alert-danger" role="alert">
            <div class="alert-text">You have to update your password for your account security.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  
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
                      <div class="input-group input-group-solid">
                        <!-- <input class="form-control form-control-solid password" type="password" placeholder="Enter password" name="password"> -->
                        <input class="form-control form-control-solid password" type="password" placeholder="Enter password" name="new_password" id="new_password">                        
                        <div class="input-group-append password_icon_div new_pass_icon_div cursor-pointer pt-2">
                          <span class="input-group-text">
                            <i class="fas fa-eye-slash password_icon"></i>
                          </span>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>



                <!-- <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <a href="javascript:" class="btn btn-light-dark btn-sm password_generate">Password Generate</a>
                    </div>
                  </div>
                </div> -->

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Confirm Password<span class="asterisk">*</span></label>
                      {{-- <input type="password" class="form-control form-control-solid" placeholder="Enter confirm password" name="confirm_password" > --}}

                      <div class="input-group input-group-solid change_custom_pwd_class">
                        <input class="form-control form-control-solid password" type="password" placeholder="Enter confirm password" name="confirm_password">
                        <div class="input-group-append password_icon_div confirm_icon_div cursor-pointer pt-2">
                          <span class="input-group-text">
                            <i class="fas fa-eye-slash password_icon"></i>
                          </span>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <span class="text-muted">Password has to meet the following criteria: Must be at least 8 characters long. Must contain at least: one lowercase letter, one uppercase letter, one numeric character, and one of the following special characters @$!%*#?&_-~<>;</span>
                    </div>
                  </div>
                </div> -->
                
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

    jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value.indexOf(" ") < 0 && value != ""; 
    }, "No space please and don't leave it empty");

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
      }else{
        $("#new_password-error").insertAfter('.new_pass_icon_div');
        $("#confirm_password-error").insertAfter('.confirm_icon_div');
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
              noSpace: true,
            },
            new_password:{
              required:true,
              //minlength:8,
              //maxlength:20,
              noSpace: true,
            },
            confirm_password:{
              required:true,
              //minlength:8,
              //maxlength:20,
              equalTo : "#new_password",
              noSpace: true,
            }
          },
          messages: {
            current_password:{
              required:'Please enter current password.',
              //minlength:'Please enter current password greater than 8 digits',
              noSpace: 'No space allowed',
            },
            new_password:{
              required:'Please enter new password.',
              //minlength:'Please enter new password greater than 8 digits',
              //maxlength:'Please enter new password less than 20 digits',
              noSpace: 'No space allowed',
            },
            confirm_password:{
              required:'Please enter confirm password.',
              //minlength:'Please enter confirm password greater than 8 digits',
              //maxlength:'Please enter confirm password less than 20 digits',
              equalTo : "Enter confirm password not same as password !",
              noSpace: 'No space allowed',
            }
          },
      });

      return validator;
    }
  });
</script>
@endpush