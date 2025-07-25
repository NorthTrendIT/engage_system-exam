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

  <div class="post d-flex flex-column-fluid detail-view-table" id="kt_post">
    <div id="kt_content_container" class="container-xxl">
      <div class="row gy-5 g-xl-8">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <div class="card-title">
                <h5>View Details</h5>
              </div>
              <div class="card-toolbar">

                @if( (is_null($data->created_by) && userrole() == 1) || (!is_null($data->created_by) && $data->created_by == Auth::id()) )
                  <a href="javascript:" data-href="{{ route('login-by-link', encryptValue($data->id."-".time())) }}" class="btn btn-icon btn-bg-light btn-active-color-success btn-sm copy_login_link" title="Copy Login Link"><i class="fa fa-link"></i></a>
                @endif

              </div>
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
                            @if($sap_connections != "")
                            <tr>
                              <th> <b>Business Units:</b> </th>
                              <td>{{ @$sap_connections ?? "-" }}</td>
                            </tr>
                            @endif
                            <tr>
                              <th> <b>First Name:</b> </th>
                              <td>{{ @$data->first_name ?? "-" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Last Name:</b> </th>
                              <td>{{ @$data->last_name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->email ?? "-" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Department Name:</b> </th>
                              <td>{{ @$data->department->name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Role Name:</b> </th>
                              <td>{{ @$data->role->name ?? "-" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Parent User Name:</b> </th>
                              <td>{{ @$data->parent->first_name ?? "-" }} {{ @$data->parent->last_name ?? "" }}</td>
                            </tr>

                            @if(!is_null($data->password_text) && userrole() == 1)
                            <tr>
                              <th> <b>Password:</b> </th>
                              <td>{{ @$data->password_text ?? "" }}</td>
                            </tr>
                            @endif

                            @if(!is_null($data->created_by))
                            <tr>
                              <th> <b>Created By:</b> </th>
                              <td>{{ @$data->created_by_user->first_name ?? "" }} {{ @$data->created_by_user->last_name ?? "" }}</td>
                            </tr>
                            @endif

                            {{-- <tr>
                              <th> <b>Province:</b> </th>
                              <td>{{ @$data->province->name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>City:</b> </th>
                              <td>{{ @$data->city->name ?? "" }}</td>
                            </tr> --}}

                            @if(@$data->role_id == 2)
                            <tr>
                              <th> <b>Sales Employee Code:</b> </th>
                              <td>{{ @$data->sales_employee_code ?? "-" }}</td>
                            </tr>
                            @endif

                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y',strtotime(@$data->created_at)) }}</td>
                            </tr>
                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b class="{{ @$data->is_active ? "text-success" : "text-danger" }}">{{ @$data->is_active == true ? "Active" : "Inactive" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Profile Image:</b> </th>

                              <td>
                                @if($data->profile && get_valid_file_url('sitebucket/users',$data->profile))
                                  <a href="{{ get_valid_file_url('sitebucket/users',$data->profile) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/users',$data->profile) }}" height="100" width="100" class="mr-10"></a>
                                @else
                                    -
                                @endif
                              </td>
                            </tr>

                            <tr>
                              <td colspan="2" class="text-center">
                                <h2>Hierarchy View</h2>
                              </td>
                            </tr>

                            <tr>
                              <td colspan="2">
                                <div id="chart-container"></div>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/css/jquery.orgchart.min.css" rel="stylesheet" />
<style>
  .orgchart {
    width: 100% !important;
  }
</style>
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/orgchart/2.1.3/js/jquery.orgchart.min.js"></script>

<script>
  var orgchart = $('#chart-container').orgchart({
    'data': {!! $tree !!},
    'nodeContent': 'title',
    'direction': 't2b'
  });

  $('#chart-container').append(orgchart);

  $(document).on('click', '.copy_login_link', function(event) {
    event.preventDefault();

    // Create a "hidden" input
    var aux = document.createElement("input");

    // Assign it the value of the specified element
    aux.setAttribute("value", $(this).attr('data-href'));

    // Append it to the body
    document.body.appendChild(aux);

    // Highlight its content
    aux.select();

    // Copy the highlighted text
    document.execCommand("copy");

    // Remove it from the body
    document.body.removeChild(aux);

    toast_success("Link copied successfully !");
  });
</script>
@endpush
