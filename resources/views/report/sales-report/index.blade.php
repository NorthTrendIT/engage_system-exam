@extends('layouts.master')

@section('title', $title)

@section('content')
<style type="text/css">
  .input-icon.engage_transaction {
    display: flex;
    align-items: center;
}
.input-icon.engage_transaction span  {
  
  padding-left: 10px;
}
.input-icon.engage_transaction input {
  width :20px;
  height: 20px;
}
</style>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">{{$title}}</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:void(0)" class="btn btn-sm btn-success generate-report" style="margin-right: 5px">Generate Report</a>
        <a href="{{ route('report.index') }}" class="btn btn-sm btn-primary">Back</a>
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
              <div class="row" style="align-items: center;">
                <div class="col-md-3">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div>
                @if(in_array(userrole(),[1,6]))
                <div class="col-md-3">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif   
                <div class="col-md-3 ">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                <div class="col-md-3 filter_brand_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                @if(in_array(userrole(),[1]))
                <div class="col-md-3 mt-5 filter_brand_div" style="display:none;">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_manager" data-allow-clear="true" data-placeholder="Select Manager">
                    <option value=""></option>
                    @foreach($managers as $m)
                      <option value="{{ $m->id }}">{{ $m->first_name.' '.$m->last_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif

                <!-- Customer Class -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer_class" data-control="select2" data-hide-search="false" data-placeholder="Select customer class" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @if(in_array(userrole(),[1]) || in_array(userrole(),[6]))
                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif
                <!-- Market Sector -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_market_sector" data-control="select2" data-hide-search="false" data-placeholder="Select market sector" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Market Sub Sector -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_market_sub_sector" data-control="select2" data-hide-search="false" data-placeholder="Select market sub sector" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_category" data-control="select2" data-hide-search="false" data-placeholder="Select product category" data-allow-clear="true">
                    <option value=""></option>
                    
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_line" data-control="select2" data-hide-search="false" data-placeholder="Select product line" data-allow-clear="true">
                    <option value=""></option>
                    
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


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_application" data-control="select2" data-hide-search="false" data-placeholder="Select product application" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_product_pattern" data-control="select2" data-hide-search="false" data-placeholder="Select product pattern" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1"  readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-3 mt-5 d-none">
                  <div class="input-icon engage_transaction">
                    <input type="checkbox" class="" name = "engage_transaction" id="engage_transaction" value="1" checked>
                    <span>
                      Engage Transactions Only
                    </span>
                  </div>
                </div>

                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                  <a href="#" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-sm-5 mb-md-0">
                  <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Quantity: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_total_quantity_count text-primary ">0</span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4 mb-sm-5 mb-md-0 d-none">
                  <div class="bg-light-chocolate px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Price: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_total_price_count text-primary ">0</span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="bg-light-red px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Price After VAT: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_total_price_after_vat_count text-primary ">0</span>
                    </h6>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
              <h5 class="text-info">List Of Records</h5>
            </div>
            <div class="card-body">
              <div class="row mb-5">
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
                              <th>Invoice #</th>
                              <th>Date</th>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Brand</th>
                              <th>Business Unit</th>
                              <th>Total Quantity</th>
                              <th>UOM</th>
                              {{-- <th>Total Price</th>
                              <th>Total Price After VAT</th> --}}
                              <th>Unit Price</th>
                              <th>Net Amount</th>
                              <th>Status</th>
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
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">

<style type="text/css">
  .other_filter_div{
    display: none;
  }
</style>
@endpush

@push('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
  $(document).ready(function() {

    render_table([]);

    $(document).on('click','.generate-report, .search',function(e){
      $stat = check_hasFilter();
      ($stat === false)? '' : render_data();
    });

    function check_hasFilter(){
      if(($('[name="filter_company"]').val() == null || $('[name="filter_company"]').val() === "") && $('[name="filter_customer"]').val() === ""){
        Swal.fire('Please select Business unit or Customer first.');
        return false;
      }
    }

    function render_data(){

      $('.loader_img').show();
      $('.grand_total_of_total_quantity_count').text("");
      $('.grand_total_of_total_price_count').text("");
      $('.grand_total_of_total_price_after_vat_count').text("");

      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_customer = $('[name="filter_customer"]').val();
      $filter_manager = $('[name="filter_manager"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      $filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
      $filter_product_class = $('[name="filter_product_class"]').find('option:selected').val();
      $filter_product_type = $('[name="filter_product_type"]').find('option:selected').val();
      $filter_product_application = $('[name="filter_product_application"]').find('option:selected').val();
      $filter_product_pattern = $('[name="filter_product_pattern"]').find('option:selected').val();

      $filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
      $filter_market_sub_sector = $('[name="filter_market_sub_sector"]').find('option:selected').val();
      $engage_transaction = $('[name="engage_transaction"]').val();

      $overdue = 'No';
      @if($title === 'Overdue Sales Invoice Report')
        $overdue = 'Yes';
      @endif

      $.ajax({
        url: '{{ route('reports.sales-report.get-all') }}',
        method: "POST",
        data: {
                _token:'{{ csrf_token() }}',
                filter_search : $filter_search,
                filter_company : $filter_company,
                filter_customer : $filter_customer,
                filter_brand : $filter_brand,
                filter_status : $filter_status,
                filter_date_range : $filter_date_range,
                filter_product_category : $filter_product_category,
                filter_product_line : $filter_product_line,
                filter_product_class : $filter_product_class,
                filter_product_type : $filter_product_type,
                filter_product_application : $filter_product_application,
                filter_product_pattern : $filter_product_pattern,

                filter_customer_class : $filter_customer_class,
                filter_sales_specialist : $filter_sales_specialist,
                filter_market_sector : $filter_market_sector,
                filter_market_sub_sector : $filter_market_sub_sector,

                filter_manager : $filter_manager,
                engage_transaction : $engage_transaction,
                overdue: $overdue,
              }
      })
      .done(function(result) {
        if(result.status){
          toast_success(result.message);
          
          $('.loader_img').hide();

          $('.grand_total_of_total_quantity_count').text(result.data.grand_total_of_total_quantity);
          $('.grand_total_of_total_price_count').text(result.data.grand_total_of_total_price);
          $('.grand_total_of_total_price_after_vat_count').text(result.data.grand_total_of_total_price_after_vat);
          
          render_table(result.data.table.original.data);
        }
      })
      .fail(function() {
        $('.loader_img').hide();
        toast_error("error");
      });
    }

    function render_table(jsonData){
      var table = $("#myTable");
      table.DataTable().destroy();
      // table.rows.add(jsonData).draw();

      table.DataTable({
          dom: 'Bfrtip',
          buttons: [
              'copy', 'csv', 'excel', 'pdf', 'print'
          ],
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 2,
            right: 0
          },
          order: [],
          data: jsonData,
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex'},
              {data: 'invoice_no', name: 'invoice_no'},
              {data: 'invoice_date', name: 'invoice_date'},
              {data: 'item_code', name: 'item_code'},
              {data: 'item_name', name: 'item_name'},
              {data: 'brand', name: 'brand'},
              {data: 'company', name: 'company'},
              {data: 'total_quantity', name: 'total_quantity'},
              {data: 'uom', name: 'uom'},
              // {data: 'total_price', name: 'total_price'},
              // {data: 'total_price_after_vat', name: 'total_price_after_vat'},
              {data: 'item_price', name: 'item_price'},
              {data: 'net_amount', name: 'net_amount'},
              {data: 'status', name: 'status'},
          ],
          drawCallback:function(){
              // $(function () {
              //   $('[data-toggle="tooltip"]').tooltip()
              //   $('table tbody tr td:last-child').attr('nowrap', 'nowrap');

              // })
          },
          initComplete: function () {
          },
          // aoColumnDefs: [{ "bVisible": false, "aTargets": [9,10] }]
      });

    }

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      $('.grand_total_of_total_quantity_count').text('');
      $('.grand_total_of_total_price_count').text('');
      $('.grand_total_of_total_price_after_vat_count').text('');
      render_table([]);
    })


    $(document).on('change', '[name="filter_company"]', function(event) {
      event.preventDefault();
      $('[name="filter_brand"]').val('').trigger('change');

      if($(this).find('option:selected').val() != ""){
        // $('.filter_brand_div').show();
      }else{
        $('.filter_brand_div').hide();
        $('.other_filter_div').hide();
      }

    });

    // $(document).on('change', '[name="filter_brand"]', function(event) {
    //   event.preventDefault();
    //   $('[name="filter_product_category"]').val('').trigger('change');
    //   $('[name="filter_product_line"]').val('').trigger('change');
    //   $('[name="filter_product_class"]').val('').trigger('change');
    //   $('[name="filter_product_type"]').val('').trigger('change');
    //   $('[name="filter_product_application"]').val('').trigger('change');
    //   $('[name="filter_product_pattern"]').val('').trigger('change');
    //   $('[name="filter_customer_class"]').val('').trigger('change');
    //   $('[name="filter_sales_specialist"]').val('').trigger('change');
    //   $('[name="filter_market_sector"]').val('').trigger('change');
    //   $('[name="filter_market_sub_sector"]').val('').trigger('change');

    //   if($(this).find('option:selected').val() != ""){
    //     $('.other_filter_div').show();
    //   }else{
    //     $('.other_filter_div').hide();
    //   }
    // });


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
            filter_customer : $('[name="filter_customer"]').val()
          };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                        return {
                          text: item.group_name,
                          id: item.group_name,
                          data_id: item.id
                        }
                      })
          };
        },
        cache: true
      },
    });


    $('[name="filter_customer_class"]').select2({
      ajax: {
          url: "{{route('customer-tagging.get-customer-class')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    brand_id: $('[name="filter_brand"]').select2('data')[0]['data_id'],
              };
          },
          processResults: function (response) {
            return {
              results: response
            };
          },
          cache: true
      },
    });

    $('[name="filter_sales_specialist"]').select2({
      ajax: {
          url: "{{route('customer-tagging.get-sales-specialist')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    filter_manager: $('[name="filter_manager"]').find('option:selected').val(),
                    brand_id: $('[name="filter_brand"]').select2('data')[0]['data_id'],
              };
          },
          processResults: function (response) {
            return {
              results: response
            };
          },
          cache: true
      },
    });


    $('[name="filter_market_sector"]').select2({
      ajax: {
          url: "{{route('customer-tagging.get-market-sector')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    brand_id: $('[name="filter_brand"]').select2('data')[0]['data_id'],
              };
          },
          processResults: function (response) {
            return {
              results: response
            };
          },
          cache: true
      },
    });

    $('[name="filter_market_sub_sector"]').select2({
      ajax: {
          url: "{{route('customer-tagging.get-market-sub-sector')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    brand_id: $('[name="filter_brand"]').select2('data')[0]['data_id'],
              };
          },
          processResults: function (response) {
            return {
              results: response
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
            results: response
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
            results: response
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

    $('[name="filter_customer"]').select2({
        ajax: {
            url: "{{route('orders.get-customer')}}",
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
                $options = [{ id: 'all', text: 'All'}];
                response.forEach(function(value, key) {
                    $options.push({
                                text: value.card_name + " (Code: " + value.card_code + ")",
                                id: value.id
                            });
                })
                return {
                    results: $options
                };
            },
            cache: true
        },
      });

    $(document).on("click", ".download_excel", function(e) {
      $stat = check_hasFilter();
      var url = "{{route('reports.sales-report.export')}}";

      var data = {};
      data.filter_search = $('[name="filter_search"]').val();
      data.filter_company = $('[name="filter_company"]').find('option:selected').val() ?? '';
      data.filter_customer = $('[name="filter_customer"]').val();
      data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      data.filter_status = $('[name="filter_status"]').find('option:selected').val();
      data.filter_date_range = $('[name="filter_date_range"]').val();
      data.filter_product_category = $('[name="filter_product_category"]').find('option:selected').val();
      data.filter_product_line = $('[name="filter_product_line"]').find('option:selected').val();
      data.filter_product_class = $('[name="filter_product_class"]').find('option:selected').val();
      data.filter_product_type = $('[name="filter_product_type"]').find('option:selected').val();
      data.filter_product_application = $('[name="filter_product_application"]').find('option:selected').val();
      data.filter_product_pattern = $('[name="filter_product_pattern"]').find('option:selected').val();

      data.filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
      data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      data.filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
      data.filter_market_sub_sector = $('[name="filter_market_sub_sector"]').find('option:selected').val();
      data.engage_transaction = $('[name="engage_transaction"]').val();
     
      data.overdue = 'No';
      @if($title === 'Overdue Sales Invoice Report')
        data.overdue = 'Yes';
      @endif

      url = url + '?data=' + btoa(JSON.stringify(data));

      ($stat === false)? e.preventDefault() : window.location.href = url;
    });

    console.log(parseInt(moment().format('MM/YYYY')));
    $('#kt_daterangepicker_1').daterangepicker({
      autoUpdateInput: false,
      showDropdowns: true,
      // minDate: moment(),
      // "startDate": "-1m",
      // "endDate": '+1m',
      minYear: 2000,
      maxYear: parseInt(moment().format('YYYY')),
    });

    $('#kt_daterangepicker_1').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('#kt_daterangepicker_1').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
    });

  })
</script>
@endpush
