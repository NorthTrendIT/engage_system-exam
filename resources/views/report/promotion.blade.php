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
        @if(userrole() == 1)
        <a href="{{ route('role.chart') }}" class="btn btn-sm btn-primary mr-10">Role Chart</a>
        @endif

        <!-- <a href="{{ route('role.create') }}" class="btn btn-sm btn-primary">Create</a> -->
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
                    @foreach($company as $c)
                      <option value="{{ $c->id }}">{{ $c->company_name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Select Customer By -->
                <div class="col-md-3 mt-5">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectModule" data-hide-search="false" data-allow-clear="true" data-placeholder="Select Customer By" name="module">
                        <option value=""></option>
                        <option value="brand">By Brand</option>
                        <option value="customer_class">By Class</option>
                        <option value="sales_specialist">By Sales Specialist</option>
                        <option value="territory">By Territory</option>
                        <option value="market_sector">By Market Sector</option>
                    </select>
                </div>
                <!-- Brand -->
                <div class="col-md-3 mt-5 brand" style="display:none">
                    <select class="form-control form-control-lg form-control-solid" data-control="select2" id="selectBrand" data-hide-search="false" data-allow-clear="true" name="filter_brand" data-placeholder="Select Brand">
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
@endpush

@push('js')
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
  $(document).ready(function() {

    $('.search').on('click', function(){
      render_table();
      $('#myTable').DataTable().search($('#kt_datatable_search_query').val()).draw();
    })

    $('.clear-search').on('click', function(){
      $('#myTable').dataTable().fnFilter('');
      $('#kt_datatable_search_query').val('');
      $('[name="filter_parent"]').val('').trigger('change');
      render_table();
    })

  })
</script>
@endpush
