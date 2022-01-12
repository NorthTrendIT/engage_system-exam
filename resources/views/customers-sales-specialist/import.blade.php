@extends('layouts.master')

@section('title','Upload Sales Specialist Assignment ')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Upload Sales Specialist Assignment </h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customers-sales-specialist.index') }}" class="btn btn-sm btn-primary">Back</a>
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
            {{-- <div class="card-header border-bottom pt-5">
              <h1 class="text-dark fw-bolder fs-3 my-1">Add Details</h1>
            </div> --}}
            <div class="card-body">
              <form method="post" id="myForm">
                @csrf
                
                <div class="row mb-5">

                  <div class="col-md-12">
                    <div class="form-group">
                      <label style="display: flex;align-items: center;justify-content: space-between;width: 100%;margin-bottom: 10px;">
                        <span>
                          Upload Excel<span class="asterisk">*</span>
                        </span>
                        <a href="{{ asset('assets/files/customer_sales_specialist_assignment_sample.xlsx') }}" class="btn btn-sm btn-info" download="">Sample Excel</a>

                      </label>
                      <input type="file" class="form-control form-control-solid" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                      </select>
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
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {

        Swal.fire({
          title: 'Are you sure you want to upload excel?',
          text: "Upload excel will run in background and it may take some time to add Data.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, do it!'
        }).then((result) => {
          if (result.isConfirmed) {

            $('[type="submit"]').prop('disabled', true);
            $.ajax({
              url: "{{route('customers-sales-specialist.import.store')}}",
              type: "POST",
              data: new FormData($("#myForm")[0]),
              async: false,
              processData: false,
              contentType: false,
              success: function (data) {
                if (data.status) {
                  toast_success(data.message)
                  // setTimeout(function(){
                  //   window.location.href = '{{ route('customers-sales-specialist.index') }}';
                  // },1500)
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

      }
    });

    function validate_form(){
      var validator = $("#myForm").validate({
          errorClass: "is-invalid",
          validClass: "is-valid",
          rules: {
            file:{
              required: true
            },
          },
          messages: {
            file:{
              required: "Please upload excel file.",
            }
          },
      });

      return validator;
    }

});
</script>
@endpush
