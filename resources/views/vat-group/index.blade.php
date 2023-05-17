@extends('layouts.master')

@section('title','VatGroup')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">VatGroup</h1>
      </div>

      @if( in_array(userrole(), ['1']) )
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:" class="btn btn-sm btn-primary sync-vatgroup">Sync VatGroup</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->
      @endif

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
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                        <option value=""></option>
                        @foreach($company as $c)
                        <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                        @endforeach
                    </select>
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
                              <th>Code</th>
                              <th>Name</th>
                              <th>Status</th>
                              <th>Rate</th>
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

<style type="text/css">
  .other_filter_div{
    display: none;
  }
</style>
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
  $(document).ready(function() {
    render_table();

    function render_table(){
        var table = $("#myTable").DataTable();
        // table.DataTable().destroy();

        // table.DataTable({
        //   scrollX: true,
        //   scrollY: "800px",
        //   scrollCollapse: true,
        //   paging: true,
        //   fixedColumns:   {
        //     left: 3,
        //     right: 0
        //   },
        //   order: [],
        //   ajax: {
        //       'url': "{{ route('product.get-all') }}",
        //       'type': 'GET',
        //       headers: {
        //         'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //       },
        //       data:{
        //         filter_search : $filter_search,
        //         filter_company : $filter_company,
        //         filter_brand : $filter_brand,
        //         filter_status : $filter_status,
        //         filter_date_range : $filter_date_range,
        //         filter_product_category : $filter_product_category,
        //         filter_product_line : $filter_product_line,
        //         filter_product_class : $filter_product_class,
        //         filter_product_type : $filter_product_type,
        //         filter_product_application : $filter_product_application,
        //         filter_product_pattern : $filter_product_pattern,
        //       }
        //   },
        //   columns: [
        //       {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              
        //   ],
        //   drawCallback:function(){
        //     //   $(function () {
        //     //     $('[data-toggle="tooltip"]').tooltip()
        //     //     $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

        //     //     $("#myTable").DataTable().column(7).visible(false);
        //     //     $("#myTable").DataTable().column(10).visible(false);

        //     //     $product_category = $('[name="filter_product_category"]').find('option:selected').val().toLowerCase();
        //     //     if(jQuery.inArray($product_category, ["lubes","chem","tires"]) !== -1){
        //     //       $("#myTable").DataTable().column(7).visible(true);
        //     //     }

        //     //     if(jQuery.inArray($product_category, ["tires"]) !== -1){
        //     //       $("#myTable").DataTable().column(10).visible(true);
        //     //     }

        //     //   })
        //   },
        //   initComplete: function () {
        //   }
        // });


    }


    $(document).on('click', '.sync-vatgroup', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure you want to Sync VatGroup?',
        text: "Syncing process will run in background and it may take some time to sync all products Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('vatgroup.sync-vatgroup') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    filter_company : $('[name="filter_company"]').find('option:selected').val(),
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
 

  })
</script>
@endpush
