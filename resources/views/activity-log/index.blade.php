@extends('layouts.master')

@section('title','Activity Log')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Activity Log</h1>
      </div>
    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-body">
              <div class="row mt-5">
                <div class="col-md-3">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-2">
                  <select class="form-control form-control-lg form-control-solid filter_company" name="filter_company" data-control="select2" data-hide-search="false" data-placeholder="Select company" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($company as $c)
                    <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-2">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-placeholder="Select status" data-allow-clear="true">
                    <option value=""></option>
                    <option value="in progress">In progress</option>
                    <option value="completed">Completed</option>
                    <option value="error">Error</option>
                  </select>
                </div>

                <div class="col-md-2">
                  <select class="form-control form-control-lg form-control-solid" name="filter_type" data-control="select2" data-hide-search="true" data-placeholder="Select type" data-allow-clear="true">
                    <option value=""></option>
                    <option value="O">OMS</option>
                    <option value="S">SAP</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>Type</th>
                              <th>Activity</th>
                              <th>Company</th>
                              <th>User Name</th>
                              <th>Status</th>
                              <th>IP Address</th>
                              <th>Date & Time</th>
                            </tr>
                          </thead>
                          <!--end::Table head-->
                          <!--begin::Table body-->
                          <tbody>

                          </tbody>
                          <!--end::Table body-->
                       </table>
                       <!--end::Table-->
                    </div>
                    <!--end::Table container-->

                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!--begin::Modal - Error Data-->
<div class="modal fade" id="kt_modal_error_data" tabindex="-1" aria-hidden="true">
<!--begin::Modal dialog-->
<div class="modal-dialog modal-dialog-top mw-900px">
  <!--begin::Modal content-->
  <div class="modal-content">
    <!--begin::Modal header-->
    <div class="modal-header">
      <!--begin::Modal title-->
      <h2>Error Data</h2>
      <!--end::Modal title-->
      <!--begin::Close-->
      <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
        <span class="svg-icon svg-icon-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="black" />
            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black" />
          </svg>
        </span>
        <!--end::Svg Icon-->
      </div>
      <!--end::Close-->
    </div>
    <!--end::Modal header-->
    <!--begin::Modal body-->
    <div class="modal-body">
      <!--begin::Stepper-->
      <div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid" id="kt_modal_error_data_stepper">
        
        <!--begin::Content-->
        <div class="flex-row-fluid">
          <p id="error_data_text">
            
          </p>
        </div>
        <!--end::Content-->

      </div>
      <!--end::Stepper-->
    </div>
    <!--end::Modal body-->
  </div>
  <!--end::Modal content-->
</div>
<!--end::Modal dialog-->
</div>
<!--end::Modal - Error Data-->

@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>


  $(document).ready(function() {
    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_type = $('[name="filter_type"]').find('option:selected').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          lengthMenu: [50, 100, 250, 500],
          pageLength: 50,
          ajax: {
              'url': "{{ route('activitylog.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_status : $filter_status,
                filter_type : $filter_type,
                filter_company : $filter_company,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'type', name: 'type'},
              {data: 'activity', name: 'activity'},
              {data: 'company', name: 'company'},
              {data: 'user_name', name: 'user_name'},
              {data: 'status', name: 'status'},
              {data: 'ip_address', name: 'ip_address',orderable:false,searchable:false},
              {data: 'date_time', name: 'date_time'},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');
              })
          },
          initComplete: function () {
          }
        });
    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('[name="filter_search"]').val('');
      $('[name="filter_status"]').val('').trigger('change');
      $('[name="filter_type"]').val('').trigger('change');
      $('[name="filter_company"]').val('').trigger('change');
      render_table();
    })

    $(document).on("click", ".error-status", function (e) {
      var id = $(this).data("error-data");
      var textedJson = JSON.stringify(id, undefined, 4);
      $('#error_data_text').html(textedJson);
      $('#kt_modal_error_data').modal('show');
    });
  })
</script>
@endpush
