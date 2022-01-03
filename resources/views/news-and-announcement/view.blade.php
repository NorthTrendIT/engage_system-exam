@extends('layouts.master')

@section('title','Notification')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Notification</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('news-and-announcement.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
        <!--end::Button-->
      </div>
      <!--end::Actions-->

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
                                    <tr>
                                    <th> <b>Notification Type:</b> </th>
                                    <td>{{ @$data->type ?? "" }}</td>
                                    </tr>

                                    <tr>
                                    <th> <b>Message:</b> </th>
                                    <td>{!! @$data->message ?? "" !!}</td>
                                    </tr>
                                    <tr>
                                    <th> <b>Module:</b> </th>

                                    <td>{{ ucwords(str_replace("_"," ",@$data->module)) ?? "" }}</td>
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

                    <div class="card-header border-0 pt-5 min-0">
                        <h5>Notification Details</h5>
                    </div>

                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="myDataTable">
                                        <thead>
                                            <th>No.</th>
                                            @if(@$data->module == 'customer')
                                            <th>Customer Name</th>
                                            @endif
                                            @if(@$data->module == 'sales_specialist')
                                            <th>Sales Specialist Name</th>
                                            @endif
                                            @if(@$data->module == 'role')
                                            <th>User Name</th>
                                            <th>Role</th>
                                            @endif
                                            <th>Is Seen</th>
                                            <th>
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
    </div>
  </div>
</div>
@endsection
@push('css')

@endpush

@push('js')

@endpush
