@extends('layouts.master')

@section('title','Assign Sales Specialist')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Assign Salse Specialist</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('territory-sales-specialist.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                            <label>Sales Specialist<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="false" name="sales_specialist_id">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Territory<span class="asterisk">*</span></label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="false" name="territory_id[]" multiple="">
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

<script>
$(document).ready(function() {

    $('body').on("submit", "#myForm", function (e) {
      e.preventDefault();
      var validator = validate_form();

      if (validator.form() != false) {
        $('[type="submit"]').prop('disabled', true);
        $.ajax({
          url: "{{route('territory-sales-specialist.store')}}",
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
                  window.location.href = '{{ route('territory-sales-specialist.index') }}';
                @endif
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
            territory_id:{
              required: true
            },
            sales_specialist_id:{
              required: true
            }
          },
          messages: {
            territory_id:{
              required: "Please select territory.",
            },
            sales_specialist_id:{
              required: "Please select sales specialist.",
            }
          },
      });

      return validator;
    }

    $initialTerritory = [];

    @if(isset($edit))
        @foreach ($edit->territories as $t)
          @if (@$t->territory->id)
            var initialOption = {
                id: {{ $t->territory->id }},
                text: '{{ $t->territory->description }}',
                selected: true
            }
            $initialTerritory.push(initialOption);
          @endif
        @endforeach
    @endif

    $('[name="territory_id[]"]').select2({
      ajax: {
          url: "{{route('territory-sales-specialist.get-territory')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  _token: "{{ csrf_token() }}",
                  search: params.term
              };
          },
          processResults: function (response) {
            return {
              results:  $.map(response, function (item) {
                            return {
                              text: item.description,
                              id: item.id
                            }
                        })
            };
          },
          cache: true
      },
      placeholder: 'Select territory',
      allowClear: true,
      multiple: true,
      @if(isset($edit))
      data:$initialTerritory,
      @endif
    });

    $('[name="sales_specialist_id"]').select2({
      ajax: {
          url: "{{route('territory-sales-specialist.get-sales-specialist')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  _token: "{{ csrf_token() }}",
                  search: params.term
              };
          },
          processResults: function (response) {
            return {
              results:  $.map(response, function (item) {
                            return {
                              text: item.sales_specialist_name + " (Code: "+item.sales_employee_code+")",
                              id: item.id
                            }
                        })
            };
          },
          cache: true
      },
      placeholder: 'Select Sales Specialist',
      allowClear: true,
      multiple: false,
      @if(isset($edit))
      data:[{
            id: {{ $edit->id }},
            text: '{{ $edit->sales_specialist_name }}'+" (Code: {{ $edit->sales_employee_code }} )",
            selected: true
          }],
      @endif
    });

});
</script>
@endpush
