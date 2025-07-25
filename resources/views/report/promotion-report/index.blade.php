@extends('layouts.master')

@section('title','Promotion Reports')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion Reports</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:void(0)" class="btn btn-sm btn-success generate-report" style="margin-right: 5px">Generate Report</a>
        <a href="{{ route('report.index') }}" class="btn btn-sm btn-primary mr-10">Back</a>
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
            <div class="card-body">
              <div class="row">
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
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_manager" data-allow-clear="true" data-placeholder="Select Manager">
                    <option value=""></option>
                    @foreach($managers as $m)
                      <option value="{{ $m->id }}">{{ $m->first_name.' '.$m->last_name }}</option>
                    @endforeach
                  </select>
                </div>
                @endif
                <!-- Brand -->
                <div class="col-md-3 mt-5 brand">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_brand">

                    </select>
                </div>

                <!-- Customer Class -->
                <div class="col-md-3 mt-5 customer_class">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_customer_class">
                    </select>
                </div>

                @if(in_array(userrole(),[1]) || in_array(userrole(),[6]))
                <!-- Sales Specilalist -->
                <div class="col-md-3 mt-5 sales_specialist">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_sales_specialist">
                    </select>
                </div>
                @endif

                @if(!in_array(userrole(),[4]))
                <!-- Customer -->
                <div class="col-md-3 mt-5 sales_specialist">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" data-allow-clear="true" name="filter_customer">
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
                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                </div>
                <div class="col-md-6 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
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
                  <div class="col-md-6 mb-sm-5 mb-md-0">
                        <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                          <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Of Pending Sales Revenue: 
                          <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                          <span class="grand_total_of_pending_sales_revenue_count text-primary "></span>
                          </h6>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="bg-light-chocolate px-6 py-8 rounded-2 min-w-150 position-relative h-100">
                          <h6 class="d-flex justify-content-between align-items-center m-0 h-100">Grand Total Of Approved Sales Revenue: 
                          <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                          <span class="grand_total_of_approved_sales_revenue_count text-primary "></span>
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
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-striped table-row-bordered table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No</th>
                              <th>Business Unit</th>
                              <th>Status</th>
                              <th>No. of Promotion</th>
                              <th>Total Sales Quantity</th>
                              <th>Total Sales Revenue</th>
                            </tr>
                          </thead>
                          <tbody class="report-data">

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
<script>
$(document).ready(function() {

    $(document).on('click','.generate-report',function(){
      getData();
    });
    
    var myTable = $('#myTable').DataTable({
        processing: true,
        serverSide: false,
        scrollY: "800px",
        scrollCollapse: true,
        paging: false,
        data: [],
        columns: [
            { "title": "No", "data": "no" },
            { "title": "Business Unit", "data": "company_name" },
            { "title": "Status", "data": "status" },
            { "title": "No. of Promotion", "data": "total_promotion"},
            { "title": "Total Sales Quantity", "data": "total_quantity" },
            { "title": "Total Sales Revenue", "data": "total_amount" }
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

    myTable.on( 'order.dt search.dt', function () {
        myTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
    //getData();

    $('.search').on('click', function(){
        getData();
    });

    $('.clear-search').on('click', function(){
        $('[name="filter_company"]').val(null).trigger('change'),
        $('[name="filter_brand"]').val(null).trigger('change'),
        $('[name="filter_customer_class"]').val(null).trigger('change'),
        $('[name="filter_sales_specialist"]').val(null).trigger('change'),
        $('[name="filter_customer"]').val(null).trigger('change'),
        $('[name="filter_manager"]').val(null).trigger('change'),
        $('input').val(''),
        getData();
    });

    function getData(){

        $('.loader_img').show();
        $('.grand_total_of_pending_sales_revenue_count').text("");
        $('.grand_total_of_approved_sales_revenue_count').text("");

        $.ajax({
            url: "{{ route('reports.promotion-report.get-all') }}",
            method: "POST",
            dataType: 'json',
            data: {
                _token:'{{ csrf_token() }}',
                filter_company: $('[name="filter_company"]').find('option:selected').val(),
                filter_brand: $('[name="filter_brand"]').find('option:selected').val(),
                filter_customer_class: $('[name="filter_customer_class"]').find('option:selected').val(),
                filter_sales_specialist: $('[name="filter_sales_specialist"]').find('option:selected').val(),
                filter_customer: $('[name="filter_customer"]').find('option:selected').val(),
                filter_manager: $('[name="filter_manager"]').find('option:selected').val(),
                filter_date_range: $('[name="filter_date_range"]').val(),
            }
        }).done(function(result) {
            if(result.status == false){
                toast_error("result.message");
            }else{

                $('.loader_img').hide();
                $('.grand_total_of_pending_sales_revenue_count').text(result.grand_total_of_pending_sales_revenue);
                $('.grand_total_of_approved_sales_revenue_count').text(result.grand_total_of_approved_sales_revenue);

                myTable.clear();
                $.each(result.data, function(index, value) {
                    myTable.row.add(value);
                });
                myTable.draw();
            }
        }).fail(function() {
            toast_error("error");
        });
    }

    $('[name="filter_brand"]').select2({
        ajax: {
            url: "{{ route('common.getBrands') }}",
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
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'By Brand',
        // minimumInputLength: 1,
        multiple: false,
    });

    $('[name="filter_customer_class"]').select2({
        ajax: {
            url: "{{route('common.getCustomerClass')}}",
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
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'By Customer Class',
        // minimumInputLength: 1,
        multiple: false,
    });

    $('[name="filter_sales_specialist"]').select2({
        ajax: {
            url: "{{route('common.getSalesSpecialist')}}",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    
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
        placeholder: 'By Sales Specialist',
        // minimumInputLength: 1,
        multiple: false,
    });

    $('[name="filter_customer"]').select2({
        ajax: {
            url: "{{route('common.getCustomer')}}",
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
                    results: response
                };
            },
            cache: true
        },
        placeholder: 'Select Customer',
        // minimumInputLength: 1,
        multiple: false,
    });

    $(document).on("click", ".download_excel", function(e) {
      var url = "{{route('reports.promotion-report.export')}}";

      var data = {};
      data.filter_company = $('[name="filter_company"]').find('option:selected').val();
      data.filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
      data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      //data.engage_transaction = $('[name="engage_transaction"]').val();
      data.filter_manager = $('[name="filter_manager"]').find('option:selected').val();
      data.filter_date_range = $('[name="filter_date_range"]').val();
      url = url + '?data=' + btoa(JSON.stringify(data));

      window.location.href = url;
    });
});


</script>
@endpush
