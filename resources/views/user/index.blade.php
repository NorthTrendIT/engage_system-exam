@extends('layouts.master')

@section('title','User')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">User</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        @if(userrole() == 1)
          {{-- <a href="javascript:" class="btn btn-sm btn-primary sync-sales-persons" style="display:none">Sync Sales Persons</a> --}}
        @endif
        <a href="{{ route('user.create') }}" class="btn btn-sm btn-primary create-btn">Create</a>
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
              <div class="row mt-5">
                
                <div class="col-md-2">
                  <select class="form-control form-control-lg form-control-solid filter_role" name="filter_role" data-control="select2" data-hide-search="true">
                    <option value="">Select role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true">
                    <option value="">Select status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search" autocomplete="off">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-4">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
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
                              <th>Role</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Email</th>
                              <th>Territory</th>
                              <th>Parent</th>
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
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
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
      $filter_role = $('[name="filter_role"]').find('option:selected').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 2,  
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('user.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_role : $filter_role,
                filter_status : $filter_status,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'role', name: 'role'},
              {data: 'first_name', name: 'first_name'},
              {data: 'last_name', name: 'last_name'},
              {data: 'email', name: 'email'},
              {data: 'territory', name: 'territory'},
              {data: 'parent', name: 'parent'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

                $role = $('[name="filter_role"]').find('option:selected').val();
                if($role == 2){
                  $("#myTable").DataTable().column(5).visible(true);
                  $("#myTable").DataTable().column(1).visible(false);
                } else {
                  $("#myTable").DataTable().column(1).visible(true);
                  $("#myTable").DataTable().column(5).visible(false);
                }
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
      $('[name="filter_role"]').val('').trigger('change');
      render_table();
    })

    $(document).on('change', '.filter_role', function(){
        $role = $('[name="filter_role"]').find('option:selected').val();
        if($role == 2){
          $('.sync-sales-persons').show();
          $('.create-btn').hide();
          $("#myTable").DataTable().column(5).visible(true);
          $("#myTable").DataTable().column(1).visible(false);
        } else {
          $('.sync-sales-persons').hide();
          $('.create-btn').show();
          $("#myTable").DataTable().column(1).visible(true);
          $("#myTable").DataTable().column(5).visible(false);
        }
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

    // $(document).on('click', '.status', function(event) { //remove enable/disable status
    //   event.preventDefault();
    //   $url = $(this).attr('data-url');

    //   Swal.fire({
    //     title: 'Are you sure want to change status?',
    //     //text: "Once deleted, you will not be able to recover this record!",
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonColor: '#3085d6',
    //     cancelButtonColor: '#d33',
    //     confirmButtonText: 'Yes, change it!'
    //   }).then((result) => {
    //     if (result.isConfirmed) {
    //       $.ajax({
    //         url: $url,
    //         method: "POST",
    //         data: {
    //                 _token:'{{ csrf_token() }}'
    //               }
    //       })
    //       .done(function(result) {
    //         if(result.status == false){
    //           toast_error(result.message);
    //         }else{
    //           toast_success(result.message);
    //           render_table();
    //         }
    //       })
    //       .fail(function() {
    //         toast_error("error");
    //       });
    //     }
    //   })
    // });

    $(document).on('click', '.sync-sales-persons', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to sync sales persons?',
        text: "Syncing process will run in background and it may take some time to sync all Sales Persons Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('sales-persons.sync-sales-persons') }}',
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


    $(document).on('click', '.copy_login_link', function(event) {
      event.preventDefault();

      // Create a "hidden" input
      var aux = document.createElement("input");

      // Assign it the value of the specified element
      aux.setAttribute("value", $(this).attr('data-href'));

      // Append it to the body
      document.body.appendChild(aux);

      // Highlight its content
      aux.select();

      // Copy the highlighted text
      document.execCommand("copy");

      // Remove it from the body
      document.body.removeChild(aux);

      toast_success("Link copied successfully !");
    });

    $(document).on("click", ".download_excel", function(e) {
            var url = "{{route('user.export')}}";
            var data = {};
            data.filter_search = $('[name="filter_search"]').val();
            data.filter_role = $('[name="filter_role"]').find('option:selected').val();
            data.filter_status = $('[name="filter_status"]').find('option:selected').val();
            url = url + '?data=' + btoa(JSON.stringify(data));
            window.location.href = url;
        });


  })


</script>
@endpush
