@extends('layouts.master')

@section('title','Customers Orders')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Orders for Customers</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('sales-specialist-orders.create') }}" class="btn btn-sm btn-primary">Create Order</a>
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
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_confirmation_status" data-placeholder= "Select confirmation status">
                    <option value=""></option>
                    <option value="P">Pending</option>
                    <option value="C">Confirmed</option>
                    <option value="ERR">Error</option>
                  </select>
                </div>

                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="false" data-placeholder="Select order status" data-allow-clear="true">
                    <option value=""></option>

                    @foreach(getOrderStatusArray() as $key => $value)
                      <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="table-responsive">
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>Customer Name</th>
                              <th>Confirmation Status</th>
                              <th>Order Status</th>
                              <th>Total</th>
                              <th>Created Date</th>
                              <th>Action</th>
                            </tr>
                          </thead>

                          <tbody>
                          </tbody>
                       </table>
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
</div>
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
      $filter_confirmation_status = $('[name="filter_confirmation_status"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('sales-specialist-orders.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_status : $filter_status,
                filter_confirmation_status : $filter_confirmation_status,
                filter_date_range : $filter_date_range,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'confirmation_status', name: 'confirmation_status'},
              {data: 'order_status', name: 'order_status', orderable:false,searchable:false},
              {data: 'total', name: 'total'},
              {data: 'created_at', name: 'created_at'},
              {data: 'action', name: 'action'},
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
      $('[name="filter_date_range"]').val('');
      $('[name="filter_status"]').val('').trigger('change');
      $('[name="filter_confirmation_status"]').val('').trigger('change');
      render_table();
    })

});
</script>
@endpush
