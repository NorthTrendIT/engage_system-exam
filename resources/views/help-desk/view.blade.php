@extends('layouts.master')

@section('title','Help Desk')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Help Desk</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('help-desk.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <th> <b>Name:</b> </th>
                              <td>{{ @$data->user->first_name ?? "" }} {{ @$data->user->last_name ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Email:</b> </th>
                              <td>{{ @$data->user->email ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y h:i:s A',strtotime(@$data->created_at)) }}</td>
                            </tr>

                            <tr>
                              <th> <b>Department:</b> </th>
                              <td>{{ @$data->department->name ?? "" }}</td>
                            </tr>
                            
                            <tr>
                              <th> <b>Urgency:</b> </th>
                              <td><b style="color: {{ @$data->urgency->color_code ??  "-" }}">{{ @$data->urgency->name ??  "-" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b style="color: {{ @$data->status->color_code ??  "-" }}">{{ @$data->status->name ??  "-" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Subject:</b> </th>
                              <td>{!! @$data->subject ?? "" !!}</td>
                            </tr>

                            <tr>
                              <th> <b>Message:</b> </th>
                              <td>{!! @$data->message ?? "" !!}</td>
                            </tr>

                            {{-- <tr>
                              <th> <b>Images:</b> </th>
                              <td>
                                @if(isset($data->files) && count($data->files) > 0)
                                  @foreach($data->files as $key => $image)

                                    @if($image->filename && get_valid_file_url('sitebucket/help-desk',$image->filename))
                                      <img src="{{ get_valid_file_url('sitebucket/help-desk',$image->filename) }}" height="100" width="100" class="mr-10">
                                    @endif

                                  @endforeach
                                @endif
                              </td>
                            </tr> --}}

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