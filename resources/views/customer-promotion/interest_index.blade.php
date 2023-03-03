@extends('layouts.master')

@section('title','My Interested/ Not Interested Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">My Interested/ Not Interested Promotions</h1>
      </div>
      
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">

        <a href="{{ route('customer-promotion.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                <div class="col-md-6">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name = "filter_search">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
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
                              <th>Promotion</th>
                              <th>Is Interested ?</th>
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

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('customer-promotion.get-interest') }}",
              'type': 'GET',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                //filter_search : $filter_search,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'promotion', name: 'promotion'},
              {data: 'is_interested', name: 'is_interested'},
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
      $('#myTable').DataTable().search($('[name="filter_search"]').val()).draw();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('[name="filter_search"]').val('');
      render_table();
    });

    $(document).on('click', '.btn_interest', function(event) {
      event.preventDefault();
      $id = $(this).attr('data-id');
      $value = $(this).attr('data-value');

      Swal.fire({
        title: 'Are you sure want to do this ?',
        //text: "Once deleted, you will not be able to recover this record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('customer-promotion.store-interest') }}',
            method: "POST",
            data: {
                    promotion_id:$id,
                    is_interested:$value,
                    _token:'{{ csrf_token() }}'
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              render_table();
              toast_success(result.message);
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