@extends('layouts.master')

@section('title','Role')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Role</h1>
      </div>
    </div>
  </div>
  
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div>
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                <div class="row mb-5 d-flex justify-content-between">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Role Name<span class="asterisk">*</span></label>
                      <input type="text" class="form-control form-control-solid" placeholder="Enter role name" name="name" value="">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Select Access<span class="asterisk">*</span></label>
                      <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" name="all_module_access">
                        <option value="">Select Access </option>
                        <option value="1">All Menu Access</option>
                        <option value="0">Custom Menu Access</option>
                      </select>
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
                              <tr class="fw-bolder text-muted">
                                <th class="min-w-150px">Module</th>
                                <th class="min-w-120px">Add</th>
                                <th class="min-w-120px">Edit</th>
                                <th class="min-w-120px">Delete</th>
                                <th class="min-w-120px">View</th>
                              </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>

                              @foreach($modules as $module)
                              <tr>
                                <td>
                                  <span class="text-muted me-2 fs-7 fw-bold">{{ $module->title }}</span>
                                </td>
                                <td>
                                  <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="modules[{{ $module->id }}][add]">
                                  </label>
                                </td>
                                <td>
                                  <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="modules[{{ $module->id }}][edit]">
                                  </label>
                                </td>
                                <td>
                                  <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="modules[{{ $module->id }}][delete]">
                                  </label>
                                </td>
                                <td>
                                  <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="modules[{{ $module->id }}][view]">
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

    $('body').on("change", '[name="all_module_access"]', function (e) {
      
      if($(this).find('option:selected').val() == 1){
        $('input[type="checkbox"]').prop('checked', true);
      }else{
        $('input[type="checkbox"]').prop('checked', false);
      }

    });

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();
      if (validator.form() != false) {
        $.ajax({
          url: "{{route('role.store')}}",
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
            }
          },
          error: function () {
            toast_error("Something went to wrong !");
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
            },
            all_module_access:{
              required: true,
            }
          },
          messages: {
            name:{
              required: "Please enter role name.",
              maxlength:'Please enter role name less than 185 character',
            },
            all_module_access:{
              required: "Please select module access.",
            },
          },
      });

      return validator;
    }
  
  });
</script>
@endpush