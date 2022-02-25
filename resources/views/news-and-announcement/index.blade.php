@extends('layouts.master')

@section('title','News & Announcement')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">News & Announcement</h1>
      </div>
      @if(@Auth::user()->role_id == 1)
      <div class="d-flex align-items-center py-1">
        <a href="{{ route('news-and-announcement.create') }}" class="btn btn-sm btn-primary">Create</a>
      </div>
      @endif
    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-body">
              <div class="row">

                @if(@Auth::user()->role_id == 1)
                @if(!empty($sap_connection))
                <div class="col-md-3 mt-5">
                    <select class="form-control form-control-lg form-control-solid" name="filter_sap_connection" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select Business Unit">
                        <option value=""></option>
                        @foreach($sap_connection as $item)
                            <option value="{{ $item->id }}"> {{ $item->company_name }} </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_module" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select Customer By">
                    <option value=""></option>
                    <option value="all">All</option>
                    <option value="brand">By Brand</option>
                    <option value="customer_class">By Class</option>
                    <option value="customer">By Customer</option>
                    <option value="sales_specialist">By Sales Specialist</option>
                    <option value="territory">By Territory</option>
                    <option value="market_sector">By Market Sector</option>
                  </select>
                </div>


                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_priority" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select priority">
                    <option value=""></option>
                    <option value="0">Normal</option>
                    <option value="1">Important</option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_type" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select type">
                    <option value=""></option>
                    <option value="A">Announcement</option>
                    <option value="N">News</option>
                  </select>
                </div>

                @if(@Auth::user()->role_id == 1)
                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Selecte date range" name="filter_date_range" id="kt_daterangepicker_1" readonly>
                  </div>
                </div>
                @endif

                <div class="col-md-6 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search title..." name="filter_search" autocomplete="off">
                  </div>
                </div>
                @else

                <div class="col-md-6 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search title..." name="filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_priority" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select priority">
                    <option value=""></option>
                    <option value="0">Normal</option>
                    <option value="1">Important</option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_type" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select type">
                    <option value=""></option>
                    <option value="A">Announcement</option>
                    <option value="N">News</option>
                  </select>
                </div>

                @endif

                <div class="col-md-3 mt-5">
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
                                <th>No</th>
                                @if(@Auth::user()->role_id == 1)
                                <th>Business Unit</th>
                                @endif
                                <th>Title</th>
                                <th>Type</th>
                                @if(@Auth::user()->role_id == 1)
                                <th>Period/Date</th>
                                @endif
                                <th>Customer</th>
                                <th>Priority</th>
                                @if(@Auth::user()->role_id == 1)
                                <th>User Name</th>
                                <th>Status</th>
                                @endif
                                <th>Action</th>
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
@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
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
      $filter_type = $('[name="filter_type"]').find('option:selected').val();
      $filter_module = $('[name="filter_module"]').find('option:selected').val();
      $filter_priority = $('[name="filter_priority"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').find('option:selected').val();
      $filter_sap_connection = $('[name="filter_sap_connection"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('news-and-announcement.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_type : $filter_type,
                filter_priority : $filter_priority,
                @if(@Auth::user()->role_id == 1)
                filter_module : $filter_module,
                filter_date_range : $filter_date_range,
                filter_sap_connection : $filter_sap_connection,
                @endif
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              @if(@Auth::user()->role_id == 1)
              {data: 'bussines_unit', name: 'bussines_unit'},
              @endif
              {data: 'title', name: 'title'},
              {data: 'type', name: 'type'},
              @if(@Auth::user()->role_id == 1)
              {data: 'date_period', name: 'date_period'},
              @endif
              {data: 'module', name: 'module'},
              {data: 'is_important', name: 'is_important', orderable: false},
              @if(@Auth::user()->role_id == 1)
              {data: 'user_name', name: 'user_name'},
              {data: 'status', name: 'status', orderable: false},
              @endif
              {data: 'action', name: 'action', orderable: false},
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
      $('[name="filter_type"]').val('').trigger('change');
      $('[name="filter_module"]').val('').trigger('change');
      $('[name="filter_priority"]').val('').trigger('change');
      $('[name="filter_date_range"]').val('').trigger('change');
      $('[name="filter_sap_connection"]').val('').trigger('change');
      render_table();
    });

    $(document).on('click', '.delete', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');

      Swal.fire({
        title: 'Are you sure?',
        text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: $url,
            method: "DELETE",
            data: {
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              render_table();
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    $(document).on('click', '.status', function(event) {
      event.preventDefault();
      $url = $(this).attr('data-url');

      Swal.fire({
        title: 'Are you sure want to change status?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: $url,
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              render_table();
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

});
</script>
@endpush
