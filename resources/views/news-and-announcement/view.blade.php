@extends('layouts.master')

@section('title','Notification')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Notification</h1>
      </div>

      <div class="d-flex align-items-center py-1">
        <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
      </div>

    </div>
  </div>

  <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
            <div class="card card-xl-stretch mb-5 mb-xl-8">

                <div class="card-header border-0 pt-5 min-0">
                <h5>Notification Details</h5>
                </div>

                <div class="card-body">
                    <div class="row mb-5">
                        <div class="col-md-12">
                        <div class="form-group">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                            <!--begin::Table-->
                            <table class="table table-bordered" id="myTable">
                                <!--begin::Table head-->
                                <thead>
                                    <tr>
                                    <th> <b>Title</b> </th>
                                    <td>{{ @$data->title ?? "" }}</td>
                                    </tr>
                                    @if(@Auth::user()->role_id == 1)
                                    <tr>
                                    <th> <b>Priority</b> </th>
                                    @if($data->is_important == 0)
                                    <td><button type="button" class="btn btn-light-info btn-sm">Normal</button></td>
                                    @elseif($data->is_important == 1)
                                    <td><button type="button" class="btn btn-light-danger btn-sm">Important</button></td>
                                    @endif
                                    </tr>
                                    @endif

                                    <tr>
                                    <th> <b>Notification Type</b> </th>
                                    <td>{{ getNotificationType($data->type) }}</td>
                                    </tr>

                                    <tr>
                                    <th> <b>Message</b> </th>
                                    <td>{!! @$data->message ?? "" !!}</td>
                                    </tr>

                                    @if(@Auth::user()->role_id == 1)
                                    <tr>
                                        <th> <b>Module:</b> </th>
                                        <td>{{ ucwords(str_replace("_"," ",@$data->module)) ?? "" }}</td>
                                    </tr>

                                    <tr>
                                        <th> <b>Start Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime($data->start_date)) }}</td>
                                    </tr>

                                    <tr>
                                        <th> <b>End Date:</b> </th>
                                        <td>{{ date('M d, Y',strtotime($data->end_date)) }}</td>
                                    </tr>

                                    <tr>
                                        <th> <b>Is Active:</b> </th>
                                        @if($data->is_active)
                                        <td><button type="button" class="btn btn-sm btn-light-success font-weight-bold">Active</button></td>
                                        @else
                                        <td><button type="button" class="btn btn-sm btn-light-danger font-weight-bold">Inactive</button></td>
                                        @endif
                                    </tr>
                                    @endif

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
        @if(@auth::user()->role_id == 1)
        <div class="row gy-5 g-xl-8">
            <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
                <div class="card card-xl-stretch mb-5 mb-xl-8">

                    <div class="card-header border-0 pt-5 min-0">
                        <h5>Users</h5>
                    </div>

                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="myDataTable">
                                        <thead>
                                            <th>No.</th>
                                            @if(@$data->module == 'customer' || @$data->module == 'customer_class' || @$data->module == 'territory' || @$data->module == 'market_sector')
                                            <th>Customer Name</th>
                                            @endif
                                            @if(@$data->module == 'sales_specialist')
                                            <th>Sales Specialist Name</th>
                                            @endif
                                            @if(@$data->module == 'market_sector')
                                            <th>Market Sector</th>
                                            @endif
                                            @if(@$data->module == 'customer' || @$data->module == 'sales_specialist' || @$data->module == 'role')
                                            <th>Role</th>
                                            @endif
                                            @if(@$data->module == 'customer_class')
                                            <th>Customer Class</th>
                                            @endif
                                            @if(@$data->module == 'territory')
                                            <th>Territory</th>
                                            @endif
                                            <th>Is Seen</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @endif
    </div>
  </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets')}}/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
@if(@Auth::user()->role_id == 1)
<script src="{{ asset('assets') }}/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="{{ asset('assets') }}/assets/plugins/custom/sweetalert2/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    render_table();

    function render_table(){
      var table = $("#myDataTable");
      table.DataTable().destroy();

      $filter_search = $('[name="filter_search"]').val();
      $filter_type = $('[name="filter_type"]').find('option:selected').val();

      table.DataTable({
          processing: true,
          serverSide: true,
          scrollX: true,
          order: [],
          ajax: {
              @if(@$data->module == 'role')
              'url': "{{ route('news-and-announcement.getAllRole') }}",
              @endif
              @if(@$data->module == 'customer')
              'url': "{{ route('news-and-announcement.getAllCustomer') }}",
              @endif
              @if(@$data->module == 'sales_specialist')
              'url': "{{ route('news-and-announcement.getAllSalesSpecialist') }}",
              @endif
              @if(@$data->module == 'customer_class')
              'url': "{{ route('news-and-announcement.getAllCustomerClass') }}",
              @endif
              @if(@$data->module == 'territory')
              'url': "{{ route('news-and-announcement.getAllTerritory') }}",
              @endif
              @if(@$data->module == 'market_sector')
              'url': "{{ route('news-and-announcement.getAllMarketSector') }}",
              @endif
              'type': 'POST',
              headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              data:{
                filter_search : $filter_search,
                filter_type : $filter_type,
                notification_id : "{{@$data->id}}",
              }
          },
          columns: [
              {data: 'DT_RowIndex', orderable: false},
              {data: 'user_name', name: 'user_name', orderable: false},
              @if(@$data->module == 'customer' || @$data->module == 'sales_specialist')
              {data: 'role', name: 'role', orderable: false},
              @endif
              @if(@$data->module == 'market_sector')
              {data: 'market_sector', name: 'market_sector', orderable: false},
              @endif
              @if(@$data->module == 'customer_class')
              {data: 'class_name', name: 'class_name', orderable: false},
              @endif
              @if(@$data->module == 'territory')
              {data: 'territory', name: 'territory', orderable: false},
              @endif
              {data: 'is_seen', name: 'is_seen', orderable: false},
          ],
          drawCallback:function(){
              $(function () {
                $('[data-toggle="tooltip"]').tooltip();
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
      $('[name="filter_status"]').val('').trigger('change');
      render_table();
    });
});
</script>
@endif
@endpush
