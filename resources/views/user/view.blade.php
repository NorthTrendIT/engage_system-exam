@extends('layouts.master')

@section('title','User')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">User</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('user.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <th> <b>First Name:</b> </th>
                              <td>{{ @$data->first_name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Last Name:</b> </th>
                              <td>{{ @$data->last_name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->email ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Department Name:</b> </th>
                              <td>{{ @$data->department->name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Role Name:</b> </th>
                              <td>{{ @$data->role->name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Parent User Name:</b> </th>
                              <td>{{ @$data->parent->first_name ?? "" }} {{ @$data->parent->last_name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Province:</b> </th>
                              <td>{{ @$data->province->name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>City:</b> </th>
                              <td>{{ @$data->city->name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y',strtotime(@$data->created_at)) }}</td>
                            </tr>
                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b class="{{ @$data->is_active ? "text-success" : "text-danger" }}">{{ @$data->is_active == true ? "Active" : "Inactive" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Profile Images:</b> </th>
                              <td>
                                
                              </td>
                            </tr>

                            <tr>
                              <th></th>
                              <td>
                                @if($data->profile && get_valid_file_url('sitebucket/users',$data->profile))
                                  <img src="{{ get_valid_file_url('sitebucket/users',$data->profile) }}" height="100" width="100" class="mr-10">
                                @endif
                              </td>
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