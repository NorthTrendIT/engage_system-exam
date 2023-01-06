@extends('layouts.master')

@section('title','Orders')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Orders</h1>
      </div>

      @if(userrole() != 4)
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="javascript:" class="btn btn-sm btn-primary sync-orders">Sync Orders</a>
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
                @if(in_array(userrole(),[1]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                    <option value=""></option>
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Select Customer By -->
                <div class="col-md-3 mt-5">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectModule" data-hide-search="false" data-allow-clear="true" name="module">
                        <option value=""></option>
                        <option value="all">All</option>
                        <option value="brand">By brand</option>
                        <option value="customer_class">By class</option>
                        <option value="sales_specialist">By sales specialist</option>
                        <option value="territory">By territory</option>
                        <option value="market_sector">By market sector</option>
                    </select>
                </div>
                <!-- Brand -->
                <div class="col-md-3 mt-5 brand" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectBrand" data-hide-search="false" data-allow-clear="true" name="filter_brand" data-placeholder="Select brand">
                        <option value=""></option>
                    </select>
                </div>

                <!-- Customer Class -->
                <div class="col-md-3 mt-5 customer_class" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectCustomerClass" data-hide-search="false" data-allow-clear="true" name="filter_customer_class">
                      <option value=""></option>
                    </select>
                </div>

                <!-- Sales Specilalist -->
                <div class="col-md-3 mt-5 sales_specialist" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectSalesSpecialist" data-hide-search="false" data-allow-clear="true" name="filter_sales_specialist">
                      <option value=""></option>
                    </select>
                </div>

                <!-- Territory -->
                <div class="col-md-3 mt-5 territory" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectTerritory" data-hide-search="false" data-allow-clear="true" name="filter_territory">
                      <option value=""></option>
                    </select>
                </div>

                <!-- Market Sector -->
                <div class="col-md-3 mt-5 market_sector" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectMarketSector" data-hide-search="false" data-allow-clear="true" name="filter_market_sector">
                      <option value=""></option>
                    </select>
                </div>
                @endif

                @if(in_array(userrole(),[1,2]))
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_customer" data-control="select2" data-hide-search="false" data-allow-clear="true" data-placeholder="Select customer" data-allow-clear="true">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid js-example-basic-multiple" name="filter_status[]" data-control="select2" data-hide-search="false" data-placeholder="Select status" data-allow-clear="true" multiple="multiple">
                    <option value=""></option>

                    @foreach(getOrderStatusArray() as $key => $value)
                      <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_order_type" data-control="select2" data-hide-search="false" data-placeholder="Select order type" data-allow-clear="true">
                    <option value=""></option>
                    <option value="Standard">Standard</option>
                    <option value="Promotion">Promotion</option>
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

                <div class="col-md-6 mt-5">
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
                              <th>No</th>
                              <th>Order #</th>
                              @if(userrole() != 4)
                              <th>Customer Name</th>
                              @endif
                              <th>Order Type</th>
                              @if(in_array(userrole(),[1]))
                              <th>Business Unit</th>
                              @endif
                              <th>Total</th>
                              <th>Created Date</th>
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
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
<script>
  $(document).ready(function() {

    render_table();

    $('#engage_transaction').change(function() {
      if(this.checked) {
        $("#kt_daterangepicker_1").css("display","block");
      }else{
        $("#kt_daterangepicker_1").css("display","none");
      }
    });

    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
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
      $filter_brand = $('[name="filter_brand"]').find('option:selected').val();
      $filter_class = $('[name="filter_class"]').find('option:selected').val();
      $filter_territory = $('[name="filter_territory"]').find('option:selected').val();
      $filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
      $filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
      $engage_transaction = engage_transaction;

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
              @if(in_array(userrole(),[1]))
              {data: 'company', name: 'company'},
              @endif
              {data: 'total', name: 'total'},
              {data: 'date', name: 'date'},
              {data: 'status', name: 'status'},
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
      // $('[name="filter_search"]').val('');
      // $('[name="filter_date_range"]').val('');
      // $('[name="filter_status"]').val('').trigger('change');
      // $('[name="filter_customer"]').val('').trigger('change');
      // $('[name="filter_company"]').val('').trigger('change');
      $('input').val('');
      $('select').val('').trigger('change');
      $('[name="module"]').val(null).trigger('change');
      render_table();
    })

    $(document).on('click', '.sync-orders', function(event) {
      event.preventDefault();

      Swal.fire({
        title: 'Are you sure want to sync Orders, Quotations, and Invoice data?',
        text: "Syncing process will run in background and it may take some time to sync all Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.sync-orders') }}',
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

    @if(in_array(userrole(),[1,2]))
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
                                id: value.card_code
                            });
                })
                return {
                    results: $options
                };
            },
            cache: true
        },
      });
    @endif

    @if(userrole() == 1)
    $(document).on('click', '.notifyCustomer', function(event) {
      event.preventDefault();
      var order_id = $(this).data('order');
      Swal.fire({
        title: 'Are you sure want to Order status update Email and notification to Customer?',
        // text: "Syncing process will run in background and it may take some time to sync all Data.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, do it!'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '{{ route('orders.notify-customer') }}',
            method: "POST",
            data: {
                    _token:'{{ csrf_token() }}',
                    order_id: order_id,
                  }
          })
          .done(function(result) {
            if(result.status == false){
              toast_error(result.message);
            }else{
              toast_success(result.message);
            //   render_table();
            }
          })
          .fail(function() {
            toast_error("error");
          });
        }
      })
    });
    @endif

    @if(in_array(userrole(),[1]))
        $(document).on("click", ".download_excel", function(e) {
            var url = "{{route('orders.export')}}";

            if ($('[name="engage_transaction"]').is(':checked')) {
                var engage_transaction = 1;
            } else {
                var engage_transaction = 0;
            }

            var data = {};
            data.filter_search = $('[name="filter_search"]').val();
            data.filter_date_range = $('[name="filter_date_range"]').val();
            data.filter_status = $('[name="filter_status"]').find('option:selected').val();
            data.filter_order_type = $('[name="filter_order_type"]').find('option:selected').val();
            data.filter_customer = $('[name="filter_customer"]').find('option:selected').val();
            data.filter_company = $('[name="filter_company"]').find('option:selected').val();
            data.filter_brand = $('[name="filter_brand"]').find('option:selected').val();
            data.filter_class = $('[name="filter_class"]').find('option:selected').val();
            data.filter_territory = $('[name="filter_territory"]').find('option:selected').val();
            data.filter_sales_specialist = $('[name="filter_sales_specialist"]').find('option:selected').val();
            data.filter_market_sector = $('[name="filter_market_sector"]').find('option:selected').val();
            data.engage_transaction = engage_transaction;

            url = url + '?data=' + btoa(JSON.stringify(data));

            window.location.href = url;
        });

        $('body').on('change' ,'#selectModule', function(){
            $module = $('[name="module"]').val();
            // Hide all.
            $('.brand').hide();
            $('.customer').hide();
            $('.customer_class').hide();
            $('.sales_specialist').hide();
            $('.territory').hide();
            $('.market_sector').hide();
            // Dissable all.
            $('#selectrBrand').prop('disabled', true);
            $('#selectCustomer').prop('disabled', true);
            $('#selectCustomerClass').prop('disabled', true);
            $('#selectSalesSpecialist').prop('disabled', true);
            $('#selectTerritory').prop('disabled', true);
            $('#selectMarketSector').prop('disabled', true);
            // Set null value to all.
            $('#selectBrand').val(null).trigger("change");
            $('#selectCustomer').val(null).trigger("change");
            $('#selectCustomerClass').val(null).trigger("change");
            $('#selectSalesSpecialist').val(null).trigger("change");
            $('#selectTerritory').val(null).trigger("change");
            $('#selectMarketSector').val(null).trigger("change");

            // Show and enable according to Module selection.
            if($module == "brand"){
                $('.brand').show();
                $('#selectBrand').prop('disabled', false);
            } else if ($module == "customer"){
                $('.customer').show();
                $('#selectCustomer').prop('disabled', false);
            } else if($module == "customer_class"){
                $('.customer_class').show();
                $('#selectCustomerClass').prop('disabled', false);
            } else if($module == "sales_specialist"){
                $('.sales_specialist').show();
                $('#selectSalesSpecialist').prop('disabled', false);
            } else if($module == "territory"){
                $('.territory').show();
                $('#selectTerritory').prop('disabled', false);
            } else if($module == "market_sector"){
                $('.market_sector').show();
                $('#selectMarketSector').prop('disabled', false);
            }
        });

        $("#selectModule").select2({
            placeholder: 'Select Customer By',
            // minimumInputLength: 1,
            multiple: false,
        });

        // Brand
        $("#selectBrand").select2({
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
                    $options = [{ id: 'all', text: 'All'}];
                    response.forEach(function(value, key) {
                        $options.push(value);
                    })
                    return {
                        results: $options
                    };
                },
                cache: true
            },
            placeholder: 'Select brand',
            // minimumInputLength: 1,
            multiple: false,
        });

        // getCustomerClass
        $("#selectCustomerClass").select2({
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
                    $options = [{ id: 'all', text: 'All'}];
                    response.forEach(function(value, key) {
                        $options.push(value);
                    })
                    return {
                        results: $options
                    };
                },
                cache: true
            },
            placeholder: 'Select customer class',
            // minimumInputLength: 2,
            multiple: false,
        });

        // getSalesSpecialist
        $("#selectSalesSpecialist").select2({
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
                    $options = [{ id: 'all', text: 'All'}];
                    response.forEach(function(value, key) {
                        $options.push(value);
                    })
                    return {
                        results: $options
                    };
                },
                cache: true
            },
            placeholder: 'Select sales specialist',
            // minimumInputLength: 2,
            multiple: false,
        });

        // getTerritory
        $("#selectTerritory").select2({
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
                    $options = [{ id: 'all', text: 'All'}];
                    response.forEach(function(value, key) {
                        $options.push(value);
                    })
                    return {
                        results: $options
                    };
                },
                cache: true
            },
            placeholder: 'Select territory',
            // minimumInputLength: 2,
            multiple: false,
        });

        // getMarketSector
        $("#selectMarketSector").select2({
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
                    $options = [{ id: 'all', text: 'All'}];
                    response.forEach(function(value, key) {
                        $options.push(value);
                    })
                    return {
                        results: $options
                    };
                },
                cache: true
            },
            placeholder: 'Select market sector',
            // minimumInputLength: 2,
            multiple: false,
        });
    @endif
  })
</script>
@endpush
