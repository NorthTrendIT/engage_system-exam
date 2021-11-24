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
        <a href="{{ route('customer.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
            <div class="card-header border-0 pt-5 min-0">
              <h5>View Details</h5>
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
                              <th> <b>Card Code:</b> </th>
                              <td>{{ @$data->card_code ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Card Name:</b> </th>
                              <td>{{ @$data->card_name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->email ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Contact Person Name:</b> </th>
                              <td>{{ @$data->contact_person ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Address:</b> </th>
                              <td>{{ @$data->address ?? "" }}</td>
                            </tr>
                            
                            <tr>
                              <th> <b>Credit Limit:</b> </th>
                              <td>{{ @$data->credit_limit ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Max Commitment:</b> </th>
                              <td>{{ @$data->max_commitment ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Federal Tax ID:</b> </th>
                              <td>{{ @$data->federal_tax_id ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Current Account Balance:</b> </th>
                              <td>{{ @$data->current_account_balance ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y',strtotime(@$data->created_date)) }}</td>
                            </tr>
                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b class="{{ @$data->is_active ? "text-success" : "text-danger" }}">{{ @$data->is_active == true ? "Active" : "Inactive" }}</b></td>
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

@endpush

@push('js')

@endpush