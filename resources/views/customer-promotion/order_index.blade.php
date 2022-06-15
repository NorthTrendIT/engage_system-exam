@extends('layouts.master')

@section('title','Claimed Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Claimed Promotions</h1>
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
              <div class="row">

                @if(in_array(userrole(),[1]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="true" name="filter_company" data-allow-clear="true" data-placeholder="Select Business Unit">
                    <option value=""></option>
                    {{-- @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_brand" data-allow-clear="true" data-placeholder="Select brand">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_customer_class" data-allow-clear="true" data-placeholder="Select customer class">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_sales_specialist" data-allow-clear="true" data-placeholder="Select sales specialist">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5 other_filter_div">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_market_sector" data-allow-clear="true" data-placeholder="Select market sector">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_territory" data-allow-clear="true" data-placeholder="Select territory">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                @if(in_array(userrole(),[1,2]))
                <div class="col-md-3 mt-5 {{ (in_array(userrole(),[1])) ? "other_filter_div" : "" }}">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_promotion" data-control="select2" data-hide-search="false" data-placeholder="Select promotion code" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-placeholder="Select status" data-allow-clear="true">
                    <option value=""></option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </div>

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
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-{{ (in_array(userrole(),[1])) ? "5" : "4" }} mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search mx-2">Clear</a>
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
                              @if(in_array(userrole(),[1]))
                              <th>Company</th>
                              @endif
                              <th>Promotion Code</th>
                              @if(in_array(userrole(),[1,2]))
                              <th>Customer</th>
                              @endif
                              <th>Date Time</th>
                              <th>Status</th>
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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css">
<style>
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
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_customer = $('[name="filter_customer"]').find('option:selected').val();
      $filter_promotion = $('[name="filter_promotion"]').find('option:selected').val();

      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
      $filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
      $filter_territory = $('[name="filter_territory"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          scrollY: "800px",
          scrollCollapse: true,
          paging: true,
          fixedColumns:   {
            left: 1,  
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('customer-promotion.order.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_date_range : $filter_date_range,
                filter_status : $filter_status,
                filter_customer : $filter_customer,
                filter_promotion : $filter_promotion,

                filter_company : $filter_company,
                filter_brand : $filter_brand,
                filter_sales_specialist : $filter_sales_specialist,
                filter_market_sector : $filter_market_sector,
                filter_customer_class : $filter_customer_class,
                filter_territory : $filter_territory,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              @if(in_array(userrole(),[1]))
              {data: 'company', name: 'company'},
              @endif
              {data: 'promotion', name: 'promotion'},
              @if(in_array(userrole(),[1,2]))
              {data: 'user', name: 'user'},
              @endif
              {data: 'created_at', name: 'created_at'},
              {data: 'status', name: 'status'},
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
    });

    $(document).on('click', '.clear-search', function(event) {
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

    @if(in_array(userrole(),[1,2]))
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
                    @if(in_array(userrole(),[1]))
                    sap_connection_id: $('[name="filter_company"]').find('option:selected').val(),
                    @endif
                };
            },
            processResults: function (response) {
              return {
                results:  $.map(response, function (item) {
                              return {
                                text: item.card_name + " (Code: " + item.card_code + ")",
                                id: item.user.id
                              }
                          })
              };
            },
            cache: true
        },
      });
    @endif


    $('[name="filter_promotion"]').select2({
      ajax: {
          url: "{{route('common.getPromotionCodes')}}",
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

    @if(in_array(userrole(),[1]))
      $(document).on('change', '[name="filter_company"]', function(event) {
        event.preventDefault();
        $('[name="filter_brand"]').val('').trigger('change');
        $('[name="filter_customer_class"]').val('').trigger('change');
        $('[name="filter_sales_specialist"]').val('').trigger('change');
        $('[name="filter_market_sector"]').val('').trigger('change');
        $('[name="filter_customer"]').val('').trigger('change');
        
        if($(this).find('option:selected').val() != ""){
          $('.other_filter_div').show();
        }else{
          $('.other_filter_div').hide();
        }

      });

      $('[name="filter_company"]').select2({
        ajax: {
            url: "{{route('common.getBusinessUnits')}}",
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
      });


      $('[name="filter_market_sector"]').select2({
        ajax: {
            url: "{{route('common.getMarketSector')}}",
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

      $('[name="filter_territory"]').select2({
        ajax: {
            url: "{{route('common.getTerritory')}}",
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

      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('customer-promotion.order.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
        data.filter_promotion = $('[name="filter_promotion"]').find('option:selected').val();
        
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();
        data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
        data.filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
        data.filter_customer_class = $('[name="filter_customer_class"]').find('option:selected').val();
        data.filter_territory = $('[name="filter_territory"]').find('option:selected').val();
        data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();

        // console.log((JSON.stringify(data)));
        // console.log(btoa(JSON.stringify(data)));
        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif

  })
</script>
@endpush
