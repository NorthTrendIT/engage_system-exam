@extends('layouts.master')

@section('title','My Promotions')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  <div class="toolbar" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
      <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title me-3 mb-5 mb-lg-0">
        <h1 class="text-dark fw-bolder fs-3 my-1 mt-5">My Promotions</h1>
      </div>

      <!--begin::Actions-->
      <div class="d-flex align-items-center py-1">
        <!--begin::Button-->
        <a href="{{ route('customer-promotion.index') }}" class="btn btn-sm btn-primary sync-products">Back</a>
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
                              <td>{{ @$data->description ?? "" }}</td>
                            </tr>
                            <tr>
                              <th> <b>Discount:</b> </th>
                              <td>
                                <b class="text-success">
                                  @if(@$data->promotion_type->scope == "R")
                                    {{ @$data->promotion_type->min_percentage }} % - {{ @$data->promotion_type->max_percentage }} %
                                  @elseif(@$data->promotion_type->scope == "P")
                                    {{ @$data->promotion_type->percentage }} %
                                  @elseif(@$data->promotion_type->scope == "U")
                                    {{ @$data->promotion_type->percentage }} %, Maximum discount upto ₱ {{ @$data->promotion_type->fixed_price }}/- only. 
                                  @endif
                                  
                                </b>

                              </td>
                            </tr>

                            <tr>
                              <th> <b>Valid from:</b> </th>
                              <td>
                                <b class="text-danger">
                                  {{ date('M d, Y',strtotime(@$data->promotion_start_date)) }} to {{ date('M d, Y',strtotime(@$data->promotion_end_date)) }}
                                </b>
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

      <div class="row gy-5 g-xl-8 customer-product-list">
        <div class="col-xl-12 col-md-12 col-lg-12 col-sm-12">
          <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5 min-0">
              <h5>List of Products</h5>
            </div>
            <div class="card-body">

              <div class="row mb-5 mt-10" id="product_list_row">
                
              </div>

              <div class="row mt-10">
                <div class="col-md-12 d-flex justify-content-center" id="view_more_col">

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
<script>
$(document).ready(function() {
  getProductList();

  function getProductList($id = ""){
    $('#view_more_btn').remove();
    $.ajax({
      url: '{{ route('customer-promotion.get-all-product-list') }}',
      type: 'POST',
      dataType:'json',
      data: {
              promotion_type_id: '{{ @$data->promotion_type_id}}',
              id: $id,
              _token:'{{ csrf_token() }}',
            },
    })
    .done(function(data) {
      $('#product_list_row').append(data.output);
      $('#view_more_col').html(data.button);
    })
    .fail(function() {
      toast_error("error");
    });
  }



  $(document).on('click', '#view_more_btn', function(event) {
    event.preventDefault();
    $id = $(this).attr('data-id');
    getProductList($id);
  });
});
</script>
@endpush