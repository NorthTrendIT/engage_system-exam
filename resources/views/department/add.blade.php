@extends('layouts.master')

@section('title','Department')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Department</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('department.index') }}" class="btn btn-sm btn-primary">Back</a>
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

                <div class="row mb-5 d-flex justify-content-between">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Department Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter department name" name="name" @if(isset($edit)) value="{{ $edit->name }}" @endif >
                    </div>
                  </div>

                </div>

                <div class="row mb-5 mt-10">
                  <div class="col-md-12">
                    <div class="form-group">
                      
                      <!--begin::Table container-->
                      <div class="table-responsive">
                         <!--begin::Table-->
                         <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                              <tr class="fw-bolder text-muted d-flex justify-content-between">
                                <th class="min-w-150px">Roles</th>
                                <th class="min-w-120px">Add to Department ?</th>
                              </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>

                              @foreach($roles as $role)
                              <tr class="d-flex justify-content-between">
                                <td>
                                  <span class="text-muted me-2 fs-7 fw-bold">{{ $role->name }}</span>
                                </td>
                                <td>
                                  <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="roles[{{ $role->id }}]" @if(isset($department_roles) && in_array($role->id, $department_roles)) checked="" @endif >
                                  </label>
                                </td>
                              </tr>
                              @endforeach

                            </tbody>
                            <!--end::Table body-->
                         </table>
                         <!--end::Table-->
                      </div>
                      <!--end::Table container-->

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
    
    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      
      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('department.store')}}",
          type: "POST",
          data: new FormData($("#myForm")[0]),
          async: false,
          processData: false,
          contentType: false,
          success: function (data) {
            if (data.status) {
              toast_success(data.message)
              setTimeout(function(){
                window.location.href = '{{ route('department.index') }}';
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
            name:{
              required: true,
              maxlength: 185,
            }
          },
          messages: {
            name:{
              required: "Please enter department name.",
              maxlength:'Please enter department name less than 185 character',
            }
          },
      });

      return validator;
    }
  
  });
</script>
@endpush