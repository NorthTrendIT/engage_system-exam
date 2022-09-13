@extends('layouts.master')

@section('title','Profile')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Profile</h1>
      </div>
    </div>
  </div>

  @if(Session::has('profile_error_message') || Auth::user()->first_login == 1)
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="alert alert-custom alert-danger" role="alert">
            <div class="alert-text">You have to change your temporary email address to your actual email address in order to access the system.</div>
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
            <div class="card-header pt-5 border-bottom">
              <h1 class="text-dark fw-bolder fs-3 my-1">Update Details</h1>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>First Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter first name" name="first_name" value="{{ @Auth::user()->first_name ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Last Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter last name" name="last_name" value="{{ @Auth::user()->last_name ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Email<span class="asterisk">*</span></label>
                      <input type="email" class="form-control form-control-solid" placeholder="Enter email" name="email" value="{{ @Auth::user()->email ?? "" }}">
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Profile</label>
                      <input type="file" class="form-control form-control-solid" name="profile" data-allowed-file-extensions="jpeg jpg png eps bmp tif tiff webp pdf doc docx xls xlsx ppt pptx odt ods">
                    </div>
                  </div>
                </div>

                @if(get_login_user_profile())
                  <div class="row mt-10 mb-10">
                    <div class="col-md-12">
                      <div class="form-group">
                        <a href="{{ get_login_user_profile() }}" class="fancybox"><img src="{{ get_login_user_profile() }}" height="100" width="100"></a>
                      </div>
                    </div>
                  </div>
                @endif

                @if(Auth::user()->role_id == 4)
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Overdue Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$totalOverdueAmount) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Outstanding Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->current_account_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Open Order Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->open_orders_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Exposure Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->current_account_balance + @$data->open_orders_balance) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Credit Limit<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->credit_limit) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>

                @if(@$data->credit_limit > (@$data->current_account_balance + @$data->open_orders_balance))
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Available Credit Limit<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(@$data->credit_limit - ($data->current_account_balance + @$data->open_orders_balance)) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>
                @else
                <div class="row mb-5">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Total Overdue Amount<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" value="{{ number_format(($data->current_account_balance + @$data->open_orders_balance) - @$data->credit_limit) ??  "" }}" readonly>
                    </div>
                  </div>
                </div>
                @endif
                @endif

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
          url: "{{route('profile.store')}}",
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
              profile:{
                required: false,
                maxsize: 10000000,
                extension: 'jpeg|jpg|png|eps|bmp|tif|tiff|webp',
              },
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
              profile:{
                extension: "Allow only .jpeg .jpg .png .eps .bmp .tif .tiff .webp files.",
                maxsize: "File size must not exceed 10MB.",
              },
          },
      });

      return validator;
    }

  });
</script>
@endpush
