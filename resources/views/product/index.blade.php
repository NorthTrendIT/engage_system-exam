@extends('layouts.master')

@section('title','Product')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product</h1>
      </div>

      @if( in_array(userrole(), ['1', '11']) )
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:" class="btn btn-sm btn-primary sync-products">Sync Products</a>
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
                    {{-- <option value=""></option> --}}
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5 filter_brand_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_groups as $product_group)
                    <option value="{{ $product_group->number }}">{{ $product_group->group_name }} @if(in_array(userrole(),[1,2]) && @$product_group->sap_connection->company_name) ({{ @$product_group->sap_connection->company_name }}) @endif</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_category as $key => $c)
                    <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                    <option value=""></option>
                    {{-- @foreach($product_line as $key => $l)
                    <option value="{{ $key }}">{{ $key }}</option>
                    @endforeach --}}
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_class" data-control="select2" data-hide-search="false" data-placeholder="Select product class" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_type" data-control="select2" data-hide-search="false" data-placeholder="Select product type" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-6 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_application" data-control="select2" data-hide-search="false" data-placeholder="Select product application" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_pattern" data-control="select2" data-hide-search="false" data-placeholder="Select product pattern" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                @if(in_array(userrole(),[1,11]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true">
                    <option value="">Select status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                  </select>
                </div>
                @endif

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                  @if(in_array(userrole(),[1,11]))
                  <button class="btn btn-success font-weight-bold download_excel " disabled>Export Excel</button>
                  @endif
                </div>

              </div>
              <div class="row mb-5 mt-5">
                <div class="col-md-12">
                  <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive column-left-right-fix-scroll-hidden">
                       <!--begin::Table-->
                       <table class="table table-bordered display nowrap" id="myTable" style="width:100%">
                          <!--begin::Table head-->
                          <thead>
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
                              <th id="before_price">Status</th>
                              <th>price1</th>
                              <th>price2</th>
                              <th>price3</th>
                              <th>price4</th>
                              <th>price5</th>
                              <th>price6</th>
                              <th>price7</th>
                              <th>price8</th>
                              <th>price9</th>
                              <th>price10</th>
                              <th>price11</th>
                              <th>price12</th>
                              <th>price13</th>
                              <th>price14</th>
                              <th>price15</th>
                              <th>price16</th>
                              <th>price17</th>
                              <th>price18</th>
                              <th>price19</th>
                              <th>price20</th>
                              {{-- <th>Online Price</th>
                              <th>Commercial Price</th>
                              <th>SRP</th>
                              <th>DLP</th>
                              <th>Gross Price</th>
                              <th>LP</th> --}}
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
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script>
  $(document).ready(function() {

    var sap_priceLists = [];
    render_table();
    function render_table(){
      $('.download_excel').attr('disabled', 'disabled');
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
      $filter_product_class = $('[name="filter_product_class"]').find('option:selected').val();
      $filter_product_type = $('[name="filter_product_type"]').find('option:selected').val();
      $filter_product_application = $('[name="filter_product_application"]').find('option:selected').val();
      $filter_product_pattern = $('[name="filter_product_pattern"]').find('option:selected').val();

      var hide_targets = [];
      @if(!in_array(userrole(),[1,11]))
        hide_targets = [];
      @endif

      table = table.DataTable({
          processing: true,
          serverSide: true,
          fixedColumns:   {
            left: 2,
            right: 0
          },
          fixedHeader: true,
          // paging: false,
          scrollCollapse: true,
          scrollX: true,
          scrollY: 400,
          order: [],
          ajax: {
              'url': "{{ route('product.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_company : $filter_company,
                filter_brand : $filter_brand,
                filter_status : $filter_status,
                filter_date_range : $filter_date_range,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
                filter_product_class : $filter_product_class,
                filter_product_type : $filter_product_type,
                filter_product_application : $filter_product_application,
                filter_product_pattern : $filter_product_pattern,
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
              {data: 'price1', name: 'price1',orderable:false,searchable:false},
              {data: 'price2', name: 'price2',orderable:false,searchable:false},
              {data: 'price3', name: 'price3',orderable:false,searchable:false},
              {data: 'price4', name: 'price4',orderable:false,searchable:false},
              {data: 'price5', name: 'price5',orderable:false,searchable:false},
              {data: 'price6', name: 'price6',orderable:false,searchable:false},
              {data: 'price7', name: 'price7',orderable:false,searchable:false},
              {data: 'price8', name: 'price8',orderable:false,searchable:false},
              {data: 'price9', name: 'price9',orderable:false,searchable:false},
              {data: 'price10', name: 'price10',orderable:false,searchable:false},
              {data: 'price11', name: 'price11',orderable:false,searchable:false},
              {data: 'price12', name: 'price12',orderable:false,searchable:false},
              {data: 'price13', name: 'price13',orderable:false,searchable:false},
              {data: 'price14', name: 'price14',orderable:false,searchable:false},
              {data: 'price15', name: 'price15',orderable:false,searchable:false},
              {data: 'price16', name: 'price16',orderable:false,searchable:false},
              {data: 'price17', name: 'price17',orderable:false,searchable:false},
              {data: 'price18', name: 'price18',orderable:false,searchable:false},
              {data: 'price19', name: 'price19',orderable:false,searchable:false},
              {data: 'price20', name: 'price20',orderable:false,searchable:false},
              // {data: 'online_price', name: 'online_price',orderable:false,searchable:false},
              // {data: 'commercial_price', name: 'commercial_price',orderable:false,searchable:false},
              // {data: 'srp_price', name: 'srp_price',orderable:false,searchable:false},
              // {data: 'rdlp_price', name: 'rdlp_price',orderable:false,searchable:false},
              // {data: 'rdlp2_price', name: 'rdlp2_price',orderable:false,searchable:false},
              // {data: 'lp_price', name: 'lp_price',orderable:false,searchable:false},
              {data: 'action', name: 'action'},
          ],
          aoColumnDefs: [{ "bVisible": false, "aTargets": hide_targets }],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

                $("#myTable").DataTable().column(7).visible(false);
                $("#myTable").DataTable().column(10).visible(false);

                $product_category = $('[name="filter_product_category"]').find('option:selected').val().toLowerCase();
                if(jQuery.inArray($product_category, ["lubes","chem","tires"]) !== -1){
                  $("#myTable").DataTable().column(7).visible(true);
                }

                if(jQuery.inArray($product_category, ["tires"]) !== -1){
                  $("#myTable").DataTable().column(10).visible(true);
                }

              })
          },
          initComplete: function () {
          }
        });
        
        $.get("{{ route('product.fetchPriceLists') }}", {filter_company : $filter_company}, function(data, status){
          
          var strt_hdr = 13; //start of dynamic header
          var hide_columns = [];
          sap_priceLists = [];
          for(x in data){
            $(table.column(strt_hdr).header()).text(data[x]);
            sap_priceLists.push({no: x, name: data[x]});
            strt_hdr++;
          }
          
          var columns = (20 - (strt_hdr - 13));
          for (let i = 0; i < columns; i++) {
            hide_columns.push(strt_hdr + i);
          }

          table.columns(hide_columns).visible( false );
          // $(table.column(1).header()).text('My title');
          $('.download_excel').removeAttr('disabled');
        });

        // $("#myTable").stickyTable({overflowy: true});

    }

    $(document).on('click', '.search', function(event) {
      render_table();
    });

    $(document).on('click', '.clear-search', function(event) {
      // $('[name="filter_search"]').val('');
      // $('[name="filter_date_range"]').val('');
      // $('[name="filter_status"]').val('').trigger('change');
      // $('[name="filter_brand"]').val('').trigger('change');
      // $('[name="filter_product_category"]').val('').trigger('change');
      // $('[name="filter_product_line"]').val('').trigger('change');
      // $('[name="filter_company"]').val('').trigger('change');

      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

    $(document).on('click', '.sync-products', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure you want to Sync Products?',
        text: "Syncing process will run in background and it may take some time to sync all products Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('product.sync-products') }}',
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

    @if(in_array(userrole(),[1,11]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('product.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
        data.filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
        data.filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();
        data.priceLists = sap_priceLists;

        // console.log(data.priceLists);
        // console.log((JSON.stringify(data)));
        // console.log(btoa(JSON.stringify(data)));
        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif


    $(document).on('change', '[name="filter_company"]', function(event) {
      event.preventDefault();
      $('[name="filter_brand"]').val('').trigger('change');

      if($(this).find('option:selected').val() != ""){
        $('.filter_brand_div').show();
      }else{
        $('.filter_brand_div').hide();
        $('.other_filter_div').hide();
      }

    });

    $(document).on('change', '[name="filter_brand"]', function(event) {
      event.preventDefault();
      $('[name="filter_product_category"]').val('').trigger('change');
      $('[name="filter_product_line"]').val('').trigger('change');
      $('[name="filter_product_class"]').val('').trigger('change');
      $('[name="filter_product_type"]').val('').trigger('change');
      $('[name="filter_product_application"]').val('').trigger('change');
      $('[name="filter_product_pattern"]').val('').trigger('change');

      if($(this).find('option:selected').val() != ""){
        $('.other_filter_div').show();
      }else{
        $('.other_filter_div').hide();
      }
    });


    $('[name="filter_brand"]').select2({
      ajax: {
        url: "{{route('product.get-brand-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                        return {
                          text: item.group_name,
                          id: item.number
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_category"]').select2({
      ajax: {
        url: "{{route('product.get-product-category-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_line"]').select2({
      ajax: {
        url: "{{route('product.get-product-line-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_class"]').select2({
      ajax: {
        url: "{{route('product.get-product-class-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_type"]').select2({
      ajax: {
        url: "{{route('product.get-product-type-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_application"]').select2({
      ajax: {
        url: "{{route('product.get-product-application-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


    $('[name="filter_product_pattern"]').select2({
      ajax: {
        url: "{{route('product.get-product-pattern-data')}}",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            _token: "{{ csrf_token() }}",
            search: params.term,
            sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
            items_group_code: $('[name="filter_brand"]').find('option:selected').val(),
          };
        },
        processResults: function (response) {
          return {
            results:  response
          };
        },
        cache: true
      },
    });


  })
</script>
@endpush
