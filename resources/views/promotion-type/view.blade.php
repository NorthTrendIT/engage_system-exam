@extends('layouts.master')

@section('title','Promotion Type')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">Promotion Type</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('promotion-type.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <th> <b>Title:</b> </th>
                              <td>{{ @$data->title ?? "" }}</td>
                            </tr>

                            <tr>
                              <th> <b>Description:</b> </th>
                              <td>{{ @$data->description ?? "-" }}</td>
                            </tr>
                            
                            <tr>
                              <th> <b>Criteria:</b> </th>
                              <td>{{ get_promotion_type_criteria($data->scope) }}</td>
                            </tr>

                            @if(isset($data) && ($data->scope == "P" || $data->scope == "U"))
                            <tr>
                              <th> <b>Discount Percentage:</b> </th>
                              <td>{{ @$data->percentage ?? "" }} %</td>
                            </tr>
                            @endif

                            @if(isset($data) && $data->scope == "U")
                            <tr>
                              <th> <b>Discount Upto Amount:</b> </th>
                              <td>{{ @$data->fixed_price ?? "" }}</td>
                            </tr>
                            @endif

                            @if(isset($data) && $data->scope == "R")
                            <tr>
                              <th> <b>Minimum Percentage:</b> </th>
                              <td>{{ @$data->min_percentage ?? "" }} %</td>
                            </tr>

                            <tr>
                              <th> <b>Maximum Percentage:</b> </th>
                              <td>{{ @$data->max_percentage ?? "" }} %</td>
                            </tr>
                            @endif

                            <tr>
                              <th> <b>Has Fixed Quantity? :</b> </th>
                              <td>{{ @$data->is_fixed_quantity == true ? "Yes" : "No" }}</td>
                            </tr>

                            @if(isset($data) && $data->is_fixed_quantity == true)
                            <tr>
                              <th> <b>Fixed Quantity Option:</b> </th>
                              <td>{{ $data->is_total_fixed_quantity ? "Total Fixed Quantity" : "Fixed Quantity Per Product" }}</td>
                            </tr>
                            @endif

                            @if(isset($data) && $data->is_fixed_quantity == true && $data->is_total_fixed_quantity == true)
                            <tr>
                              <th> <b>Total Fixed Quantity:</b> </th>
                              <td>{{ @$data->total_fixed_quantity }}</td>
                            </tr>
                            @endif

                            <tr>
                              <th> <b>Number Of Delivery:</b> </th>
                              <td>{{ @$data->number_of_delivery ?? "" }}</td>
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
                              <th> <b>Business Unit:</b> </th>
                              <td>{{ @$data->sap_connection->company_name ?? "" }}</td>
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
              <h5>List Of Product</h5>
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
                            

                            @if(isset($data) && count($data->products) > 0)

                              <tr>
                                <th> <b>Brand</b> </th>

                                <th> <b>Product Option</b> </th>
                                
                                <th> <b>Product</b> </th>

                                @if($data->is_fixed_quantity == "1" && $data->is_total_fixed_quantity == "0")
                                <th> <b>Fixed Quantity </b> </th>
                                @endif

                                @if($data->scope == "R")
                                <th> <b>Discount</b> </th>
                                @endif

                              </tr>

                              @foreach($data->products as $p)
                                <tr>
                                  <td>{{ @$p->brand->group_name }}</td>

                                  <td>By {{ @$p->product_option }}
                                    @if( @$p->product_option == "category")
                                      {{ "(".$p->category.")" }}
                                    @elseif( @$p->product_option == "pattern")
                                      {{ "(".(@$p->product->u_pattern2_sap_value->value ?? @$p->pattern).")" }}
                                    @endif
                                  </td>

                                  <td>{{ @$p->product->item_name }}</td>

                                  @if($data->is_fixed_quantity == "1" && $data->is_total_fixed_quantity == "0")
                                  <td>{{ @$p->fixed_quantity }}</td>
                                  @endif

                                  @if($data->scope == "R")
                                  <td>{{ @$p->discount_percentage }} %</td>
                                  @endif

                                </tr>
                              @endforeach
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
    </div>
  </div>
</div>
@endsection

@push('css')

@endpush

@push('js')

@endpush