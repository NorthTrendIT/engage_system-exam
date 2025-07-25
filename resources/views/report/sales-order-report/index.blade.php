@extends('layouts.master')

@section('title','Sales Order Report')

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
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Sales Order Report</h1>
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
        
      {{-- Filters --}}
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
           
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
                <div class="col-md-3 mt-5 filter_brand_div d-none">
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
                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5" style="display: none;">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @else
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                <!-- Customer -->
                @if(!in_array(userrole(),[4]))
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

                <div class="col-md-3 mt-5">
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
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>


      {{-- Number of Sales Orders --}}
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Number of Sales Orders</h5>
            </div>
            <div class="card-body">
              <div class="row mb-5">
                <div class="col-md-4 bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-warning fw-bold fs-6">Pending </a>
                  <span class="count text-warning fw-bold fs-1 number_of_sales_orders_pending_count">0</span>
                </div>

                <div class="col-md-4 bg-light-dark px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-dark fw-bold fs-6">On Process</a>
                  <span class="count text-dark fw-bold fs-1 number_of_sales_orders_on_process_count">0</span>
                </div>

                <div class="col-md-4 bg-light-info px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative d-none">
                  <a href="javascript:" class="text-info fw-bold fs-6">For Delivery</a>
                  <span class="count text-info fw-bold fs-1 number_of_sales_orders_for_delivery_count">0</span>
                </div>

                <div class="col-md-4 bg-light-primary px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-primary fw-bold fs-6">Partially Served</a>
                  <span class="count text-primary fw-bold fs-1 number_of_sales_orders_partially_served_count">0</span>
                </div>

                <div class="col-md-4 bg-light-success px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-success fw-bold fs-6">Completed</a>
                  <span class="count text-success fw-bold fs-1 number_of_sales_orders_completed_count">0</span>
                </div>

                <div class="col-md-4 bg-light-danger px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-danger fw-bold fs-6">Cancelled</a>
                  <span class="count text-danger fw-bold fs-1 number_of_sales_orders_cancelled_count">0</span>
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-12">
                  {{-- <div id="number_of_sales_orders_pie_chart_div" class="h-500px"></div> --}}

                    <!--begin::Table container-->
                    <div class="table-responsive column-left-right-fix-scroll-hidden">
                      <!--begin::Table-->
                      <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                         <!--begin::Table head-->
                         <thead>
                           <tr>
                             <th>No</th>
                             <th>Order #</th>
                             @if(userrole() != 4)
                             <th>Customer Name</th>
                             @endif
                             <th>Order Type</th>
                             @if(in_array(userrole(),[1,10]))
                             <th>Business Unit</th>
                             @endif
                             <th>Created Date</th>
                             <th>Status</th>
                             <th>DocTotal</th>
                             <th>Created By</th>
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

                </div>
              </div>


            </div>
          </div>
        </div>
      </div>

      {{-- Total Sales Quantity --}}
      <div class="row gy-5 g-xl-8 d-none">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Total Sales Quantity</h5>
            </div>
            <div class="card-body">
              <div class="row mb-5">
                <div class="col-md-4 bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-warning fw-bold fs-6">Pending </a>
                  <span class="count text-warning fw-bold fs-1 total_sales_quantity_pending_count">0</span>
                </div>

                <div class="col-md-4 bg-light-dark px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-dark fw-bold fs-6">On Process </a>
                  <span class="count text-dark fw-bold fs-1 total_sales_quantity_on_process_count">0</span>
                </div>

                <div class="col-md-4 bg-light-info px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-info fw-bold fs-6">For Delivery </a>
                  <span class="count text-info fw-bold fs-1 total_sales_quantity_for_delivery_count">0</span>
                </div>

                <div class="col-md-4 bg-light-primary px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-primary fw-bold fs-6">Delivered</a>
                  <span class="count text-primary fw-bold fs-1 total_sales_quantity_delivered_count">0</span>
                </div>

                <div class="col-md-4 bg-light-success px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-success fw-bold fs-6">Completed</a>
                  <span class="count text-success fw-bold fs-1 total_sales_quantity_completed_count">0</span>
                </div>

                <div class="col-md-4 bg-light-danger px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-danger fw-bold fs-6">Cancelled</a>
                  <span class="count text-danger fw-bold fs-1 total_sales_quantity_cancelled_count">0</span>
                </div>
              </div>

              <div class="row mb-5">
                <div class="col-md-12">
                  <div id="total_sales_quantity_pie_chart_div" class="h-500px"></div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      {{-- Total Sales Revenue --}}
      <div class="row gy-5 g-xl-8 d-none">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5 class="text-info">Total Sales Amount</h5>
            </div>
            <div class="card-body">
              <div class="row mb-5">
                <div class="col-md-4 bg-light-warning px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-warning fw-bold fs-6">Pending </a>
                  <span class="count text-warning fw-bold fs-1 total_sales_revenue_pending_count">0</span>
                </div>

                <div class="col-md-4 bg-light-dark px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-dark fw-bold fs-6">On Process </a>
                  <span class="count text-dark fw-bold fs-1 total_sales_revenue_on_process_count">0</span>
                </div>

                <div class="col-md-4 bg-light-info px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-info fw-bold fs-6">For Delivery </a>
                  <span class="count text-info fw-bold fs-1 total_sales_revenue_for_delivery_count">0</span>
                </div>

                <div class="col-md-4 bg-light-primary px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-primary fw-bold fs-6">Delivered</a>
                  <span class="count text-primary fw-bold fs-1 total_sales_revenue_delivered_count">0</span>
                </div>

                <div class="col-md-4 bg-light-success px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-success fw-bold fs-6">Completed</a>
                  <span class="count text-success fw-bold fs-1 total_sales_revenue_completed_count">0</span>
                </div>

                <div class="col-md-4 bg-light-danger px-6 py-8 rounded-2 me-7 mb-7 min-w-150 col-box-4 position-relative">
                  <a href="javascript:" class="text-danger fw-bold fs-6">Cancelled</a>
                  <span class="count text-danger fw-bold fs-1 total_sales_revenue_cancelled_count">0</span>
                </div>

              </div>


              <div class="row mb-5">
                <div class="col-md-12">
                  <div id="total_sales_revenue_pie_chart_div" class="h-500px"></div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div id="hover">
        
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
<script src="{{ asset('assets') }}/assets/plugins/custom/flotcharts/flotcharts.bundle.js"></script>
{{-- <script src="{{ asset('assets') }}/assets/js/custom/documentation/charts/flotcharts/pie.js"></script> --}}
<script src="http://www.flotcharts.org/flot/source/jquery.flot.legend.js"></script>

<script>
  $(document).ready(function() {
    render_table();

    $(document).on('click','.generate-report, .search',function(){
      // alert('Oppss! this functionality is under construction.');
      // render_data();
      render_table();
    });


    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      if ($('[name="engage_transaction"]').is(':checked')) {
          var engage_transaction = 1;
          $("#kt_daterangepicker_1").css("display","block");
      } else {
          var engage_transaction = 0;
          $("#kt_daterangepicker_1").css("display","none");
      }

      $filter_search = $('[name="filter_search"]').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_status = $('[name="filter_status[]"]').select2('val');
      $filter_order_type = $('[name="filter_order_type"]').find('option:selected').val();
      $filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_group = $('[name="filter_group"]').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_class = $('[name="filter_class"]').find('option:selected').val();
      $filter_territory = $('[name="filter_territory"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
      $engage_transaction = engage_transaction;

      // @if(userrole() !== 4)
      //   data_post['filter_customer'] = $('[name="filter_customer"]').select2('data')[0].code;
      // @endif

      $.ajax({
        url: '{{ route('reports.sales-order-report.count-stat') }}',
        method: "GET",
        data: {
                _token:'{{ csrf_token() }}',
                filter_date_range : $filter_date_range,
                filter_company: $filter_company,
                filter_customer: $filter_customer
        }
        })
        .done(function(result) {
            if(result.status == false){
                toast_error(result.message);
            }else{
                $('.number_of_sales_orders_pending_count').text(result.data.pending);
                $('.number_of_sales_orders_cancelled_count').text(result.data.cancelled);
                $('.number_of_sales_orders_on_process_count').text(result.data.on_process);
                $('.number_of_sales_orders_partially_served_count').text(result.data.partially_served);
                $('.number_of_sales_orders_completed_count').text(result.data.completed);
            }
        })
        .fail(function() {
            toast_error("error");
        });



      var hide_targets = [];
      // @if(userrole() === 4)
      //   hide_targets.push(4)
      // @endif

      // @if(userrole() != 4 && !in_array(userrole(),[1,10]))
      //   hide_targets.push(5)
      // @endif

      // @if(in_array(userrole(),[1,10]))
      //   hide_targets.push(6)
      // @endif
      // console.log(hide_targets);
      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            @if(userrole() != 4)
            left: 3,
            @else
            left: 2,
            @endif
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('orders.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_date_range : $filter_date_range,
                filter_status : $filter_status,
                filter_order_type : $filter_order_type,
                filter_company : $filter_company,
                filter_group : $filter_group,
                filter_customer : $filter_customer == 'all' ? '' : $filter_customer,
                filter_brand : $filter_brand == 'all' ? '' : $filter_brand,
                filter_class : $filter_class == 'all' ? '' : $filter_class,
                filter_sales_specialist : $filter_sales_specialist == 'all' ? '' : $filter_sales_specialist,
                filter_market_sector : $filter_market_sector == 'all' ? '' : $filter_market_sector,
                filter_territory : $filter_territory == 'all' ? '' : $filter_territory,
                engage_transaction : $engage_transaction,
              }
          },
          columns: [
              {data: 'DT_RowIndex'},
              {data: 'doc_entry', name: 'doc_entry'},
              @if(userrole() != 4)
              {data: 'name', name: 'name'},
              @endif
              {data: 'order_type', name: 'order_type', orderable:false},
              @if(in_array(userrole(),[1,10]))
              {data: 'company', name: 'company'},
              @endif
              {data: 'date', name: 'date'},
              {data: 'status', name: 'status'},
              {data: 'total', name: 'total'},
              {data: 'created_by', name: 'created_by'},
              {data: 'action', name: 'action'},
          ],
          createdRow: function( row, data, dataIndex ) {
            // console.log(data);
          },
          drawCallback:function(settings){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip()
                $('table tbody tr td:last-child').attr('nowrap', 'nowrap');
              })
              // console.log( this.fnSettings().fnRecordsTotal() );
          },
          initComplete: function () {
          },
          aoColumnDefs: [{ "bVisible": false, "aTargets": hide_targets }]
        });
    }







    function render_data(){

      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $engage_transaction = $('[name="engage_transaction"]').val();

      $.ajax({
        url: '{{ route('reports.sales-order-report.get-all') }}',
        method: "POST",
        data: {
                _token:'{{ csrf_token() }}',
                filter_company : $filter_company,
                filter_date_range : $filter_date_range,
                filter_brand : $filter_brand,
                filter_customer : $filter_customer,
                filter_sales_specialist : $filter_sales_specialist,
                engage_transaction : $engage_transaction,
              }
      })
      .done(function(result) {
        if(result.status){
          toast_success(result.message);

          $('.number_of_sales_orders_pending_count').text(result.data.pending_total_sales_orders);
          $('.number_of_sales_orders_approved_count').text(result.data.approved_total_sales_orders);
          $('.number_of_sales_orders_disapproved_count').text(result.data.disapproved_total_sales_orders);
          $('.number_of_sales_orders_on_process_count').text(result.data.on_process_total_sales_orders);
          $(".number_of_sales_orders_for_delivery_count").text(result.data.for_delivery_total_sales_orders);
          $(".number_of_sales_orders_delivered_count").text(result.data.delivered_total_sales_orders);
          $(".number_of_sales_orders_completed_count").text(result.data.completed_total_sales_orders);
          $('.number_of_sales_orders_cancelled_count').text(result.data.cancelled_total_sales_orders);

          $('.total_sales_quantity_pending_count').text(result.data.pending_total_sales_quantity);
          $('.total_sales_quantity_on_process_count').text(result.data.on_process_total_sales_quantity);
          $('.total_sales_quantity_for_delivery_count').text(result.data.for_delivery_total_sales_quantity);
          $('.total_sales_quantity_delivered_count').text(result.data.delivered_total_sales_quantity);
          $('.total_sales_quantity_completed_count').text(result.data.completed_total_sales_quantity);
          $('.total_sales_quantity_cancelled_count').text(result.data.cancelled_total_sales_quantity);

          $('.total_sales_revenue_pending_count').text("₱ " + get_format_number_value(result.data.pending_total_sales_revenue));
          $('.total_sales_revenue_on_process_count').text("₱ " + get_format_number_value(result.data.on_process_total_sales_revenue));
          $('.total_sales_revenue_for_delivery_count').text("₱ " + get_format_number_value(result.data.for_delivery_total_sales_revenue));
          $('.total_sales_revenue_delivered_count').text("₱ " + get_format_number_value(result.data.delivered_total_sales_revenue));
          $('.total_sales_revenue_completed_count').text("₱ " + get_format_number_value(result.data.completed_total_sales_revenue));
          $('.total_sales_revenue_cancelled_count').text("₱ " + get_format_number_value(result.data.cancelled_total_sales_revenue));

          render_pie_chart(result.data);

        }
      })
      .fail(function() {
        toast_error("error");
      });
    }


    function render_pie_chart(result){

      {{-- Number of Sales Orders --}}
      var data = [

            { label: "Pending", data: result.pending_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-warning") },
            { label: "On Process", data: result.on_process_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-dark") },
            { label: "For Delivery", data: result.for_delivery_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-info") },
            { label: "Delivered", data: result.delivered_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-primary") },
            { label: "Completed", data: result.completed_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-success") },
            { label: "Cancelled", data: result.cancelled_total_sales_orders, color: KTUtil.getCssVariableValue("--bs-active-danger") }
          ];

      $.plot('#number_of_sales_orders_pie_chart_div', data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: true,
              radius: 3/4,
              formatter: labelFormatter,
              threshold: 0.1,
            }
          }
        },
        legend: {
          show: false
        },
        grid: {
          hoverable: true,
          clickable: true
        },
      });

      if(result.pending_total_sales_orders == 0 && result.on_process_total_sales_orders == 0 && result.for_delivery_total_sales_orders == 0 && result.delivered_total_sales_orders == 0 && result.completed_total_sales_orders == 0 && result.cancelled_total_sales_orders == 0){
        $('#number_of_sales_orders_pie_chart_div').removeClass('h-500px');
      }


      {{-- Total Sales Quantity --}}
      var data = [
            { label: "Pending", data: result.pending_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-warning") },
            { label: "On Process", data: result.on_process_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-dark") },
            { label: "For Delivery", data: result.for_delivery_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-info") },
            { label: "Delivered", data: result.delivered_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-primary") },
            { label: "Completed", data: result.completed_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-success") },
            { label: "Cancelled", data: result.cancelled_total_sales_quantity, color: KTUtil.getCssVariableValue("--bs-active-danger") }
          ];
          
      $.plot('#total_sales_quantity_pie_chart_div', data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: true,
              radius: 3/4,
              formatter: labelFormatter,
              threshold: 0.1,
            }
          }
        },
        legend: {
          show: false
        },
        grid: {
          hoverable: true,
          clickable: true
        },
      });

      if(result.pending_total_sales_quantity == 0 && result.approved_total_sales_quantity == 0 && result.disapproved_total_sales_quantity == 0){
        $('#total_sales_quantity_pie_chart_div').removeClass('h-500px');
      }

      {{-- Total Sales Revenue --}}
      var data = [
            { label: "Pending", data: result.pending_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-warning") },
            { label: "On Process", data: result.on_process_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-dark") },
            { label: "For Delivery", data: result.for_delivery_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-info") },
            { label: "Delivered", data: result.delivered_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-primary") },
            { label: "Completed", data: result.completed_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-success") },
            { label: "Cancelled", data: result.cancelled_total_sales_revenue, color: KTUtil.getCssVariableValue("--bs-active-danger") }
          ];
          
      $.plot('#total_sales_revenue_pie_chart_div', data, {
        series: {
          pie: {
            show: true,
            radius: 1,
            label: {
              show: true,
              radius: 3/4,
              formatter: labelFormatter,
              threshold: 0.1,
            }
          }
        },
        legend: {
          show: false
        },
        grid: {
          hoverable: true,
          clickable: true
        },
      });

      if(result.pending_total_sales_revenue == 0 && result.approved_total_sales_revenue == 0 && result.disapproved_total_sales_revenue == 0){
        $('#total_sales_revenue_pie_chart_div').removeClass('h-500px');
      }
    }


    function labelFormatter(label, series) {
      return "<div class='default_label' style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }

    $('#total_sales_revenue_pie_chart_div,#total_sales_quantity_pie_chart_div,#number_of_sales_orders_pie_chart_div').bind("plothover", function(event, pos, obj) {
      if(obj){
        var percent = Math.round(obj.series.percent);
        $("#hover").html("<span style='font-weight:bold; color:" + obj.series.color + "'>" + obj.series.label + " (" + percent + "%)</span>");
        $('#hover').css({'position':'absolute','display':'block','left':pos.pageX,'top':pos.pageY}); 
      }
      else {
        $('#hover').css('display','none');
      }
    });

    function get_format_number_value(number) {
      var formatter = new Intl.NumberFormat('en-US', {
        // style: 'currency',
        currency: 'PHL',
      });

      return formatter.format(number); 
    }

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
      // render_data();
    })


    // $(document).on('change', '[name="filter_company"]', function(event) {
    //   event.preventDefault();
    //   $('[name="filter_brand"]').val('').trigger('change');

    //   if($(this).find('option:selected').val() != ""){
    //     $('.filter_brand_div').show();
    //   }else{
    //     $('.filter_brand_div').hide();
    //     $('.other_filter_div').hide();
    //   }

    // });

    $(document).on('change', '[name="filter_brand"]', function(event) {
      event.preventDefault();
      $('[name="filter_sales_specialist"]').val('').trigger('change');

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
                search: params.term
            };
        },
        processResults: function (response) {
          return {
            results:  $.map(response, function (item) {
                          return {
                            text: item.card_name + " (Code: " + item.card_code + ")",
                            id: item.card_code,
                            code: item.id
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
