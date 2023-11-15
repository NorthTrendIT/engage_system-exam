@extends('layouts.master')

@section('title','Benefits')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Benefits</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('product.benefits') }}" class="btn btn-sm btn-primary sync-products">Create</a>
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

                <div class="col-md-2 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    {{-- <option value=""></option> --}}
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-2 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true">
                    <option value="">Select status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary btn-sm px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark btn-sm font-weight-bold clear-search mx-2">Clear</a>
                  {{-- @if(in_array(userrole(),[1,11]))
                  <button class="btn btn-success font-weight-bold download_excel " disabled>Export Excel</button>
                  @endif --}}
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="d-flex flex-row-reverse">
                    <button class="btn btn-dark btn-sm" id="product-benefits-assign">Submit</button>
                  </div>
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-bordered display nowrap" id="myTable" style="width:100%">
                          <!--begin::Table head-->
                          <thead class="bg-dark text-white">
                            <tr>
                              <th>No.</th>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Brand</th>
                              <th>Product Category</th>
                              <th>Business Unit</th>
                              <th>Product Line</th>
                              <th>Product Class</th>
                              <th>Product Type</th>
                              <th>Product Application</th>
                              <th>Product Pattern</th>
                              <th>Date</th>
                              <th>Status</th>
                              @foreach($benefits as $b)
                                <th>{{ $b }}</th>
                              @endforeach
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
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">

@endpush

@push('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
  $(document).ready(function() {

    var table = [];
    render_table();
    function render_table(){
      $("#myTable th").addClass('bg-dark text-white');
    //   $('.download_excel').attr('disabled', 'disabled');
      table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').val();

      var hide_targets = [];
      @if(!in_array(userrole(),[1,11]))
        hide_targets = [];
      @endif

      table = table.DataTable({
          bFilter: false, bInfo: false, bPaginate: false, bSort: false,
          bLengthChange: false,
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "400px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:{
            left: 2,
            right: 10
          },
          order: [],
          ajax: {
              'url': "{{ route('product.get-all-benefits') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_company : $filter_company,
                filter_status : $filter_status,
                filter_date_range : $filter_date_range,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'item_code', name: 'item_code'},
              {data: 'item_name', name: 'item_name'},
              {data: 'brand', name: 'brand'},
              {data: 'u_tires', name: 'u_tires'},
              {data: 'company', name: 'company'},
              {data: 'u_item_line', name: 'u_item_line'},
              {data: 'item_class', name: 'item_class'},
              {data: 'u_item_type', name: 'u_item_type'},
              {data: 'u_item_application', name: 'u_item_application'},
              {data: 'u_pattern2', name: 'u_pattern2'},
              {data: 'created_date', name: 'created_date'},
              {data: 'status', name: 'status'},
              {data: 'bnf1', name: 'bnf1',orderable:false,searchable:false},
              {data: 'bnf2', name: 'bnf2',orderable:false,searchable:false},
              {data: 'bnf3', name: 'bnf3',orderable:false,searchable:false},
              {data: 'bnf4', name: 'bnf4',orderable:false,searchable:false},
              {data: 'bnf5', name: 'bnf5',orderable:false,searchable:false},
              {data: 'bnf6', name: 'bnf6',orderable:false,searchable:false},
              {data: 'bnf7', name: 'bnf7',orderable:false,searchable:false},
              {data: 'bnf8', name: 'bnf8',orderable:false,searchable:false},
              {data: 'bnf9', name: 'bnf9',orderable:false,searchable:false},
              {data: 'bnf10', name: 'bnf10',orderable:false,searchable:false},
          ],
          aoColumnDefs: [{ "bVisible": false, "aTargets": hide_targets }],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

                $("#myTable").DataTable().column(7).visible(false);
                $("#myTable").DataTable().column(10).visible(false);

              })
          },
          initComplete: function () {
          }
        });
        
        // $("#myTable").stickyTable({overflowy: true});
    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    });

    $(document).on('click', '#product-benefits-assign', function(e){
      var bnf_assignment = [];
   
      $('tbody').find('tr').each(function(){
          var row  = $(this);
		     	var data = table.row(row).data();
          var checked_bnf = $(row).find('input[type=checkbox]:checked');
          var bnf_arr = [];

          // if(checked_bnf.length > 0){
            checked_bnf.each(function(){
              bnf_arr.push($(this).val());
            });

            bnf_assignment.push({
                                  product_id: data.id,
                                  benefit_ids: bnf_arr.join(", ") 
                                });
          // }
      });
      // var with_s = (bnf_assignment.length > 1) ? 's' : '';
      Swal.fire({
        title: 'Confirmation',
        text: "Are you sure you want to update Product Benefits Assignment?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ route('benefits.assignment.add') }}",
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    bnf_assignment: bnf_assignment,
                    }
          })
          .done(function(result) {
              if(result.status == false){
                  toast_error(result.message);
              }else{
                  toast_success(result.message);
              }
          })
          .fail(function() {
              toast_error("error");
          });
        }
      }); //swal close
  
    });

    $('tbody').on('click', 'input', function(e){
      $(this).attr('wew', 'test');
    })


    // @if(in_array(userrole(),[1,11]))
    //   $(document).on("click", ".download_excel", function(e) {
    //     var url = "{{route('product.export')}}";

    //     var data = {};
    //     data.filter_search = $('[name="filter_search"]').val();
    //     data.filter_date_range = $('[name="filter_date_range"]').val();
    //     data.filter_status = $('[name="filter_status"]').find('option:selected').val();
    //     data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
    //     data.filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
    //     data.filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
    //     data.filter_company = $('[name="filter_company"]').find('option:selected').val();
    //     data.priceLists = sap_priceLists;

    //     url = url + '?data=' + btoa(JSON.stringify(data));

    //     window.location.href = url;
    //   });
    // @endif
        


  })
</script>
@endpush
