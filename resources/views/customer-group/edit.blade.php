@extends('layouts.master')

@section('title','Customer Group')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Group</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer-group.index') }}" class="btn btn-sm btn-primary">Back</a>
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
              <h1 class="text-dark fw-bolder fs-3 my-1">Update Details</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                  <input type="hidden" name="id" value="{{ @$edit->id }}">


                <div class="row mb-5">                  
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Email<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter emails" name="email" value="{{$edit->emails}}">
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
      //var validator = validate_form();
      
      //if (validator.form() != false) {
        //$(this).find('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('customer-group.store')}}",
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
                  window.location.href = '{{ route('customer-group.index') }}';
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
      //}
    });

    // function validate_form(){
    //   var validator = $("#myForm").validate({
    //       errorClass: "is-invalid",
    //       validClass: "is-valid",
    //       rules: {
    //         first_name:{
    //           required: true,
    //           maxlength: 185,
    //         },
    //         last_name:{
    //           required: true,
    //           maxlength: 185,
    //         },
    //         email:{
    //           required:true,
    //           maxlength: 185,
    //         },
    //         role_id:{
    //           required:true,
    //         },
    //         department_id:{
    //           required:true,
    //         },
    //         @if(!isset($edit))
    //         password:{
    //           required:true,
    //           minlength:8,
    //           maxlength:20,
    //         },
    //         confirm_password:{
    //           required:true,
    //           minlength:8,
    //           maxlength:20,
    //           equalTo : "#password"
    //         }
    //         @endif
    //       },
    //       messages: {
    //         first_name:{
    //           required: "Please enter first name.",
    //           maxlength:'Please enter first name less than 185 character',
    //         },
    //         last_name:{
    //           required: "Please enter last name.",
    //           maxlength:'Please enter last name less than 185 character',
    //         },
    //         email:{
    //           required:"Please enter email.",
    //           maxlength:'Please enter email less than 185 character',
    //         },
    //         email:{
    //           required:"Please select role.",
    //         },
    //         password:{
    //           required:'Please enter password.',
    //           minlength:'Please enter password greater than 8 digits',
    //           maxlength:'Please enter password less than 20 digits',
    //         },
    //         confirm_password:{
    //           required:'Please enter confirm password.',
    //           minlength:'Please enter confirm password greater than 8 digits',
    //           maxlength:'Please enter confirm password less than 20 digits',
    //           equalTo : "Enter confirm password same as password !"
    //         }
    //       },
    //   });

    //   return validator;
    // }
  
  });
</script>
@endpush