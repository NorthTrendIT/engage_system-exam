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
                      <label>Department<span class="asterisk">*</span></label>
                      <select class="form-control form-control-solid" name="department_id">
                        <option value=""></option>
                        @foreach($departments as $department)
                          <option value="{{ $department->id }}" @if(isset($edit) && $edit->department_id == $department->id) selected="" @endif>{{ $department->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                </div>

                <div class="row mb-5">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Role<span class="asterisk">*</span></label>
                      <select class="form-control form-control-solid" name="role_id">
                        <option value=""></option>
                        @if(isset($edit) && $edit->role_id == 4)
                          <option value="4" selected>Customer</option>
                        @endif
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="parent_id_label">Parent User</label>
                      <select class="form-control form-control-solid" name="parent_id">
                        <option value=""></option>
                        
                      </select>
                    </div>
                  </div>

                </div>

                {{-- <div class="row mb-5">
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Province</label>
                      <select class="form-control form-control-solid" name="province_id">
                        <option value=""></option>
                        @foreach($provinces as $province)
                          <option value="{{ $province->id }}" @if(isset($edit) && $edit->province_id == $province->id) selected="" @endif>{{ $province->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>City</label>
                      <select class="form-control form-control-solid" name="city_id">
                        <option value=""></option>
                        @if(isset($edit))
                          @foreach($cities as $city)
                            <option value="{{ $city->id }}" @if(isset($edit) && $edit->city_id == $city->id) selected="" @endif>{{ $city->name }}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                  </div>

                </div> --}}


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

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <span class="text-muted">Password has to meet the following criteria: Must be at least 8 characters long. Must contain at least: one lowercase letter, one uppercase letter, one numeric character, and one of the following special characters !@#$%^&-_+=.</span>
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
                      <input type="submit" value="{{ isset($edit) ? "Update" : "Save" }}" class="btn btn-primary">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>


      @if(isset($edit))
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">Update Password</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myPasswordForm">
                @csrf
                <input type="hidden" name="id" value="{{ $edit->id }}">

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>New Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter new password" name="new_password" id="new_password">
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Confirm Password<span class="asterisk">*</span></label>
                      <input type="password" class="form-control form-control-solid" placeholder="Enter confirm password" name="confirm_password">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-6">
                    <div class="form-group">
                      <span class="text-muted">Password has to meet the following criteria: Must be at least 8 characters long. Must contain at least: one lowercase letter, one uppercase letter, one numeric character, and one of the following special characters !@#$%^&-_+=.</span>
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
      @endif


    </div>
  </div>
</div>
@endsection


@push('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js"></script>

<script>
  $(document).ready(function() {
    $('[name="department_id"]').select2({
      placeholder: "Select a department",
      allowClear: true
    });

    $('[name="parent_id"]').select2({
      placeholder: "Select a parent",
      allowClear: true
    });

    $('[name="role_id"]').select2({
      placeholder: "Select a role",
      allowClear: true
    });

    $('[name="province_id"]').select2({
      placeholder: "Select a province",
      allowClear: true
    });

    $('[name="city_id"]').select2({
      placeholder: "Select a city",
      allowClear: true
    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $(this).find('[type="submit"]').prop('disabled', true);
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

                @if(isset($edit->id))
                  window.location.reload(); 
                @else
                  window.location.href = '{{ route('user.index') }}';
                @endif
                
              },1500)
            } else {
              toast_error(data.message);
              $(this).find('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $(this).find('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });

    $(document).on('change', '[name="province_id"]', function(event) {
      event.preventDefault();
      getCity();
    });

    $(document).on('change', '[name="department_id"]', function(event) {
      event.preventDefault();
      getRoles();
    });

    $(document).on('change', '[name="role_id"]', function(event) {
      event.preventDefault();
      getParents();
    });

    @if(isset($edit))
      @if($edit->role_id != 4)
      getRoles('{{ $edit->role_id }}');
      @endif
      setTimeout(function(){ getParents('{{ $edit->parent_id }}'); }, 1000);
    @endif

    function getCity() {
      $id = $('[name="province_id"]').find('option:selected').val();

      $.ajax({
        url: '{{ route('user.get-city') }}',
        method: "POST",
        data: {
                id:$id, 
                _token:'{{ csrf_token() }}' 
              }
      })
      .done(function(result) {
        var option = "<option value=''></option>";
        $.each(result, function(index, val) {
          option += '<option value='+val.id+'>'+val.name+'</option>';
        });

        $('[name="city_id"]').html(option);
        // $('[name="city_id"]').select2();
      })
      .fail(function() {
        toast_error("error");
      });
    }

    function getRoles($selected_id = false) {
      $department_id = $('[name="department_id"]').find('option:selected').val();

      $.ajax({
        url: '{{ route('user.get-roles') }}',
        method: "POST",
        data: {
                department_id:$department_id, 
                _token:'{{ csrf_token() }}' 
              }
      })
      .done(function(result) {
        var option = "<option value=''></option>";
        $.each(result, function(index, val) {
          var selected = "";

          if($selected_id == val.role.id){
            selected = "selected";
          }

          option += '<option value='+val.role.id+' '+ selected + '>'+val.role.name+'</option>';
        });

        $('[name="role_id"]').html(option);
      })
      .fail(function() {
        toast_error("error");
      });
    }

    function getParents($selected_id = false) {
      $role_id = $('[name="role_id"]').find('option:selected').val();

      $.ajax({
        url: '{{ route('user.get-parents') }}',
        method: "POST",
        data: {
                @if(isset($edit))
                id:'{{ $edit->id }}', 
                @endif
                role_id:$role_id, 
                _token:'{{ csrf_token() }}' 
              }
      })
      .done(function(result) {
        var option = "<option value=''></option>";
        $.each(result.users, function(index, val) {

          var selected = "";

          if($selected_id == val.id){
            selected = "selected";
          }

          option += '<option value='+val.id+' '+ selected + '>'+val.first_name+' '+val.last_name+'</option>';

        });

        $('[name="parent_id"]').html(option);

        var parent_name = "Parent User";
        if(result.parent_name){
          parent_name = result.parent_name;
        }
        
        $('[name="parent_id"]').select2({
          placeholder: "Select " + parent_name,
          allowClear: true
        });
        $('.parent_id_label').text(parent_name);

      })
      .fail(function() {
        toast_error("error");
      });
    }

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
            department_id:{
              required:true,
            },
            @if(!isset($edit))
            password:{
              required:true,
              minlength:8,
              maxlength:20,
            },
            confirm_password:{
              required:true,
              minlength:8,
              maxlength:20,
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
              maxlength:'Please enter password less than 20 digits',
            },
            confirm_password:{
              required:'Please enter confirm password.',
              minlength:'Please enter confirm password greater than 8 digits',
              maxlength:'Please enter confirm password less than 20 digits',
              equalTo : "Enter confirm password same as password !"
            }
          },
      });

      return validator;
    }


    function validate_password_form(){
      var validator = $("#myPasswordForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
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
            new_password:{
              required:'Please enter new password.',
              minlength:'Please enter new password greater than 8 digits',
              maxlength:'Please enter new password less than 20 digits',
            },
            confirm_password:{
              required:'Please enter confirm password.',
              minlength:'Please enter confirm password greater than 8 digits',
              maxlength:'Please enter confirm password less than 20 digits',
              equalTo : "Enter confirm password same as new password !"
            }
          },
      });

      return validator;
    }

    $('body').on("submit", "#myPasswordForm", function (e) {
      e.preventDefault();
      var validator = validate_password_form();
      
      if (validator.form() != false) {
        $(this).find('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('user.change-password.store')}}",
          type: "POST",
          data: new FormData($("#myPasswordForm")[0]),
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
              $(this).find('[type="submit"]').prop('disabled', false);
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
            $(this).find('[type="submit"]').prop('disabled', false);
          },
        });
      }
    });
  
  });
</script>
@endpush