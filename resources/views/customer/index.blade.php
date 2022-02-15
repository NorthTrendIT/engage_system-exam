@extends('layouts.master')

@section('title','Customer')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Customer</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:" class="btn btn-sm btn-primary sync-customers">Sync Customers</a>
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
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                <!-- Market Sector -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_market_sector" data-control="select2" data-hide-search="false" data-placeholder="Select Market Sector" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Market Subsetor -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_market_sub_sector" data-control="select2" data-hide-search="false" data-placeholder="Select Market Subsector" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Region -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_region" data-control="select2" data-hide-search="false" data-placeholder="Select Region" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Province -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_province" data-control="select2" data-hide-search="false" data-placeholder="Select Province" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- City -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_city" data-control="select2" data-hide-search="false" data-placeholder="Select City" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Territory -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_territory" data-control="select2" data-hide-search="false" data-placeholder="Select territory" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Branch -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_branch" data-control="select2" data-hide-search="false" data-placeholder="Select Branch" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Sales Specialist -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_sales_specialist" data-control="select2" data-hide-search="false" data-placeholder="Select Sales Specialist" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <!-- Customer Class -->
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer_class" data-control="select2" data-hide-search="false" data-placeholder="Select Customer Class" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Selecte date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
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
                    <div class="table-responsive">
                       <!--begin::Table-->
                       <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTable">
                          <!--begin::Table head-->
                          <thead>
                            <tr>
                              <th>No.</th>
                              <th>Name</th>
                              @if(in_array(userrole(),[1]))
                              <th>Business Unit</th>
                              @endif
                              <th>Universal Card Code</th>
                              @if(userrole() == 1)
                              <th>Credit Limit</th>
                              @endif
                              <th>Group</th>
                              <th>Territory</th>
                              <th>Date</th>
                              <th>Class</th>
                              {{-- <th>Status</th> --}}
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
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
  $(document).ready(function() {

    render_table();

    function render_table(){
      var table = $("#myTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_date_range = $('[name="filter_date_range"]').val();
      $filter_status = $('[name="filter_status"]').find('option:selected').val();
      $filter_territory = $('[name="filter_territory"]').find('option:selected').val();
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').val();
      $filter_market_sub_sector = $('[name="filter_market_sub_sector"]').val();
      $filter_region = $('[name="filter_region"]').val();
      $filter_province = $('[name="filter_province"]').val();
      $filter_city = $('[name="filter_city"]').val();
      $filter_branch = $('[name="filter_branch"]').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').val();
      $filter_customer_class = $('[name="filter_customer_class"]').val();


      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          responsive: true,
          order: [],
          ajax: {
              'url': "{{ route('customer.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_company : $filter_company,
                filter_market_sector : $filter_market_sector,
                filter_market_sub_sector : $filter_market_sub_sector,
                filter_region : $filter_region,
                filter_province : $filter_province,
                filter_city : $filter_city,
                filter_branch : $filter_branch,
                filter_sales_specialist : $filter_sales_specialist,
                filter_customer_class : $filter_customer_class,

                filter_territory : $filter_territory,
                filter_search : $filter_search,
                filter_date_range : $filter_date_range,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'name', name: 'name'},
              @if(in_array(userrole(),[1]))
              {data: 'company', name: 'company'},
              @endif
              {data: 'u_card_code', name: 'u_card_code'},
              @if(userrole() == 1)
              {data: 'credit_limit', name: 'credit_limit'},
              @endif
              {data: 'group', name: 'group'},
              {data: 'territory', name: 'territory'},
              {data: 'created_at', name: 'created_at'},
              {data: 'class', name: 'class'},
              // {data: 'status', name: 'status'},
              {data: 'action', name: 'action'},
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
      $('[name="filter_search"]').val('');
      $('[name="filter_date_range"]').val('');
      $('[name="filter_territory"]').val('').trigger('change');
      $('[name="filter_company"]').val('').trigger('change');
      $('[name="filter_market_sector"]').val('').trigger('change');
      $('[name="filter_market_sub_sector"]').val('').trigger('change');
      $('[name="filter_region"]').val('').trigger('change');
      $('[name="filter_province"]').val('').trigger('change');
      $('[name="filter_city"]').val('').trigger('change');
      $('[name="filter_branch"]').val('').trigger('change');
      $('[name="filter_sales_specialist"]').val('').trigger('change');
      $('[name="filter_customer_class"]').val('').trigger('change');
      render_table();
    })

    $(document).on('click', '.sync-customers', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure you want to Sync Customers?',
        text: "Syncing process will run in background and it may take some time to sync all Customers Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('customer.sync-customers') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}'
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

    @if(in_array(userrole(),[1]))
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
    @endif

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

    $('[name="filter_market_sub_sector"]').select2({
      ajax: {
          url: "{{route('common.getMarketSubSector')}}",
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

    $('[name="filter_region"]').select2({
      ajax: {
          url: "{{route('common.getRegion')}}",
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

    $('[name="filter_province"]').select2({
      ajax: {
          url: "{{route('common.getProvince')}}",
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

    $('[name="filter_city"]').select2({
      ajax: {
          url: "{{route('common.getCity')}}",
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

    $('[name="filter_branch"]').select2({
      ajax: {
          url: "{{route('common.getProvince')}}",
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


    @if(in_array(userrole(),[1]))
      $(document).on("click", ".download_excel", function(e) {
        var url = "{{route('customer.export')}}";

        var data = {};
        data.filter_search = $('[name="filter_search"]').val();
        data.filter_date_range = $('[name="filter_date_range"]').val();
        data.filter_status = $('[name="filter_status"]').find('option:selected').val();
        data.filter_territory = $('[name="filter_territory"]').find('option:selected').val();
        data.filter_company = $('[name="filter_company"]').find('option:selected').val();

        // console.log((JSON.stringify(data)));
        // console.log(btoa(JSON.stringify(data)));
        url = url + '?data=' + btoa(JSON.stringify(data));

        window.location.href = url;
      });
    @endif
  })
</script>
@endpush
