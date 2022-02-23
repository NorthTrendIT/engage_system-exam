@extends('layouts.master')

@section('title','Promotion')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
                <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion</h1>
            </div>
            <div class="d-flex align-items-center py-1">
                <a href="{{ route('promotion.index') }}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-bottom pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">{{ $data->title }}</h1>
                        </div>

                        <div class="card-body">

                            <div class="row mb-5">
                                <!-- Title -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control form-control-solid"  value="{{ $data->title }}" disabled="disabled">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Code</label>
                                        <input type="text" class="form-control form-control-solid"  value="{{ $data->code }}" disabled="disabled">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <!-- Description -->
                                <div class="col-md-12">
                                    <label>Description</label>
                                    <textarea class="form-control form-control-solid" disabled="disabled">{!! $data->description !!}</textarea>
                                </div>
                            </div>

                            <div class="row mb-5">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Business Unit</label>
                                        <input type="text" class="form-control form-control-solid"  value="{{ @$data->sap_connection->company_name }}" disabled="disabled">
                                    </div>
                                </div>

                                <!-- Promotion Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Promotion Type</label>
                                        <input type="text" class="form-control form-control-solid"  value="{{ @$data->promotion_type->title }}" disabled="disabled">
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-5">
                                <!-- Promotion Scope -->
                                <div class="col-md-6" id="scope_block">
                                    <div class="form-group">
                                        <label>Promotion Customers</label>
                                        @if($data->promotion_scope == 'A')
                                            <input type="text" class="form-control form-control-solid" value="All" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'C')
                                            <input type="text" class="form-control form-control-solid" value="Customer" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'CL')
                                            <input type="text" class="form-control form-control-solid" value="Class" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'T')
                                            <input type="text" class="form-control form-control-solid" value="Territory" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'SS')
                                            <input type="text" class="form-control form-control-solid" value="Sales Specialist" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'B')
                                            <input type="text" class="form-control form-control-solid" value="Brand" disabled="disabled">
                                        @endif

                                        @if($data->promotion_scope == 'MS')
                                            <input type="text" class="form-control form-control-solid" value="Market Sector" disabled="disabled">
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <!-- Promotion Start Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Promotion Start Date</label>
                                        <input type="text" class="form-control form-control-solid" value="{{ date('M d, Y',strtotime($data->promotion_start_date)) }}" disabled="disabled"/>
                                    </div>
                                </div>

                                <!-- Promotion End Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Promotion end Date</label>
                                        <input type="text" class="form-control form-control-solid" value="{{ date('M d, Y',strtotime($data->promotion_end_date)) }}" disabled="disabled"/>
                                    </div>
                                </div>
                            </div>

                             @if(isset($data->promo_image))
                                <div class="row mb-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Promotion Image</label><br>
                                            <a href="{{ get_valid_file_url('sitebucket/promotion',$data->promo_image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/promotion',$data->promo_image) }}" height="100" width="100"></a>
                                        </div>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-0  pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">
                                @if($data->promotion_scope == 'C')
                                    Customer
                                @endif

                                @if($data->promotion_scope == 'CL')
                                    Class
                                @endif

                                @if($data->promotion_scope == 'T')
                                    Territory
                                @endif

                                @if($data->promotion_scope == 'SS')
                                    Sales Specialist
                                @endif

                                @if($data->promotion_scope == 'B')
                                    Brand
                                @endif

                                @if($data->promotion_scope == 'MS')
                                    Market Sector
                                @endif List
                            </h1>
                        </div>

                        <div class="card-body">

                          <div class="row mb-5">
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


            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-0  pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">Claimed Customer's </h1>
                        </div>

                        <div class="card-body">

                          <div class="row mb-5">
                            <div class="col-md-12">
                              <div class="form-group">
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                   <!--begin::Table-->
                                   <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTableClaimed">
                                      <!--begin::Table head-->
                                      <thead>
                                        <tr>
                                          <th>No.</th>
                                          <th>Customer</th>
                                          <th>Date Time</th>
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

            <div class="row gy-5 g-xl-8">
                <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                    <div class="card card-xl-stretch mb-5 mb-xl-8">
                        <div class="card-header border-0  pt-5">
                            <h1 class="text-dark fw-bolder fs-3 my-1">Customer's Interest</h1>
                        </div>

                        <div class="card-body">

                          <div class="row mb-5">
                            <div class="col-md-12">
                              <div class="form-group">
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                   <!--begin::Table-->
                                   <table class="table table-row-gray-300 align-middle gs-0 gy-4 table-bordered display nowrap" id="myTableInterested">
                                      <!--begin::Table head-->
                                      <thead>
                                        <tr>
                                          <th>No.</th>
                                          <th>Customer</th>
                                          <th>Is Interested ?</th>
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

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('promotion.get-promotion-data') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                id : {{ $data->id }},
                scope : "{{ $data->promotion_scope }}",
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'name', name: 'name'},
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

    render_interested_table();

    function render_interested_table(){
      var table = $("#myTableInterested");
      table.DataTable().destroy();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('promotion.get-promotion-interest-data') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                id : {{ $data->id }},
              }
          },
          columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'customer', name: 'customer', orderable: false, searchable: false},
                {data: 'is_interested', name: 'is_interested', orderable: false, searchable: false},
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


    render_claimed_table();

    function render_claimed_table(){
      var table = $("#myTableClaimed");
      table.DataTable().destroy();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              'url': "{{ route('promotion.get-promotion-claimed-data') }}",
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                id : {{ $data->id }},
              }
          },
          columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'customer', name: 'customer', orderable: false, searchable: false},
                {data: 'date_time', name: 'date_time', orderable: false, searchable: false},
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
});
</script>
@endpush
