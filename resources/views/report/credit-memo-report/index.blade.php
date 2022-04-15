@extends('layouts.master')

@section('title','Credit Memo Report')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Credit Memo Report</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
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
              <div class="row">

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5 filter_brand_div" style="display:none;">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                    
                  </select>
                </div>

                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>


                <!-- Customer -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
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
                <div class="col-md-4">
                  <div class="bg-light-warning px-6 py-8 rounded-2 min-w-150 position-relative">
                    <h6 class="d-flex justify-content-between align-items-center m-0">Grand Total Of Amount: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_amount_count text-primary "></span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="bg-light-chocolate px-6 py-8 rounded-2 min-w-150 position-relative">
                    <h6 class="d-flex justify-content-between align-items-center m-0">Grand Total Of Price After VAT: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_price_after_vat_count text-primary "></span>
                    </h6>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="bg-light-red px-6 py-8 rounded-2 min-w-150 position-relative">
                    <h6 class="d-flex justify-content-between align-items-center m-0">Grand Total Of Gross Total: 
                      <img src="{{ asset('assets/assets/media/loader-gray.gif') }}" style="width: 20px;display: none;" class="loader_img"> 
                      <span class="grand_total_of_gross_total_count text-primary "></span>
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
            <div class="card-header border-0 pt-5 min-0">
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
                              <th>No</th>
                              <th>Customer Name</th>
                              <th>Business Unit</th>
                              <th>Date</th>
                              <th>Credit Memo No</th>
                              <th>Sales Specialist</th>
                              <th>Total Amount</th>
                              <th>Description</th>
                              <th>Price After VAT</th>
                              <th>Gross Total</th>
                              <th>Remarks</th>
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
    
    render_data();
    function render_data(){

      $('.loader_img').show();
      $('.grand_total_of_amount_count').text("");
      $('.grand_total_of_price_after_vat_count').text("");
      $('.grand_total_of_gross_total_count').text("");

      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();

      $.ajax({
        url: '{{ route('reports.credit-memo-report.get-all') }}',
        method: "POST",
        data: {
                _token:'{{ csrf_token() }}',
                filter_company : $filter_company,
                filter_customer : $filter_customer,
                filter_brand : $filter_brand,
                filter_sales_specialist : $filter_sales_specialist,
              }
      })
      .done(function(result) {
        if(result.status){
          toast_success(result.message);
          
          $('.loader_img').hide();

          $('.grand_total_of_amount_count').text(result.data.grand_total_of_amount);
          $('.grand_total_of_price_after_vat_count').text(result.data.grand_total_of_price_after_vat);
          $('.grand_total_of_gross_total_count').text(result.data.grand_total_of_gross_total);
          
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

      table.DataTable({
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
            {data: 'DT_RowIndex' ,orderable:false,searchable:false},
            {data: 'card_name', name: 'card_name' ,orderable:false,searchable:false},
            {data: 'company', name: 'company' ,orderable:false,searchable:false},
            {data: 'date', name: 'date' ,orderable:false,searchable:false},
            {data: 'doc_num', name: 'doc_num' ,orderable:false,searchable:false},
            {data: 'sales_specialist', name: 'sales_specialist' ,orderable:false,searchable:false},
            {data: 'doc_total', name: 'doc_total' ,orderable:false,searchable:false},
            {data: 'item_description', name: 'item_description' ,orderable:false,searchable:false},
            {data: 'price_after_vat', name: 'price_after_vat' ,orderable:false,searchable:false},
            {data: 'gross_total', name: 'gross_total' ,orderable:false,searchable:false},
            {data: 'comments', name: 'comments' ,orderable:false,searchable:false},
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
      $('input').val('');
      $('select').val('').trigger('change');
      render_data();
    })


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
                          id: item.id,
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
                            id: item.id
                          }
                      })
          };
        },
        cache: true
      },
      tags: true,
      minimumInputLength: 2,
    });


    $(document).on("click", ".download_excel", function(e) {
      var url = "{{route('reports.credit-memo-report.export')}}";

      var data = {};
      data.filter_company = $('[name="filter_company"]').find('option:selected').val();
      data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();

      url = url + '?data=' + btoa(JSON.stringify(data));

      window.location.href = url;
    });

  })
</script>
@endpush
