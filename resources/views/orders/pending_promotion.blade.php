@extends('layouts.master')

@section('title','Pending Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Pending Promotions</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-right py-1">
        <a href="javascript:;" class="btn btn-sm btn-info push-all-promotion">Push All</a>
      </div>
      <!--end::Actions-->

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
           {{--  <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div> --}}
            <div class="card-body">
              <div class="row mt-5">
                <div class="col-md-3">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search" autocomplete="off">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <!-- <div class="col-md-3">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-placeholder="Select status" data-allow-clear="true">
                    <option value=""></option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </div> -->

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
                              <th>No</th>
                              <th>Promotion</th>
                              @if(Auth::id() == 1)
                              <th>Customer</th>
                              @endif
                              <th>Date Time</th>
                              <th>Status</th>
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

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('orders.get-all-pending-promotion') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_status : $filter_status,
              }
          },
          columns: [
              {data: 'DT_RowIndex'},
              {data: 'promotion', name: 'promotion'},
              @if(Auth::id() == 1)
              {data: 'user', name: 'user'},
              @endif
              {data: 'created_at', name: 'created_at'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
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
      render_table();
    })

    $(document).on('click', '.push-in-sap', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      Swal.fire({
        title: 'Are you sure want to do this?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('customer-promotion.order.push-in-sap') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    id: id,
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
              setTimeout(function(){
                window.location.reload();
              },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

    $(document).on('click', '.push-all-promotion', function(event) {
      event.preventDefault();
      var id = $(this).data('id');
      Swal.fire({
        title: 'Are you sure want to push all promotion?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.push-all-promotion') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
            //   setTimeout(function(){
            //     window.location.reload();
            //   },500)
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });

  })
</script>
@endpush
