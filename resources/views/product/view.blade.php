@extends('layouts.master')

@section('title','Product')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Product</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('product.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <th> <b>Product Name:</b> </th>
                              <td>{{ @$data->item_name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Product Brand:</b> </th>
                              <td>{{ @$data->group->group_name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Product Code:</b> </th>
                              <td>{{ @$data->item_code ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Business Unit:</b> </th>
                              <td>{{ @$data->sap_connection->company_name ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Product Line:</b> </th>
                              <td>{{ @$data->u_item_line ?? "-" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Product Category:</b> </th>
                              <td>{{ @$data->u_tires ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Created Date:</b> </th>
                              <td>{{ date('M d, Y',strtotime(@$data->created_date)) }}</td>
                            </tr>
                            <tr>
                              <th> <b>Status:</b> </th>
                              <td><b class="{{ @$data->is_active ? "text-success" : "text-danger" }}">{{ @$data->is_active == true ? "Active" : "Inactive" }}</b></td>
                            </tr>

                            <tr>
                              <th> <b>Technical specifications:</b> </th>
                              <td>{!! @$data->technical_specifications ?? "" !!}</td>
                            </tr>
                            <tr>
                              <th> <b>Features:</b> </th>
                              <td>{!! @$data->product_features ?? "" !!}</td>
                            </tr>
                            <tr>
                              <th> <b>Advantages & Benefits:</b> </th>
                              <td>{!! @$data->product_benefits ?? "" !!}</td>
                            </tr>
                            <tr>
                              <th> <b>Sell Sheets:</b> </th>
                              <td>{!! @$data->product_sell_sheets ?? "" !!}</td>
                            </tr>

                            <tr>
                              <th> <b>Product Images:</b> </th>
                              <td>
                                @if(isset($data->product_images) && count($data->product_images) > 0)
                                  @foreach($data->product_images as $key => $image)

                                    @if($image->image && get_valid_file_url('sitebucket/products',$image->image))
                                      <a href="{{ get_valid_file_url('sitebucket/products',$image->image) }}" class="fancybox"><img src="{{ get_valid_file_url('sitebucket/products',$image->image) }}" height="100" width="100" class="mr-10"></a>
                                    @endif

                                  @endforeach
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