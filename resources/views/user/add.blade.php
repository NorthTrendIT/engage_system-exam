@extends('layouts.master')

@section('title','User')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">User</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('user.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                      <label>First Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter first name" name="first_name" @if(isset($edit)) value="{{ $edit->first_name }}" @endif>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Last Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter last name" name="last_name" @if(isset($edit)) value="{{ $edit->last_name }}" @endif>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Email<span class="asterisk">*</span></label>
                      <input type="email" class="form-control form-control-solid" placeholder="Enter email" name="email" @if(isset($edit)) value="{{ $edit->email }}" @endif>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Role<span class="asterisk">*</span></label>
                      <select class="form-control" name="role_id">
                        <option value=""></option>
                        @foreach($roles as $role)
                          <option value="{{ $role->id }}" @if(isset($edit) && $edit->role_id == $role->id) selected="" @endif>{{ $role->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>

                @if(!isset($edit))
                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter password" name="password" id="password">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Confirm Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter confirm password" name="confirm_password">
                    </div>
                  </div>

                </div>
                @endif

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Profile</label>
                      <input type="file" class="form-control form-control-solid" name="profile" accept="image/*">

                      @if(isset($edit) && $edit->profile &&get_valid_file_url('sitebucket/users',$edit->profile))
                        <img src="{{ get_valid_file_url('sitebucket/users',$edit->profile) }}" height="100" width="100" class="mt-10">
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


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
  $(document).ready(function() {

    $('[name="role_id"]').select2({
      placeholder: "Select a role",
      allowClear: true
    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('user.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('user.index') }}';
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
            first_name:{
              required: true,
              maxlength: 185,
            },
            last_name:{
              required: true,
              maxlength: 185,
            },
            email:{
              required:true,
              maxlength: 185,
            },
            role_id:{
              required:true,
            },
            @if(!isset($edit))
            password:{
              required:true,
              minlength:8,
            },
            confirm_password:{
              required:true,
              minlength:8,
              equalTo : "#password"
            }
            @endif
          },
          messages: {
            first_name:{
              required: "Please enter first name.",
              maxlength:'Please enter first name less than 185 character',
            },
            last_name:{
              required: "Please enter last name.",
              maxlength:'Please enter last name less than 185 character',
            },
            email:{
              required:"Please enter email.",
              maxlength:'Please enter email less than 185 character',
            },
            email:{
              required:"Please select role.",
            },
            password:{
              required:'Please enter password.',
              minlength:'Please enter password greater than 8 digits',
            },
            confirm_password:{
              required:'Please enter confirm password.',
              minlength:'Please enter confirm password greater than 8 digits',
              equalTo : "Enter confirm password same as password !"
            }
          },
      });

      return validator;
    }
  
  });
</script>
@endpush