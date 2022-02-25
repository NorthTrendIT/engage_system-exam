@extends('layouts.master')

@section('title','Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotions</h1>
      </div>

      <div class="d-flex align-items-center py-1">
        <a href="{{ route('promotion.create') }}" class="btn btn-sm btn-primary">Create</a>
      </div>

    </div>
  </div>

  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">

            <div class="card-body">
              <div class="row">
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="true" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_promotion_type" data-control="select2" data-hide-search="false" data-placeholder="Select promotion type" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-2 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select status">
                    <option value=""></option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_scope" data-control="select2" data-hide-search="true" data-allow-clear="true" data-placeholder="Select customer group">
                    <option value=""></option>
                    <option value="A">All</option>
                    <option value="C">Customer</option>
                    <option value="CL">Class</option>
                    <option value="T">Territory</option>
                    <option value="SS">Sales Specialist</option>
                    <option value="B">Brand</option>
                    <option value="MS">Market Sector</option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Selecte date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-5 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mr-10">Clear</a>
                  @if(in_array(userrole(),[1]))
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
                  @endif
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive column-left-right-fix-scroll-hidden">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>Business Unit</th>
                              <th>Title</th>
                              <th>Code</th>
                              <th>Customer Group</th>
                              <th>Start Date</th>
                              <th>End Date</th>
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
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
  $(document).ready(function() {

    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_scope = $('[name="filter_scope"]').find('option:selected').val();
      $filter_promotion_type = $('[name="filter_promotion_type"]').find('option:selected').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 3,  
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('promotion.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_status : $filter_status,
                filter_scope : $filter_scope,
                filter_date_range : $filter_date_range,
                filter_promotion_type : $filter_promotion_type,
                filter_company : $filter_company,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'company', name: 'company'},
              {data: 'title', name: 'title'},
              {data: 'code', name: 'code'},
              {data: 'scope', name: 'scope'},
              {data: 'start_date', name: 'start_date'},
              {data: 'end_date', name: 'end_date'},
              {data: 'status', name: 'status', searchable: false},
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
      $('[name="filter_date_range"]').val('');
      $('[name="filter_status"]').val('').trigger('change');
      $('[name="filter_scope"]').val('').trigger('change');
      $('[name="filter_promotion_type"]').val('').trigger('change');
      $('[name="filter_company"]').val('').trigger('change');
      render_table();
    })

    $(document).on('click', '.sync-orders', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to sync orders?',
        text: "Syncing process will run in background and it may take some time to sync all Orders Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.sync-orders') }}',
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

    $('[name="filter_promotion_type"]').select2({
      ajax: {
          url: "{{route('promotion.get-promotion-type')}}",
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
                              text: item.title,
                              id: item.id
                            }
                        })
            };
          },
          cache: true
      },
    });


    @if(in_array(userrole(),[1]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('promotion.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_scope = $('[name="filter_scope"]').find('option:selected').val();
        data.filter_promotion_type = $('[name="filter_promotion_type"]').find('option:selected').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();
      
        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif


  })
</script>
@endpush
