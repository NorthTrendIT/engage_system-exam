@extends('layouts.master')

@section('title','Customer Delivery Schedule')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Delivery Schedule</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        @if(userrole() == 1)
        <a href="{{ route('customer-delivery-schedule.all-view') }}" class="btn btn-sm btn-primary mr-10">View All Schedule</a>
        @endif
        
        <a href="{{ route('customer-delivery-schedule.create') }}" class="btn btn-sm btn-primary">New Schedule</a>
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
           {{--  <div class="card-header border-0 pt-5">
              <h5>{{ isset($edit) ? "Update" : "Add" }} Details</h5>
            </div> --}}
            <div class="card-body">
              <div class="row">
                
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_territory" data-control="select2" data-hide-search="false" data-placeholder="Select territory" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-4 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-4  mt-5">
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
                              <th>Customer</th>
                              <th>Territory</th>
                              {{-- <th>Schedule Dates</th> --}}
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

      // $filter_search = $('[name="filter_search"]').val();
      // $filter_territory = $('[name="filter_territory"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('customer-delivery-schedule.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                // filter_search : $filter_search
                // filter_territory : $filter_territory
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'customer', name: 'customer'},
              {data: 'territory', name: 'territory'},
              // {data: 'date', name: 'date', orderable:false},
              {data: 'action', name: 'action', orderable:false},
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
      // render_table();
      $('#myTable').DataTable().search($('[name="filter_search"]').val()).draw();

      if($('[name="filter_territory"]').val() != ""){
        $('#myTable').DataTable().column(2).search($('[name="filter_territory"]').val()).draw();
      }
    });

    $(document).on('click', '.clear-search', function(event) {
      $('#myTable').dataTable().fnFilter('');
      $('[name="filter_search"]').val('');
      $('[name="filter_territory"]').val('').trigger('change');
      render_table();
    })

    // $(document).on('change', '[name="filter_territory"]', function(event) {
    //   event.preventDefault();
    //   $('#myTable').DataTable().column(2).search($(this).val()).draw();
    // });

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

    $('[name="filter_territory"]').select2({
      ajax: {
        url: "{{route('customer-delivery-schedule.get-territory')}}",
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
            results: $.map(response, function (item) {
                            return {
                              id: item.description,
                              text: item.description,
                            }
                        })
          };
        },
        cache: true
      },
    });

  })
</script>
@endpush
