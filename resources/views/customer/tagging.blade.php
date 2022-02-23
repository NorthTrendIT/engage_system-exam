@extends('layouts.master')

@section('title','Customer Tagging')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer Tagging</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        {{-- <a href="javascript:" class="btn btn-sm btn-primary sync-customers">Sync Customers</a> --}}
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
                
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Brand -->
                <div class="col-md-3 mt-5 brand_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_brand" data-control="select2" data-hide-search="false" data-placeholder="Select brand" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Customer Class -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer_class" data-control="select2" data-hide-search="false" data-placeholder="Select customer class" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select sales specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Territory -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_territory" data-control="select2" data-hide-search="false" data-placeholder="Select territory" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Market Sector -->
                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" name="filter_market_sector" data-control="select2" data-hide-search="false" data-placeholder="Select market sector" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_search" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-5 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mr-10">Clear</a>
                  @if(in_array(userrole(),[1]))
                  <a href="javascript:" class="btn btn-success font-weight-bold download_excel ">Export Excel</a>
                  @endif
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
                              <th>Customer Code</th>
                              <th>Customer Name</th>
                              <th>Customer Class</th>
                              <th>Customer Segment</th>
                              <th>Market Sector</th>
                              <th>Market Sub-Sector</th>
                              <th>Region</th>
                              <th>Province</th>
                              <th>Territory</th>
                              <th>City</th>
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
  .other_filter_div, .brand_filter_div{
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
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $filter_territory = $('[name="filter_territory"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();


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
              'url': "{{ route('customer.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_company : $filter_company,
                filter_brand : $filter_brand,
                filter_customer_class : $filter_customer_class,
                filter_market_sector : $filter_market_sector,
                filter_sales_specialist : $filter_sales_specialist,
                filter_territory : $filter_territory,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'customer_code', name: 'customer_code'},
              {data: 'card_name', name: 'card_name'},
              {data: 'class', name: 'class'},
              {data: 'u_cust_segment', name: 'u_cust_segment'},
              {data: 'u_msec', name: 'u_msec'},
              {data: 'u_tsec', name: 'u_tsec'},
              {data: 'u_rgn', name: 'u_rgn'},
              {data: 'u_province', name: 'u_province'},
              {data: 'territory', name: 'territory'},
              {data: 'city', name: 'city'},
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
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

    $('[name="filter_company"]').select2({
      ajax: {
          url: "{{route('common.getBusinessUnits')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                    _token: "{{ csrf_token() }}",
                    search: params.term,
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

    $('[name="filter_brand"]').select2({
      ajax: {
          url: "{{route('common.getBrands')}}",
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
                    brand_id: $('[name="filter_brand"]').find('option:selected').val(),
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
                    brand_id: $('[name="filter_brand"]').find('option:selected').val(),
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

    $('[name="filter_territory"]').select2({
      ajax: {
          url: "{{route('customer-tagging.get-territory')}}",
          type: "post",
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  _token: "{{ csrf_token() }}",
                  search: params.term,
                  sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                  brand_id: $('[name="filter_brand"]').find('option:selected').val(),
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
                    brand_id: $('[name="filter_brand"]').find('option:selected').val(),
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

    $('[name="filter_search"]').select2({
      ajax: {
        url: "{{ route('customer-promotion.get-customer') }}",
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

    $(document).on('change', '[name="filter_company"]', function(event) {
      event.preventDefault();
      $('[name="filter_brand"]').val('').trigger('change');
      
      if($(this).find('option:selected').val() != ""){
        $('.brand_filter_div').show();
      }else{
        $('.brand_filter_div').hide();
        $('.other_filter_div').hide();
      }

    });

    $(document).on('change', '[name="filter_brand"]', function(event) {
      event.preventDefault();
      $('[name="filter_customer_class"]').val('').trigger('change');
      $('[name="filter_sales_specialist"]').val('').trigger('change');
      $('[name="filter_territory"]').val('').trigger('change');
      $('[name="filter_market_sector"]').val('').trigger('change');

      if($(this).find('option:selected').val() != ""){
        $('.other_filter_div').show();
      }else{
        $('.other_filter_div').hide();
      }
    });


    @if(in_array(userrole(),[1]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('customer.export')}}";

        var data = {};
        data.module_type = "customer-tagging";
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();
        data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
        data.filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
        data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
        data.filter_territory = $('[name="filter_territory"]').find('option:selected').val();
        data.filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();


        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif
  })
</script>
@endpush
