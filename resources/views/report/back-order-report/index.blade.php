@extends('layouts.master')

@section('title','Back Order Report')

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
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Back Order Report</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        {{-- <a href="javascript:void(0)" class="btn btn-sm btn-success generate-report" style="margin-right: 5px">Generate Report</a> --}}
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
                @if(in_array(userrole(),[1]) || in_array(userrole(),[6]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
                @if(in_array(userrole(),[1]))
                <div class="col-md-3 mt-5 d-none">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_manager" data-allow-clear="true" data-placeholder="Select Manager">
                    <option value=""></option>
                    @foreach($managers as $m)
                      <option value="{{ $m->id }}">{{ $m->first_name.' '.$m->last_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
                @if(in_array(userrole(),[4]) || in_array(userrole(),[2]))
                <div class="col-md-3 mt-5 filter_brand_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    
                  </select>
                </div>
                @else
                <div class="col-md-3 mt-5 filter_brand_div" style="display:none;">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    
                  </select>
                </div>
                @endif
                @if(in_array(userrole(),[4]) || in_array(userrole(),[2]))
                <div class="col-md-3 mt-5" style="display:none;">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @else                
                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif
                @if(!in_array(userrole(),[4]))
                <!-- Customer -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
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

                {{-- <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                  </div>
                </div> --}}

                <div class="col-md-3 mt-5 d-none">
                  <div class="input-icon engage_transaction">
                    <input type="checkbox" class="" name = "engage_transaction" id="engage_transaction" value="1" checked>
                    <span>
                      Engage Transactions Only
                    </span>
                  </div>
                </div>


                <div class="col-md-3 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel d-none">Export Excel</a>
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
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Total Quantity Ordered: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="total_quantity_ordered_count text-primary"></span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4 mb-sm-5 mb-md-0">
                  <div class="bg-light-chocolate px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Total Remaining Open Quantity: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="total_remaining_open_quantity_count text-primary "></span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4 d-none">
                  <div class="bg-light-red px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                    <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Of Open Amount: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_open_amount_count text-primary "></span>
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
                  {{-- <div class="form-group">
                    <!--begin::Table container-->
                    <div class="table-responsive column-left-right-fix-scroll-hidden">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>SO No.</th>
                              <th>SO Date</th>
                              @if(Auth::user()->role_id != 4)
                              <th>Business Unit</th>
                              @endif
                              <th>Order No (OMS No)</th>
                              @if(Auth::user()->role_id != 4)
                              <th>Customer Name</th>
                              <th>Sales Person</th>
                              @endif
                              <th>Brand</th>
                              <th>Product Code</th>
                              <th>Product Name</th>
                              <th>Quantity Ordered</th>
                              <th>Remaining Open Quantity</th>
                              <th>Price</th>
                              <th>Price After VAT</th>
                              <th>Open Amount</th>
                              <th>Days Passed</th>
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

                  </div> --}}

                  <div class="container">
                    <div class="table-responsive">
                      <table id="back_order_tbl" class="table table-bordered display nowrap">
                          <thead class="">
                              <tr> 
                                  <th>Top</th>
                                  <th>Customer</th>
                                  <th>Code</th>
                                  <th>Product</th>
                                  <th>Ordered Qty</th>
                                  <th>Invoiced Qty</th>
                                  <th>Open Qty</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              
                          </tbody>
                      </table>
                    </div> <!--/end of table responsive -->
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


<!-- Modal -->
<div class="modal fade" id="backorderModal" tabindex="-1" aria-labelledby="backorderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="backorderModalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          {{-- <div class="table-responsive"> --}}
            <table id="back_order_details_tbl" class="table table-bordered table-hover"  width="100%" >
                <thead class="">
                    <tr> 
                        <th>Quotation #</th>
                        <th>Invoice #</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
          {{-- </div> <!--/end of table responsive --> --}}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
      </div>
    </div>
  </div>
</div>


@endsection

@push('css')
  <link href="{{ asset('assets')}}/assets/css/switch.css" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css"> --}}
  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
  <style type="text/css">
    .other_filter_div{
      display: none;
    }
  </style>
@endpush

@push('js')
  <script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
{{-- <script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script> --}}
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script>
  $(document).ready(function() {
    // render_table();
    // var back_order_details_tbl = $('#back_order_details_tbl').DataTable();

      var btn_settings = [];
      @if(!in_array(userrole(),[14]))
        btn_settings =   [
          // 'copy', 'csv', 'excel', 'pdf', 'print',
          {
              extend: 'copy',
              className: 'btn btn-secondary btn-sm border border-info',
              exportOptions: {
                  columns: 'th:not(:last-child)'
              },
          },
          {
              extend: 'csv',
              // text: 'Export Search Results',
              className: 'btn btn-secondary btn-sm border border-info',
              exportOptions: {
                  columns: 'th:not(:last-child)'
              },
          },
          {
              extend: 'excel',
              className: 'btn btn-secondary btn-sm border border-info',
              exportOptions: {
                  columns: 'th:not(:last-child)'
              },
          },
          {
              extend: 'pdf',
              className: 'btn btn-secondary btn-sm border border-info',
              exportOptions: {
                  columns: 'th:not(:last-child)'
              },
          },
          {
              extend: 'print',
              className: 'btn btn-secondary btn-sm border border-info',
              exportOptions: {
                  columns: 'th:not(:last-child)'
              },
          }
      ];
    @endif

    var back_order_tbl = $('#back_order_tbl').DataTable({
                                                columnDefs: [
                                                    // {
                                                    //     className: 'text-center',
                                                    //     targets: [0]
                                                    // },
                                                    // {
                                                    //     className: 'text-end',
                                                    //     targets: -1
                                                    // },
                                                    // { orderable: false, targets: -1 } //last row
                                                ],
                                                dom: 'Bfrtip',
                                                buttons: btn_settings
                                              });
    @if(@Auth::user()->role_id == 4)
      back_order_tbl.column( 1 ).visible( false );
    @endif

    $(document).on('click','.generate-report',function(){
      render_data();
    });
    //render_data();
    function render_data(){

      $('.loader_img').show();
      $('.grand_total_of_quantity_ordered_count').text("");
      $('.grand_total_of_remaining_open_quantity_count').text("");
      $('.grand_total_of_open_amount_count').text("");

      if ($('[name="engage_transaction"]').is(':checked')) {
          var engage_transaction = 1;
          $("#kt_daterangepicker_1").css("display","block");
      } else {
          var engage_transaction = 0;
          $("#kt_daterangepicker_1").css("display","none");
      }

      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $engage_transaction = engage_transaction;
      $filter_date_range = $('[name="filter_date_range"]').val();
      var back_order_data = {
              _token:'{{ csrf_token() }}',
              // filter_company : $filter_company,
              filter_brand : $filter_brand,
              filter_date_range: $filter_date_range
          }
      
      @if(in_array(@Auth::user()->role_id, [1, 14]))
        back_order_data['filter_company'] = $('[name="filter_company"]').find('option:selected').val() ?? $('[name="filter_customer"]').select2('data')[0]['sap_connection_id'];
        back_order_data['filter_customer'] = $('[name="filter_customer"]').select2('data')[0]['card_code'];
      @endif

      $.ajax({
        url: '{{ route('reports.back-order-report.get-all') }}',
        method: "GET",
        data: back_order_data
      })
      .done(function(result) {
        if(result.status){
          // toast_success(result.message);         
          // render_table(result.data);
          back_order_tbl.clear().draw();
          var total_ordered_qty = 0;
          var total_open_qty = 0;
          if(result.data.length > 0){
              $.each(result.data, function( index, value ) {
                modal_btn = '<button type="button" class="btn btn-secondary btn-sm border border-info" data-bs-toggle="modal" data-bs-target="#backorderModal" data-card_code="'+value.card_code+'" data-sap_connection="'+value.sap_connection_id+'">View</button>'
                back_order_tbl.row.add([(index+1), value.card_name, value.item_code, value.item_description, (value.ordered).toLocaleString(), (value.invoiced).toLocaleString(), (value.total_order).toLocaleString(), modal_btn]);
                total_ordered_qty += value.ordered;
                total_open_qty += value.total_order;
              });
          }
          $('.total_quantity_ordered_count').text((total_ordered_qty).toLocaleString());
          $('.total_remaining_open_quantity_count').text((total_open_qty).toLocaleString());
          // $('.grand_total_of_open_amount_count').text(result.data.grand_total_of_open_amount);
          $('.loader_img').hide(); 
          back_order_tbl.draw();
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

      table.DataTable({
         scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 3,
            right: 0
          },
          order: [],
          data: jsonData,
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'doc_entry', name: 'doc_entry',orderable:false,searchable:false},
              {data: 'doc_date', name: 'doc_date',orderable:false,searchable:false},
              @if(Auth::user()->role_id != 4)
              {data: 'company', name: 'company',orderable:false,searchable:false},
              @endif
              {data: 'order_no', name: 'doc_entry',orderable:false,searchable:false},
              @if(Auth::user()->role_id != 4)
              {data: 'customer', name: 'customer',orderable:false,searchable:false},
              {data: 'sales_specialist', name: 'sales_specialist',orderable:false,searchable:false},
              @endif
              {data: 'brand', name: 'brand',orderable:false,searchable:false},
              {data: 'item_code', name: 'item_code',orderable:false,searchable:false},
              {data: 'item_name', name: 'item_name',orderable:false,searchable:false},
              {data: 'quantity', name: 'quantity',orderable:false,searchable:false},
              {data: 'remaining_open_quantity', name: 'remaining_open_quantity',orderable:false,searchable:false},
              {data: 'price', name: 'price',orderable:false,searchable:false},
              {data: 'price_after_vat', name: 'price_after_vat',orderable:false,searchable:false},
              {data: 'open_amount', name: 'open_amount',orderable:false,searchable:false},
              {data: 'day_passed', name: 'doc_entry',orderable:false,searchable:false},
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
      render_data();
    });

    $(document).on('click', '.clear-search', function(event) {
      //$('input').val('');
      $("#kt_daterangepicker_1").val('');
      $('select').val('').trigger('change');
      render_data();
    })

    $('#back_order_tbl tbody').on( 'click', 'tr button', function () {
      $product_code = back_order_tbl.row( $(this).closest('tr') ).data()[2];
      $product_name = back_order_tbl.row( $(this).closest('tr') ).data()[3];
      $filter_customer = $(this).attr('data-card_code');
      $filter_company = $(this).attr('data-sap_connection');
      $filter_date_range = $('[name="filter_date_range"]').val();

      var back_order_data = {
              _token:'{{ csrf_token() }}',
              filter_company : $filter_company,
              filter_customer : $filter_customer,
              filter_date_range: $filter_date_range,
              product_code : $product_code
          }
      $('#backorderModalLabel').text('['+$product_code+']'+$product_name); 
      $.ajax({
        url: '{{ route('reports.back-order-report.view-details') }}',
        method: "GET",
        data: back_order_data
      })
      .done(function(result) {
        var html = '';
        if(result.data.length > 0){
          $.each(result.data, function( index, value ) {
            html += '<tr><td><a href="'+value.backorder_href+'" title="View details">'+value.quotation_no+'</a></td><td>'+value.invoice_no+'</td></tr>'
          });
        }else{
            html += '<tr><td colspan="2" class="text-center">No data found.</td></tr>'
        }

        $('#back_order_details_tbl tbody').html(html);

      })
      .fail(function() {
        toast_error("error");
      });


      //  console.log( back_order_tbl.row( $(this).closest('tr') ).data() ); 
    });


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
      $('[name="filter_customer"]').val('').trigger('change');
      $('[name="filter_sales_specialist"]').val('').trigger('change');

      // if($(this).find('option:selected').val() != ""){
      //   $('.other_filter_div').show();
      // }else{
      //   $('.other_filter_div').hide();
      // }
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
                          id: item.number,
                          data_id: item.id
                        }
                      })
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
                    brand_id: $('[name="filter_brand"]').select2('data')[0]['data_id'],
                    filter_manager: $('[name="filter_manager"]').find('option:selected').val(),
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

    $('[name="filter_customer"]').select2({
      ajax: {
        url: "{{route('customer-promotion.get-customer')}}",
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
                            text: item.card_name + " (Code: " + item.card_code + ")",
                            id: item.id,
                            card_code: item.card_code,
                            sap_connection_id: item.sap_connection_id
                          }
                      })
          };
        },
        cache: true
      },
    });


    $(document).on("click", ".download_excel", function(e) {
      var url = "{{route('reports.back-order-report.export')}}";

      var data = {};
      data.filter_search = $('[name="filter_search"]').val();
      data.filter_company = $('[name="filter_company"]').find('option:selected').val();
      data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      data.filter_date_range = $('[name="filter_date_range"]').val();
      data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      data.filter_manager = $('[name="filter_manager"]').find('option:selected').val();

      if ($('[name="engage_transaction"]').is(':checked')) {
          var engage_transaction = 1;
          $("#kt_daterangepicker_1").css("display","block");
      } else {
          var engage_transaction = 0;
          $("#kt_daterangepicker_1").css("display","none");
      }
      data.engage_transaction = engage_transaction;

      url = url + '?data=' + btoa(JSON.stringify(data));

      window.location.href = url;
    });

  })
</script>
@endpush
