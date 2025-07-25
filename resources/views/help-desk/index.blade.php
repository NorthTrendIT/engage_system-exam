@extends('layouts.master')

@section('title','Help Desk')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Help Desk</h1>
      </div>

      @if(!(userrole() == 1 || userdepartment() == 1))
      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('help-desk.create') }}" class="btn btn-sm btn-primary">Create</a>
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

                @if(userrole() == 1 || userdepartment() == 1)
                  <div class="col-md-3 mt-5">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_company" data-allow-clear="true" data-placeholder="Select business unit">
                      <option value=""></option>
                    </select>
                  </div>

                  <div class="col-md-3 mt-5 other_filter_div">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_sales_specialist" data-allow-clear="true" data-placeholder="Select sales specialist">
                      <option value=""></option>
                    </select>
                  </div>

                  <div class="col-md-3 mt-5 other_filter_div">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_customer_class" data-allow-clear="true" data-placeholder="Select customer class">
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
                
                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_urgency" data-control="select2" data-hide-search="true" data-placeholder="Select a urgency" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($urgencies as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_status" data-control="select2" data-hide-search="true" data-placeholder="Select a status" data-allow-clear="true">
                    <option value=""></option>
                    @foreach($status as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Select date range" name = "filter_date_range" id="kt_daterangepicker_1" readonly>
                    <span>
                    </span>
                  </div>
                </div>

                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" name="filter_type_of_customer_request" data-control="select2" data-hide-search="false" data-placeholder="Select a type of customer request" data-allow-clear="true">
                    <option value=""></option>
                    @foreach(\App\Models\HelpDesk::$type_of_customer_requests as $key => $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                  </select>
                </div>

                @if(userrole() == 1 || userdepartment() == 1)
                <div class="col-md-4 mt-5">
                  <select class="form-control form-control-lg form-control-solid" data-control="select2" data-hide-search="false" name="filter_user" data-allow-clear="true" data-placeholder="Select user">
                    <option value=""></option>
                  </select>
                </div>
                @endif

                <div class="col-md-3 mt-5">
                  <div class="input-icon">
                    <input type="text" class="form-control form-control-lg form-control-solid" placeholder="Search here..." name="filter_search" autocomplete="off">
                    <span>
                      <i class="flaticon2-search-1 text-muted"></i>
                    </span>
                  </div>
                </div>

                <div class="col-md-3 mt-5">
                  <a href="javascript:" class="btn btn-primary px-6 font-weight-bold search">Search</a>
                  <a href="javascript:" class="btn btn-light-dark font-weight-bold clear-search">Clear</a>
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
                              <th>Ticket No.</th>
                              <th>User Name</th>
                              <th>Type of Customer Request</th>
                              <th>Subject</th>
                              <th>Date</th>
                              <th>Status</th>
                              <th>Urgency</th>
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
      $filter_user = $('[name="filter_user"]').find('option:selected').val();
      $filter_urgency = $('[name="filter_urgency"]').find('option:selected').val();
      $filter_type_of_customer_request = $('[name="filter_type_of_customer_request"]').find('option:selected').val();
      
      $filter_company = $('[name="filter_company"]').find('option:selected').val();
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
            left: 3,  
            right: 0
          },
          order: [],
          ajax: {
              'url': "{{ route('help-desk.get-all') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_date_range : $filter_date_range,
                filter_status : $filter_status,
                filter_user : $filter_user,
                filter_urgency : $filter_urgency,
                filter_type_of_customer_request : $filter_type_of_customer_request,

                filter_company : $filter_company,
                filter_sales_specialist : $filter_sales_specialist,
                filter_market_sector : $filter_market_sector,
                filter_customer_class : $filter_customer_class,
                filter_territory : $filter_territory,
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable:false,searchable:false},
              {data: 'ticket_number', name: 'ticket_number'},
              {data: 'user', name: 'user'},
              {data: 'type_of_customer_request', name: 'type_of_customer_request'},
              {data: 'subject', name: 'subject'},
              {data: 'created_at', name: 'created_at'},
              {data: 'status', name: 'status'},
              {data: 'urgency', name: 'urgency'},
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
      $('input').val('');
      $('select').val('').trigger('change');
      render_table();
    })

    $(document).on('change', '[name="filter_company"]', function(event) {
      event.preventDefault();
      $('[name="filter_market_sector"]').val('').trigger('change');
      // $('[name="filter_territory"]').val('').trigger('change');
      $('[name="filter_sales_specialist"]').val('').trigger('change');
      $('[name="filter_customer_class"]').val('').trigger('change');
      
      if($(this).find('option:selected').val() != ""){
        $('.other_filter_div').show();
      }else{
        $('.other_filter_div').hide();
      }

    });

    $('[name="filter_user"]').select2({
      ajax: {
          url: "{{route('common.getUsers')}}",
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

  })
</script>
@endpush
